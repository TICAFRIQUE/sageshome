<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Residence;
use App\Models\ResidenceType;
use App\Models\ResidenceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResidenceController extends Controller
{
    public function index()
    {
        $residences = Residence::with(['residenceType', 'images', 'bookings'])
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.pages.sages-home.residences.index', compact('residences'));
    }

    public function create()
    {
        $residenceTypes = ResidenceType::active()->orderBy('sort_order')->get();
        
        return view('backend.pages.sages-home.residences.create', compact('residenceTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'full_description' => 'nullable|string',
            'residence_type_id' => 'required|exists:residence_types,id',
            'capacity' => 'required|integer|min:1|max:20',
            'price_per_night' => 'required|numeric|min:0',
            'amenities' => 'nullable|array',
            'address' => 'required|string',
            'ville' => 'required|string|max:255',
            'commune' => 'nullable|string|max:255',
            'google_maps_url' => 'nullable|url|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:1024', // 1MB max
            'primary_image_index' => 'nullable|integer|min:0'
        ]);

        $residence = Residence::create([
            'name' => $request->name,
            'description' => $request->description,
            'full_description' => $request->full_description,
            'residence_type_id' => $request->residence_type_id,
            'capacity' => $request->capacity,
            'price_per_night' => $request->price_per_night,
            'amenities' => $request->amenities ?? [],
            'address' => $request->address,
            'ville' => $request->ville,
            'commune' => $request->commune,
            'google_maps_url' => $request->google_maps_url,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_available' => $request->boolean('is_available', true),
            'is_featured' => $request->boolean('is_featured', false),
        ]);

        // Gérer les images
        $primaryIndex = (int) $request->input('primary_image_index', 0);
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('residences', 'public');
                
                $isPrimary = $index === $primaryIndex;
                
                ResidenceImage::create([
                    'residence_id' => $residence->id,
                    'image_path' => $path,
                    'alt_text' => $residence->name,
                    'is_primary' => $isPrimary,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.residences.index')
            ->with('success', 'Résidence créée avec succès.');
    }

    public function show(Residence $residence)
    {
        $residence->load(['images', 'bookings.user']);
        
        return view('backend.pages.sages-home.residences.show', compact('residence'));
    }

    public function edit(Residence $residence)
    {
        $residence->load(['residenceType', 'images']);
        $residenceTypes = ResidenceType::active()->orderBy('sort_order')->get();
        
        return view('backend.pages.sages-home.residences.edit', compact('residence', 'residenceTypes'));
    }

    public function update(Request $request, Residence $residence)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'full_description' => 'nullable|string',
            'residence_type_id' => 'required|exists:residence_types,id',
            'capacity' => 'required|integer|min:1|max:20',
            'price_per_night' => 'required|numeric|min:0',
            'amenities' => 'nullable|array',
            'address' => 'required|string',
            'ville' => 'required|string|max:255',
            'commune' => 'nullable|string|max:255',
            'google_maps_url' => 'nullable|url|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:1024', // 1MB max
        ]);

        $residence->update([
            'name' => $request->name,
            'description' => $request->description,
            'full_description' => $request->full_description,
            'residence_type_id' => $request->residence_type_id,
            'capacity' => $request->capacity,
            'price_per_night' => $request->price_per_night,
            'amenities' => $request->amenities ?? [],
            'address' => $request->address,
            'ville' => $request->ville,
            'commune' => $request->commune,
            'google_maps_url' => $request->google_maps_url,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_available' => $request->boolean('is_available'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        // Gérer les nouvelles images
        if ($request->hasFile('new_images')) {
            $currentMaxOrder = $residence->images()->max('sort_order') ?? -1;
            
            foreach ($request->file('new_images') as $index => $file) {
                $path = $file->store('residences', 'public');
                
                ResidenceImage::create([
                    'residence_id' => $residence->id,
                    'image_path' => $path,
                    'alt_text' => $residence->name,
                    'is_primary' => false,
                    'sort_order' => $currentMaxOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.residences.index')
            ->with('success', 'Résidence mise à jour avec succès.');
    }

    public function destroy(Residence $residence)
    {
        // Supprimer les images physiques
        foreach ($residence->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $residence->delete();

        return redirect()->route('admin.residences.index')
            ->with('success', 'Résidence supprimée avec succès.');
    }

    public function deleteImage($imageId)
    {
        $image = ResidenceImage::findOrFail($imageId);
        
        // Supprimer l'image physique
        Storage::disk('public')->delete($image->image_path);
        
        // Si c'était l'image principale, définir une nouvelle image principale
        if ($image->is_primary) {
            $newPrimary = ResidenceImage::where('residence_id', $image->residence_id)
                ->where('id', '!=', $image->id)
                ->orderBy('sort_order')
                ->first();
            
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }
        
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function setPrimaryImage($imageId)
    {
        $image = ResidenceImage::findOrFail($imageId);
        
        // Retirer le statut principal de toutes les autres images
        ResidenceImage::where('residence_id', $image->residence_id)
            ->update(['is_primary' => false]);
        
        // Définir cette image comme principale
        $image->update(['is_primary' => true]);

        return response()->json(['success' => true]);
    }
}