<?php

namespace App\Http\Controllers;

use App\Models\Residence;
use App\Models\ResidenceType;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer les résidences en vedette
        $featuredResidences = Residence::available()
            ->featured()
            ->with(['primaryImage', 'residenceType'])
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        // Types actifs avec le nombre de résidences disponibles et seulement
        // les 6 premières résidences de chaque type (limite par type, Laravel 11+)
        $allResidenceTypes = ResidenceType::active()->ordered()
            ->withCount(['residences as available_residences_count' => function ($query) {
                $query->available();
            }])
            ->with(['residences' => function ($query) {
                $query->available()->with(['primaryImage', 'residenceType'])->orderBy('sort_order')->limit(6);
            }])
            ->get()
            ->filter(fn ($type) => $type->available_residences_count > 0)
            ->values();

        $stats = [
            'residences' => Residence::available()->count(),
            'communes' => Residence::available()->whereNotNull('commune')->distinct()->count('commune'),
        ];

        return view('frontend.home', compact('featuredResidences', 'allResidenceTypes', 'stats'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1|max:20',
            'type' => 'nullable|exists:residence_types,id',
            'ville' => 'nullable|string',
            'commune' => 'nullable|string',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
        ]);

        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $guests = $request->guests;

        // Construire la requête
        $query = Residence::available()
            ->byCapacity($guests)
            ->with(['primaryImage', 'residenceType']);

        // Filtrer par type
        if ($request->type) {
            $query->byType($request->type);
        }

        // Filtrer par localisation
        if ($request->ville) {
            $query->byVille($request->ville);
        }

        if ($request->commune) {
            $query->byCommune($request->commune);
        }

        // Filtrer par prix
        if ($request->min_price || $request->max_price) {
            $query->priceRange($request->min_price, $request->max_price);
        }

        // Récupérer toutes les résidences qui correspondent aux critères de base
        $residences = $query->get();

        // Filtrer par disponibilité pour les dates demandées
        $availableResidences = $residences->filter(function ($residence) use ($checkIn, $checkOut) {
            return $residence->isAvailableForDates($checkIn, $checkOut);
        });

        // Calculer les prix pour chaque résidence
        $availableResidences = $availableResidences->map(function ($residence) use ($checkIn, $checkOut) {
            $priceInfo = $residence->calculateTotalPrice($checkIn, $checkOut);
            $residence->nights = $priceInfo['nights'];
            $residence->total_price = $priceInfo['total_price'];
            return $residence;
        });

        // Récupérer les types de résidences actifs pour les filtres
        $residenceTypes = ResidenceType::active()->ordered()->get();

        return view('frontend.search-results', compact(
            'availableResidences', 
            'checkIn', 
            'checkOut', 
            'guests',
            'residenceTypes'
        ));
    }
}