<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ResidenceType;
use Illuminate\Http\Request;

class ResidenceTypeController extends Controller
{
    public function index()
    {
        $residenceTypes = ResidenceType::withCount('residences')
            ->ordered()
            ->paginate(20);

        return view('backend.pages.sages-home.residence-types.index', compact('residenceTypes'));
    }

    public function create()
    {
        return view('backend.pages.sages-home.residence-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:residence_types',
            'description' => 'nullable|string',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $residenceType = ResidenceType::create([
            'name' => $request->name,
            'description' => $request->description,
            'min_capacity' => $request->min_capacity,
            'max_capacity' => $request->max_capacity,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.residence-types.index')
            ->with('success', 'Type de résidence créé avec succès.');
    }

    public function show(ResidenceType $residenceType)
    {
        $residenceType->load('residences');
        
        return view('backend.pages.sages-home.residence-types.show', compact('residenceType'));
    }

    public function edit(ResidenceType $residenceType)
    {
        return view('backend.pages.sages-home.residence-types.edit', compact('residenceType'));
    }

    public function update(Request $request, ResidenceType $residenceType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:residence_types,name,' . $residenceType->id,
            'description' => 'nullable|string',
            'min_capacity' => 'required|integer|min:1',
            'max_capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $residenceType->update([
            'name' => $request->name,
            'description' => $request->description,
            'min_capacity' => $request->min_capacity,
            'max_capacity' => $request->max_capacity,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.residence-types.index')
            ->with('success', 'Type de résidence modifié avec succès.');
    }

    public function destroy(ResidenceType $residenceType)
    {
        // Vérifier qu'aucune résidence n'utilise ce type
        if ($residenceType->residences()->count() > 0) {
            return redirect()->route('admin.residence-types.index')
                ->with('error', 'Impossible de supprimer ce type car il est utilisé par des résidences.');
        }

        $residenceType->delete();

        return redirect()->route('admin.residence-types.index')
            ->with('success', 'Type de résidence supprimé avec succès.');
    }
}
