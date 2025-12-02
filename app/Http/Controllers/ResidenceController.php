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

        // Filtres de localisation
        if ($request->ville) {
            $query->byVille($request->ville);
        }

        if ($request->commune) {
            $query->byCommune($request->commune);
        }

        $residences = $query->orderBy('sort_order')->paginate(12);

        // Récupérer les types de résidences actifs pour les filtres
        $residenceTypes = ResidenceType::active()->ordered()->get();

        // Récupérer TOUTES les villes et communes disponibles pour les filtres
        $allVillesCommunes = config('ville-commune');
        $availableVilles = collect(array_keys($allVillesCommunes));
        $availableCommunes = collect();
        
        // Si une ville est sélectionnée, récupérer ses communes
        if ($request->ville && isset($allVillesCommunes[$request->ville])) {
            $availableCommunes = collect($allVillesCommunes[$request->ville]);
        }

        return view('frontend.residences.index', compact(
            'residences', 
            'residenceTypes', 
            'availableVilles', 
            'availableCommunes',
            'allVillesCommunes'
        ));
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

    public function getUnavailableDates(Request $request, Residence $residence)
    {
        // Définir la période à vérifier (par défaut 6 mois)
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->addMonths(6)->format('Y-m-d'));

        $unavailableDates = [];

        // 1. Dates avec réservations confirmées ou en attente
        $bookings = $residence->bookings()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('check_in', '<=', $endDate)
                      ->where('check_out', '>=', $startDate);
            })
            ->whereIn('status', ['confirmed', 'pending'])
            ->get(['check_in', 'check_out']);

        foreach ($bookings as $booking) {
            $current = \Carbon\Carbon::parse(max($booking->check_in, $startDate));
            $end = \Carbon\Carbon::parse(min($booking->check_out, $endDate));
            
            while ($current->lte($end)) {
                $unavailableDates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        // 2. Dates marquées comme indisponibles dans le calendrier
        $calendarUnavailable = $residence->availabilityCalendar()
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_available', false)
            ->pluck('date')
            ->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        $unavailableDates = array_merge($unavailableDates, $calendarUnavailable);

        // Supprimer les doublons et retourner
        $unavailableDates = array_unique($unavailableDates);
        
        return response()->json([
            'unavailable_dates' => array_values($unavailableDates),
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
}