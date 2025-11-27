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

        // Récupérer toutes les résidences par catégorie avec les types dynamiques
        $residenceTypes = ResidenceType::active()->ordered()->with(['residences' => function($query) {
            $query->available()->with(['primaryImage', 'residenceType'])->orderBy('sort_order')->take(4);
        }])->get();

        // Pour la compatibilité avec la vue existante, créer un tableau avec les clés attendues
        $residencesByType = [
            'studio_1ch' => $residenceTypes->where('id', 2)->first()?->residences ?? collect(),
            'appartement_2ch' => $residenceTypes->where('id', 4)->first()?->residences ?? collect(),
            'appartement_3ch' => $residenceTypes->where('id', 5)->first()?->residences ?? collect(),
        ];

        // Récupérer tous les types avec toutes leurs résidences pour l'affichage dynamique
        $allResidenceTypes = ResidenceType::active()->ordered()->with(['residences' => function($query) {
            $query->available()->with(['primaryImage', 'residenceType'])->orderBy('sort_order');
        }])->get();

        return view('frontend.home', compact('featuredResidences', 'residencesByType', 'allResidenceTypes'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1|max:20',
            'type' => 'nullable|exists:residence_types,id',
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