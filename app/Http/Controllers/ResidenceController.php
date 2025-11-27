<?php

namespace App\Http\Controllers;

use App\Models\Residence;
use App\Models\ResidenceType;
use Illuminate\Http\Request;

class ResidenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Residence::available()->with(['primaryImage', 'residenceType']);

        // Filtres
        if ($request->type) {
            $query->byType($request->type);
        }

        if ($request->capacity) {
            $query->byCapacity($request->capacity);
        }

        if ($request->min_price || $request->max_price) {
            $query->priceRange($request->min_price, $request->max_price);
        }

        $residences = $query->orderBy('sort_order')->paginate(12);

        // Récupérer les types de résidences actifs pour les filtres
        $residenceTypes = ResidenceType::active()->ordered()->get();

        return view('frontend.residences.index', compact('residences', 'residenceTypes'));
    }

    public function show(Residence $residence)
    {
        $residence->load(['images', 'residenceType', 'availabilityCalendar' => function($query) {
            $query->where('date', '>=', now())
                  ->orderBy('date');
        }]);

        // Récupérer les résidences similaires
        $similarResidences = Residence::available()
            ->where('id', '!=', $residence->id)
            ->where('residence_type_id', $residence->residence_type_id)
            ->with(['primaryImage', 'residenceType'])
            ->take(3)
            ->get();

        return view('frontend.residences.show', compact('residence', 'similarResidences'));
    }

    public function checkAvailability(Request $request, Residence $residence)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $isAvailable = $residence->isAvailableForDates(
            $request->check_in,
            $request->check_out
        );

        $priceInfo = null;
        if ($isAvailable) {
            $priceInfo = $residence->calculateTotalPrice(
                $request->check_in,
                $request->check_out
            );
        }

        return response()->json([
            'available' => $isAvailable,
            'price_info' => $priceInfo,
        ]);
    }
}