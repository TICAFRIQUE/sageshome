@extends('layouts.app')

@section('title', $residence->name . ' - Sages Home')

@push('styles')
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .image-gallery {
            position: relative;
        }

        .main-image {
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
        }

        .thumbnail-gallery {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            overflow-x: auto;
        }

        .thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .thumbnail:hover,
        .thumbnail.active {
            opacity: 1;
        }

        .amenity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .amenity-item:hover {
            background: #e9ecef;
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .amenity-icon {
            width: 20px;
            margin-right: 12px;
            color: #007bff;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .amenity-text {
            font-weight: 500;
            color: #333;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 0.5rem;
        }

        .amenities-hidden {
            display: none;
        }

        .amenities-hidden.show {
            display: flex;
        }

        .btn-show-amenities {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-show-amenities:hover {
            background: linear-gradient(135deg, #0056b3, #007bff);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            color: white;
        }

        /* Styles pour les messages de dates indisponibles */
        .date-availability-info {
            font-size: 0.85rem;
            border-left: 4px solid #17a2b8;
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(255, 255, 255, 0.9));
        }

        .date-unavailable-warning {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 255, 255, 0.9));
            border-left: 4px solid #dc3545;
            animation: fadeInWarning 0.3s ease;
        }

        @keyframes fadeInWarning {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animation pour les champs avec dates invalides */
        .date-input-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            animation: shakeInput 0.5s ease;
        }

        @keyframes shakeInput {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        /* Styles pour l'indicateur de chargement des dates */
        .loading-dates {
            position: relative;
        }

        .loading-dates::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translateY(-50%) rotate(0deg);
            }

            100% {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        /* Styles personnalisés pour Flatpickr */
        .flatpickr-calendar {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid #e9ecef;
        }

        .flatpickr-calendar .flatpickr-month {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .flatpickr-calendar .flatpickr-month .flatpickr-prev-month,
        .flatpickr-calendar .flatpickr-month .flatpickr-next-month {
            color: white;
        }

        .flatpickr-calendar .flatpickr-month .flatpickr-prev-month:hover,
        .flatpickr-calendar .flatpickr-month .flatpickr-next-month:hover {
            color: #f8f9fa;
        }

        /* Dates indisponibles en rouge et non cliquables */
        .flatpickr-calendar .flatpickr-day.unavailable {
            background-color: #dc3545 !important;
            color: white !important;
            cursor: not-allowed !important;
            opacity: 0.7;
            position: relative;
            pointer-events: none !important;
        }

        .flatpickr-calendar .flatpickr-day.unavailable:hover {
            background-color: #c82333 !important;
            color: white !important;
            cursor: not-allowed !important;
        }

        .flatpickr-calendar .flatpickr-day.unavailable::after {
            content: '✕';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 10px;
            font-weight: bold;
            color: white;
            pointer-events: none;
        }

        /* Forcer l'état non cliquable même si d'autres styles tentent de l'écraser */
        .flatpickr-calendar .flatpickr-day.unavailable[aria-disabled="true"] {
            background-color: #dc3545 !important;
            color: white !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            opacity: 0.7 !important;
        }

        /* Empêcher les états hover et focus sur les dates indisponibles */
        .flatpickr-calendar .flatpickr-day.unavailable:hover,
        .flatpickr-calendar .flatpickr-day.unavailable:focus,
        .flatpickr-calendar .flatpickr-day.unavailable:active {
            background-color: #dc3545 !important;
            color: white !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Dates avec prix spéciaux */
        .flatpickr-calendar .flatpickr-day.special-price {
            background-color: #ffc107 !important;
            color: #212529 !important;
            font-weight: bold;
        }

        .flatpickr-calendar .flatpickr-day.special-price:hover {
            background-color: #e0a800 !important;
        }

        /* Style pour les dates sélectionnées */
        .flatpickr-calendar .flatpickr-day.selected {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
            color: white !important;
        }

        .flatpickr-calendar .flatpickr-day.today {
            border-color: #007bff;
            color: #007bff;
            font-weight: bold;
        }

        /* Responsive pour mobile */
        @media (max-width: 768px) {
            .flatpickr-calendar {
                width: 100% !important;
            }
        }

        /* Styles pour le champ voyageurs obligatoire */
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .form-label .text-danger {
            font-weight: bold;
        }

        .text-muted {
            font-size: 0.85rem;
        }

        /* Styles pour le bouton de réservation */
        .btn-book-now {
            background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-book-now:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .btn-book-now:active {
            transform: translateY(-1px);
        }

        .btn-book-now::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-book-now:hover::before {
            left: 100%;
        }

        /*style pour le bouton de connexion*/
        .btn-login {
            background: linear-gradient(135deg, #017052 0%, #0377d6 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .btn-login::

        /* Animation du bouton quand il apparaît */
        .btn-book-now.show {
            animation: slideInUp 0.5s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Icône dans le bouton */
        .btn-book-now i {
            margin-right: 8px;
            font-size: 1.2rem;
        }

        /* État désactivé */
        .btn-book-now:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-book-now:disabled::before {
            display: none;
        }

        /* Style pour les champs de saisie avec Flatpickr */
        .flatpickr-input[readonly] {
            background-color: white !important;
            cursor: pointer !important;
        }

        .flatpickr-input {
            font-family: inherit;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .flatpickr-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        @media (max-width: 768px) {
            .amenities-grid {
                grid-template-columns: 1fr;
            }

            .amenity-item {
                padding: 0.6rem 0.8rem;
                font-size: 0.9rem;
            }

            .amenity-icon {
                width: 18px;
                margin-right: 10px;
            }
        }

        .booking-card {
            position: sticky;
            top: 100px;
        }

        /* Bouton flottant mobile */
        .floating-book-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            padding: 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-floating-book {
            width: 100%;
            padding: 15px 20px;
            background: linear-gradient(135deg, var(--sage-green-dark, #2F4A33), var(--sage-green-secondary, #4A6B42));
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-floating-book:hover {
            background: linear-gradient(135deg, var(--sage-green-secondary, #4A6B42), var(--sage-green-dark, #2F4A33));
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .btn-floating-book::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-floating-book:hover::before {
            left: 100%;
        }

        .price-indicator {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--sage-gold-start, #F2D18A);
            text-align: right;
            line-height: 1.2;
        }

        .btn-floating-book i {
            font-size: 1.2rem;
            margin-right: 8px;
        }

        .btn-floating-book span {
            flex-grow: 1;
            text-align: left;
            margin-left: 8px;
        }

        /* Animation d'apparition */
        .floating-book-btn {
            animation: slideUpFade 0.5s ease-out;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(100px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .calendar-container {
            max-height: 300px;
            overflow-y: auto;
        }

        .availability-calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            font-size: 0.8rem;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .calendar-day.available {
            background-color: #e8f5e8;
            color: var(--green-dark);
        }

        .calendar-day.unavailable {
            background-color: #ffe6e6;
            color: #dc3545;
            cursor: not-allowed;
        }

        .calendar-day.selected {
            background-color: var(--gold-end);
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <!-- Breadcrumb -->
        {{-- <nav aria-label="breadcrumb" class="mb-4 py-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('residences.index') }}">Résidences</a></li>
                <li class="breadcrumb-item active">{{ $residence->name }}</li>
            </ol>
        </nav> --}}

        <div class="row g-4">
            <!-- Left Column - Images and Details -->
            <div class="col-lg-8">
                <!-- Image Gallery -->
                <div class="image-gallery mb-4">
                    @if ($residence->images->count() > 0)
                        <img id="mainImage" src="{{ $residence->images->first()->full_url }}"
                            class="img-fluid main-image w-100" alt="{{ $residence->name }}">

                        @if ($residence->images->count() > 1)
                            <div class="thumbnail-gallery">
                                @foreach ($residence->images as $index => $image)
                                    <img src="{{ $image->full_url }}" class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                                        alt="{{ $residence->name }}"
                                        onclick="changeMainImage('{{ $image->full_url }}', this)">
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="main-image bg-light d-flex align-items-center justify-content-center">
                            <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                </div>

                <!-- Details -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h1 class="text-green mb-2">{{ $residence->name }}</h1>
                        <h2 class="h5  my-3">
                            <i class="bi bi-geo-alt me-2"></i>
                            {{ $residence?->ville }} {{ $residence?->commune }}

                            <p class="text-muted my-3">
                                {{ $residence?->address }}
                            </p>
                        </h2>


                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <span class="badge bg-light text-dark fs-6">{{ $residence->type_display }}</span>
                            <span class="badge bg-light text-dark fs-6">
                                <i class="bi bi-people me-1"></i>{{ $residence->capacity }} voyageurs
                            </span>
                            @if ($residence->is_featured)
                                <span class="badge badge-gold fs-6">Résidence vedette</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4 text-md-end">
                        <div class="price-display">
                            <span class="text-gold fw-bold" style="font-size: 2rem;">
                                {{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA
                            </span>
                            <div class="text-muted">par nuit</div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-5">
                    <h3 class="text-green mb-3">Description</h3>
                    <p class="text-justify">{{ $residence->description }}</p>
                    @if ($residence->full_description)
                        <div id="fullDescription" class="collapse">
                            <p class="text-justify">{{ $residence->full_description }}</p>
                        </div>
                        <button class="btn btn-link p-0 text-primary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#fullDescription">
                            Voir plus <i class="bi bi-chevron-down"></i>
                        </button>
                    @endif
                </div>

                <!-- Amenities -->
                @php
                    $amenitiesList = $residence->amenities ?? [];
                    if (is_string($amenitiesList)) {
                        $amenitiesList = json_decode($amenitiesList, true) ?? [];
                    }
                    if (!is_array($amenitiesList)) {
                        $amenitiesList = [];
                    }

                    // Mapping des icônes FontAwesome
                    $amenityIcons = [
                        'wifi' => 'fas fa-wifi',
                        'piscine' => 'fas fa-swimming-pool',
                        'climatisation' => 'fas fa-snowflake',
                        'parking' => 'fas fa-car',
                        'cuisine' => 'fas fa-utensils',
                        'television' => 'fas fa-tv',
                        'internet' => 'fas fa-wifi',
                        'jardin' => 'fas fa-tree',
                        'balcon' => 'fas fa-home',
                        'terrasse' => 'fas fa-home',
                        'garage' => 'fas fa-warehouse',
                        'securite' => 'fas fa-shield-alt',
                        'ascenseur' => 'fas fa-arrow-up',
                        'spa' => 'fas fa-heart',
                        'gym' => 'fas fa-dumbbell',
                        'salle de sport' => 'fas fa-dumbbell',
                        'laverie' => 'fas fa-tshirt',
                        'lave linge' => 'fas fa-tshirt',
                        'lave vaisselle' => 'fas fa-utensils',
                        'micro onde' => 'fas fa-microwave',
                        'refrigerateur' => 'fas fa-ice-cream',
                        'four' => 'fas fa-fire',
                        'animaux' => 'fas fa-paw',
                        'non fumeur' => 'fas fa-ban',
                        'fumeur' => 'fas fa-smoking',
                        'enfants' => 'fas fa-child',
                        'baby' => 'fas fa-baby',
                        'bebe' => 'fas fa-baby',
                        'air' => 'fas fa-wind',
                        'chauffage' => 'fas fa-fire',
                        'douche' => 'fas fa-shower',
                        'baignoire' => 'fas fa-bath',
                    ];

                    function getAmenityIcon($amenity, $amenityIcons)
                    {
                        $amenityLower = strtolower($amenity);

                        foreach ($amenityIcons as $keyword => $icon) {
                            if (strpos($amenityLower, $keyword) !== false) {
                                return $icon;
                            }
                        }

                        return 'fas fa-check-circle';
                    }
                @endphp

                @if (!empty($amenitiesList) && count($amenitiesList) > 0)
                    <div class="mb-5">
                        <h3 class="text-green mb-3">
                            <i class="fas fa-cogs me-2"></i>Équipements
                        </h3>

                        <div class="amenities-grid" id="amenitiesGrid">
                            @foreach ($amenitiesList as $index => $amenity)
                                @php
                                    $cleanAmenity = ucfirst(str_replace('_', ' ', trim($amenity)));
                                    $amenityIcon = getAmenityIcon($amenity, $amenityIcons);
                                    $isHidden = $index >= 6; // Afficher seulement les 6 premiers
                                @endphp

                                <div class="amenity-item {{ $isHidden ? 'amenities-hidden' : '' }}">
                                    <i class="{{ $amenityIcon }} amenity-icon"></i>
                                    <span class="amenity-text">{{ $cleanAmenity }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if (count($amenitiesList) > 6)
                            <div class="text-center">
                                <button class="btn btn-show-amenities" onclick="toggleAllAmenities()"
                                    id="toggleAmenitiesBtn">
                                    <i class="fas fa-plus me-2"></i>
                                    Voir tous les équipements ({{ count($amenitiesList) }})
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Map -->
                {{-- @if ($residence->latitude && $residence->longitude)
                    <div class="mb-5">
                        <h3 class="text-green mb-3">Localisation</h3>
                        <div id="map" style="height: 300px; border-radius: 12px; background: #f8f9fa;">
                            <!-- Vous pouvez intégrer Google Maps ou OpenStreetMap ici -->
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <div class="text-center">
                                    <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">{{ $residence->address }}</p>
                                    <small class="text-muted">
                                        Lat: {{ $residence->latitude }}, Lng: {{ $residence->longitude }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif --}}
            </div>

            <!-- Right Column - Booking Card -->
            <div class="col-lg-4">
                <div class="booking-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-center text-green mb-4">Réserver maintenant</h4>
                            @guest
                                <p class="text-center text-muted mb-4">
                                    <i class="fas fa-lock me-2"></i>
                                    <a href="{{ route('login') }}">Connectez-vous</a> pour effectuer une réservation

                                </p>
                            @endguest

                            <form id="bookingForm">
                                @csrf
                                <input type="hidden" name="residence_id" value="{{ $residence->id }}">

                                <div class="mb-3">
                                    <label for="check_in" class="form-label fw-semibold">Date d'arrivée</label>
                                    <input type="date" class="form-control" id="check_in" name="check_in"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="check_out" class="form-label fw-semibold">Date de départ</label>
                                    <input type="date" class="form-control" id="check_out" name="check_out" required>
                                </div>

                                <div class="mb-3">
                                    <label for="guests" class="form-label fw-semibold">
                                        Voyageurs <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="guests" name="guests" required>
                                        <option selected disabled>Choisir</option>
                                        @for ($i = 1; $i <= $residence->capacity; $i++)
                                            <option value="{{ $i }}">
                                                {{ $i }} {{ $i === 1 ? 'voyageur' : 'voyageurs' }}
                                            </option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">Capacité maximale: {{ $residence->capacity }}
                                        voyageur{{ $residence->capacity > 1 ? 's' : '' }}</small>
                                </div>

                                <div id="priceCalculation" class="mb-3" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <span>Prix par nuit:</span>
                                                <span
                                                    id="pricePerNight">{{ number_format($residence->price_per_night, 0, ',', ' ') }}
                                                    FCFA</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Nombre de nuits:</span>
                                                <span id="nightsCount">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Sous-total:</span>
                                                <span id="subtotal">0 FCFA</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Taxes (10%):</span>
                                                <span id="taxes">0 FCFA</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Total:</span>
                                                <span id="totalPrice" class="text-gold">0 FCFA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="availabilityMessage" class="alert" style="display: none;"></div>

                                @guest

                                    <a href="{{ route('login') }}" class="btn btn-login w-100">
                                        Se connecter pour réserver
                                    </a>
                                @else
                                    <button type="button" id="checkAvailabilityBtn"
                                        class="btn btn-outline-primary w-100 mb-2">
                                        Vérifier la disponibilité
                                    </button>

                                    <button type="submit" id="bookNowBtn" class="btn btn-book-now w-100"
                                        style="display: none;" disabled>
                                        <i class="fas fa-calendar-check"></i>
                                        Réserver maintenant
                                        <span class="ms-2">
                                            <i class="fas fa-arrow-right"></i>
                                        </span>
                                    </button>
                                @endguest
                            </form>
                        </div>
                    </div>

                    <!-- Similar Residences -->
                    @if ($similarResidences->count() > 0)
                        <div class="card mt-4">
                            <div class="card-body">
                                <h5 class="card-title text-green mb-3">Résidences similaires</h5>

                                @foreach ($similarResidences as $similar)
                                    <div class="d-flex mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                                        @if ($similar->primaryImage)
                                            <img src="{{ $similar->primaryImage->full_url }}" class="me-3"
                                                style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                                alt="{{ $similar->name }}">
                                        @endif

                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('residences.show', $similar->slug) }}"
                                                    class="text-decoration-none text-dark">
                                                    {{ $similar->name }}
                                                </a>
                                            </h6>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-people me-1"></i>{{ $similar->capacity }} voyageurs
                                            </small>
                                            <span class="text-gold fw-bold small">
                                                {{ number_format($similar->price_per_night, 0, ',', ' ') }} FCFA/nuit
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton flottant mobile pour réservation -->
    <div class="floating-book-btn d-block d-lg-none">
        <button type="button" class="btn btn-floating-book" onclick="scrollToBooking()">
            <i class="fas fa-calendar-alt me-2"></i>
            <span>Réserver</span>
            <div class="price-indicator">
                {{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA/nuit
            </div>
        </button>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            const guestsInput = document.getElementById('guests');
            const checkAvailabilityBtn = document.getElementById('checkAvailabilityBtn');
            const bookNowBtn = document.getElementById('bookNowBtn');
            const bookingForm = document.getElementById('bookingForm');
            const priceCalculation = document.getElementById('priceCalculation');
            const availabilityMessage = document.getElementById('availabilityMessage');

            // Réinitialiser l'état du bouton lors du chargement de la page (gère le retour en arrière)
            if (bookNowBtn) {
                bookNowBtn.disabled = false;
                bookNowBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Réserver maintenant';
            }

            if (checkAvailabilityBtn) {
                checkAvailabilityBtn.disabled = false;
                checkAvailabilityBtn.innerHTML = 'Vérifier la disponibilité';
            }

            // Variables pour la gestion des dates indisponibles
            let unavailableDates = [];
            let checkInPicker = null;
            let checkOutPicker = null;
            let isLoadingUnavailableDates = false;

            // Configuration Flatpickr de base
            const baseConfig = {
                locale: 'fr',
                dateFormat: 'd/m/Y',
                altInput: true,
                altFormat: 'd/m/Y',
                minDate: 'today',
                allowInput: true,
                clickOpens: true,
                onReady: function(selectedDates, dateStr, instance) {
                    // Appliquer les styles personnalisés après le rendu
                    setTimeout(() => applyUnavailableDatesStyles(instance), 150);
                },
                onOpen: function(selectedDates, dateStr, instance) {
                    // Réappliquer les styles à chaque ouverture
                    setTimeout(() => applyUnavailableDatesStyles(instance), 50);
                },
                onMonthChange: function(selectedDates, dateStr, instance) {
                    // Réappliquer les styles quand on change de mois
                    setTimeout(() => applyUnavailableDatesStyles(instance), 100);
                },
                onYearChange: function(selectedDates, dateStr, instance) {
                    // Réappliquer les styles quand on change d'année
                    setTimeout(() => applyUnavailableDatesStyles(instance), 100);
                },
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    // Appliquer les styles directement lors de la création de chaque jour
                    const dateStr = fp.formatDate(dayElem.dateObj, 'Y-m-d');
                    if (unavailableDates.includes(dateStr)) {
                        dayElem.classList.add('unavailable');
                        dayElem.title = 'Date indisponible - Réservée ou en maintenance';
                        dayElem.style.pointerEvents = 'none';
                    }
                },
                disable: []
            };

            // Auto-populate dates from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const initialCheckIn = urlParams.get('check_in');
            const initialCheckOut = urlParams.get('check_out');

            // S'assurer qu'une valeur par défaut est définie pour les voyageurs
            if (urlParams.get('guests')) {
                guestsInput.value = urlParams.get('guests');
            } else if (!guestsInput.value) {
                guestsInput.value = '1'; // Valeur par défaut si aucune n'est définie
            }

            // Charger les dates indisponibles et initialiser les calendriers
            loadUnavailableDates().then(() => {
                initializeDatePickers();
            });

            // Fonction pour initialiser les sélecteurs de date Flatpickr
            function initializeDatePickers() {
                // Configuration pour la date d'arrivée
                const checkInConfig = {
                    ...baseConfig,
                    disable: unavailableDates.map(date => new Date(date)),
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            // Mettre à jour la date minimale pour le départ
                            const nextDay = new Date(selectedDates[0]);
                            nextDay.setDate(nextDay.getDate() + 1);

                            if (checkOutPicker) {
                                checkOutPicker.set('minDate', nextDay);

                                // Si la date de départ est antérieure, la réinitialiser
                                if (checkOutPicker.selectedDates.length > 0 &&
                                    checkOutPicker.selectedDates[0] <= selectedDates[0]) {
                                    checkOutPicker.clear();
                                }
                            }

                            // Mettre à jour la valeur du champ caché pour le formulaire
                            checkInInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                        }

                        // Vérifier automatiquement la disponibilité
                        autoCheckAvailability();
                    }
                };

                // Configuration pour la date de départ
                const checkOutConfig = {
                    ...baseConfig,
                    disable: unavailableDates.map(date => new Date(date)),
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            // Mettre à jour la valeur du champ caché pour le formulaire
                            checkOutInput.value = instance.formatDate(selectedDates[0], 'Y-m-d');
                        }

                        // Vérifier automatiquement la disponibilité
                        autoCheckAvailability();
                    }
                };

                // Initialiser Flatpickr
                checkInPicker = flatpickr(checkInInput, checkInConfig);
                checkOutPicker = flatpickr(checkOutInput, checkOutConfig);

                // Définir les valeurs initiales si présentes dans l'URL
                if (initialCheckIn) {
                    checkInPicker.setDate(initialCheckIn, true);
                    checkInInput.value = initialCheckIn; // Assurer que la valeur du formulaire est correcte
                }
                if (initialCheckOut) {
                    checkOutPicker.setDate(initialCheckOut, true);
                    checkOutInput.value = initialCheckOut; // Assurer que la valeur du formulaire est correcte
                }

                // Déclencher la vérification automatique si toutes les valeurs sont déjà présentes
                // (notamment depuis les paramètres URL ou valeurs par défaut)
                setTimeout(() => {
                    autoCheckAvailability();
                }, 500); // Petit délai pour s'assurer que tout est initialisé
            }

            // Fonction pour vérifier automatiquement la disponibilité
            function autoCheckAvailability() {
                const checkInValue = checkInPicker ? checkInPicker.selectedDates[0] : null;
                const checkOutValue = checkOutPicker ? checkOutPicker.selectedDates[0] : null;
                const guestsValue = guestsInput.value;

                // Vérifier quels champs sont manquants
                const missingFields = [];
                if (!checkInValue) missingFields.push('date d\'arrivée');
                if (!checkOutValue) missingFields.push('date de départ');
                if (!guestsValue || guestsValue === '' || guestsValue === '0' || guestsValue === 'Choisir') {
                    missingFields.push('nombre de voyageurs');
                }

                // Si des champs sont manquants, afficher un message informatif
                if (missingFields.length > 0) {
                    if (missingFields.length === 3) {
                        showMessage(
                            '📋 Veuillez remplir tous les champs : date d\'arrivée, date de départ et nombre de voyageurs.',
                            'info');
                    } else {
                        const missingText = missingFields.length === 1 ?
                            missingFields[0] :
                            missingFields.slice(0, -1).join(', ') + ' et ' + missingFields.slice(-1);
                        showMessage(`📝 Il manque encore : ${missingText}.`, 'warning');
                    }
                    hideAvailabilityResults();
                    return;
                }

                // Si toutes les informations sont présentes
                if (checkInValue && checkOutValue && guestsValue) {
                    // Convertir les dates en format string
                    const checkInStr = checkInInput.value || (checkInPicker ? checkInPicker.formatDate(checkInValue,
                        'Y-m-d') : '');
                    const checkOutStr = checkOutInput.value || (checkOutPicker ? checkOutPicker.formatDate(
                        checkOutValue, 'Y-m-d') : '');

                    // Vérifier s'il y a des dates indisponibles dans la période sélectionnée
                    if (hasUnavailableDatesInRange(checkInStr, checkOutStr)) {
                        return; // Arrêter si des dates sont indisponibles
                    }

                    // Cacher le bouton de vérification et afficher un indicateur de chargement
                    if (checkAvailabilityBtn) {
                        checkAvailabilityBtn.style.display = 'none';
                    }

                    // Afficher un message de vérification en cours
                    showMessage('Vérification automatique de la disponibilité...', 'info');

                    // Effectuer la vérification automatiquement
                    fetch(`{{ route('residences.check-availability', $residence) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                check_in: checkInStr,
                                check_out: checkOutStr
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.available) {
                                showAvailability(data.price_info);
                                showMessage('Résidence disponible ! Vous pouvez procéder à la réservation.',
                                    'success');

                                // Afficher directement le bouton de réservation avec animation
                                if (bookNowBtn) {
                                    bookNowBtn.style.display = 'block';
                                    bookNowBtn.disabled = false;
                                    bookNowBtn.classList.add('show');

                                    // Retirer la classe d'animation après l'animation
                                    setTimeout(() => {
                                        bookNowBtn.classList.remove('show');
                                    }, 500);
                                }
                            } else {
                                showMessage('Désolé, cette résidence n\'est pas disponible pour ces dates.',
                                    'danger');

                                // Réafficher le bouton de vérification
                                if (checkAvailabilityBtn) {
                                    checkAvailabilityBtn.style.display = 'block';
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage(
                                'Erreur lors de la vérification automatique. Utilisez le bouton de vérification manuelle.',
                                'warning');

                            // Réafficher le bouton de vérification en cas d'erreur
                            if (checkAvailabilityBtn) {
                                checkAvailabilityBtn.style.display = 'block';
                            }
                        });
                } else {
                    // Si toutes les informations ne sont pas présentes, masquer les résultats
                    hideAvailabilityResults();

                    // Réafficher le bouton de vérification
                    if (checkAvailabilityBtn) {
                        checkAvailabilityBtn.style.display = 'block';
                    }
                }
            }

            // Fonction pour appliquer les styles aux dates indisponibles de façon plus robuste
            function applyUnavailableDatesStyles(instance) {
                if (!instance.calendarContainer) return;

                // Utiliser requestAnimationFrame pour s'assurer que le DOM est prêt
                requestAnimationFrame(() => {
                    const calendar = instance.calendarContainer;
                    const days = calendar.querySelectorAll('.flatpickr-day');

                    days.forEach(day => {
                        // Réinitialiser d'abord les styles
                        day.classList.remove('unavailable');
                        day.style.pointerEvents = '';
                        day.title = '';

                        if (day.dateObj) {
                            const dateStr = instance.formatDate(day.dateObj, 'Y-m-d');

                            // Marquer les dates indisponibles
                            if (unavailableDates.includes(dateStr)) {
                                day.classList.add('unavailable');
                                day.title = 'Date indisponible - Réservée ou en maintenance';
                                day.style.pointerEvents = 'none';
                                day.setAttribute('aria-disabled', 'true');

                                // Empêcher la sélection si on clique dessus
                                day.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return false;
                                }, true);

                                // Empêcher le hover
                                day.addEventListener('mouseenter', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                }, true);
                            }
                        }
                    });

                    // Observer pour surveiller les changements dans le calendrier
                    if (!calendar.hasObserver) {
                        const observer = new MutationObserver(() => {
                            // Réappliquer les styles si le calendrier est modifié
                            setTimeout(() => applyUnavailableDatesStyles(instance), 10);
                        });

                        observer.observe(calendar, {
                            childList: true,
                            subtree: true,
                            attributes: true,
                            attributeFilter: ['class']
                        });

                        calendar.hasObserver = true;
                    }
                });
            }

            // Mise à jour des gestionnaires d'événements pour les autres éléments
            guestsInput.addEventListener('change', function() {
                // Validation du nombre de voyageurs
                const guestsValue = parseInt(this.value);
                const maxCapacity = {{ $residence->capacity }};

                if (!guestsValue || guestsValue < 1) {
                    this.classList.add('is-invalid');
                    showMessage('Veuillez sélectionner au moins 1 voyageur.', 'warning');
                    hideAvailabilityResults();
                    return;
                } else if (guestsValue > maxCapacity) {
                    this.classList.add('is-invalid');
                    showMessage(`Le nombre maximum de voyageurs pour cette résidence est ${maxCapacity}.`,
                        'warning');
                    this.value = maxCapacity;
                    return;
                } else {
                    this.classList.remove('is-invalid');
                }

                // Déclencher la vérification automatique quand le nombre d'invités change
                autoCheckAvailability();
            });

            // Check availability
            if (checkAvailabilityBtn) {
                checkAvailabilityBtn.addEventListener('click', function() {
                    const checkInValue = checkInPicker ? checkInPicker.selectedDates[0] : null;
                    const checkOutValue = checkOutPicker ? checkOutPicker.selectedDates[0] : null;
                    const guestsValue = guestsInput.value;

                    // Vérifier que tous les champs obligatoires sont remplis
                    const missingFields = [];
                    if (!checkInValue) missingFields.push('date d\'arrivée');
                    if (!checkOutValue) missingFields.push('date de départ');
                    if (!guestsValue || guestsValue === '' || guestsValue === 'Choisir') {
                        missingFields.push('nombre de voyageurs');
                    }

                    if (missingFields.length > 0) {
                        const missingText = missingFields.length === 1 ?
                            missingFields[0] :
                            missingFields.slice(0, -1).join(', ') + ' et ' + missingFields.slice(-1);
                        showMessage(`❌ Veuillez remplir : ${missingText}.`, 'warning');
                        return;
                    }

                    // Utiliser les valeurs du formulaire (format Y-m-d)
                    const checkInStr = checkInInput.value;
                    const checkOutStr = checkOutInput.value;

                    // Vérifier s'il y a des dates indisponibles dans la période sélectionnée
                    if (hasUnavailableDatesInRange(checkInStr, checkOutStr)) {
                        return; // Le message d'erreur est déjà affiché par hasUnavailableDatesInRange
                    }

                    this.disabled = true;
                    this.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Vérification...';

                    fetch(`{{ route('residences.check-availability', $residence) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                check_in: checkInStr,
                                check_out: checkOutStr
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.available) {
                                showAvailability(data.price_info);
                                showMessage('Cette résidence est disponible pour vos dates !',
                                    'success');
                                bookNowBtn.style.display = 'block';
                                bookNowBtn.disabled = false;
                            } else {
                                showMessage(
                                    'Désolé, cette résidence n\'est pas disponible pour ces dates.',
                                    'danger');
                                bookNowBtn.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage('Une erreur est survenue lors de la vérification.', 'danger');
                        })
                        .finally(() => {
                            this.disabled = false;
                            this.innerHTML = 'Vérifier la disponibilité';
                        });
                });
            }

            // Book now form submission
            if (bookingForm) {
                bookingForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const checkInValue = checkInPicker ? checkInPicker.selectedDates[0] : null;
                    const checkOutValue = checkOutPicker ? checkOutPicker.selectedDates[0] : null;

                    if (!checkInValue || !checkOutValue || !guestsInput.value) {
                        showMessage('Veuillez remplir tous les champs.', 'warning');
                        return;
                    }

                    // Afficher l'état de chargement sur le bouton
                    if (bookNowBtn) {
                        bookNowBtn.disabled = true;
                        bookNowBtn.innerHTML =
                            '<i class="fas fa-spinner fa-spin"></i> Redirection en cours...';
                    }

                    // Utiliser les valeurs du formulaire (format Y-m-d)
                    const checkInStr = checkInInput.value;
                    const checkOutStr = checkOutInput.value;

                    // Redirect to booking creation page
                    const params = new URLSearchParams({
                        check_in: checkInStr,
                        check_out: checkOutStr,
                        guests: guestsInput.value
                    });

                    window.location.href =
                        `{{ route('booking.create', $residence) }}?${params.toString()}`;
                });
            }

            function showAvailability(priceInfo) {
                document.getElementById('nightsCount').textContent = priceInfo.nights;
                document.getElementById('subtotal').textContent = formatPrice(priceInfo.total_price);

                const taxes = priceInfo.total_price * 0;
                document.getElementById('taxes').textContent = formatPrice(taxes);

                const total = priceInfo.total_price + taxes;
                document.getElementById('totalPrice').textContent = formatPrice(total);

                priceCalculation.style.display = 'block';
            }

            function hideAvailabilityResults() {
                priceCalculation.style.display = 'none';
                availabilityMessage.style.display = 'none';
                if (bookNowBtn) {
                    bookNowBtn.style.display = 'none';
                    bookNowBtn.disabled = true;
                    bookNowBtn.classList.remove('show'); // Supprimer l'animation si présente
                }
                // Réafficher le bouton de vérification quand on cache les résultats
                if (checkAvailabilityBtn) {
                    checkAvailabilityBtn.style.display = 'block';
                }
            }

            function showMessage(message, type) {
                availabilityMessage.className = `alert alert-${type}`;
                availabilityMessage.textContent = message;
                availabilityMessage.style.display = 'block';
            }

            function formatPrice(price) {
                return new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
            }

            // Fonction pour charger les dates indisponibles
            function loadUnavailableDates() {
                if (isLoadingUnavailableDates) return Promise.resolve();

                isLoadingUnavailableDates = true;

                return fetch(`{{ route('residences.unavailable-dates', $residence) }}`)
                    .then(response => response.json())
                    .then(data => {
                        unavailableDates = data.unavailable_dates || [];
                        console.log('Dates indisponibles chargées:', unavailableDates);

                        // Afficher les informations sur les dates indisponibles
                        displayUnavailableDatesInfo();

                        return unavailableDates;
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des dates indisponibles:', error);
                        showMessage(
                            'Erreur lors du chargement des disponibilités. Certaines dates peuvent ne pas être à jour.',
                            'warning');
                        return [];
                    })
                    .finally(() => {
                        isLoadingUnavailableDates = false;
                    });
            }

            // Fonction pour afficher les informations sur les dates indisponibles
            function displayUnavailableDatesInfo() {
                if (unavailableDates.length > 0) {
                    const dateInfo = document.createElement('div');
                    dateInfo.className = 'alert alert-info date-availability-info mt-2';

                    const unavailableCount = unavailableDates.length;
                    const nextUnavailable = getNextUnavailableDate();

                    let infoText = `<i class="fas fa-info-circle me-2"></i><strong>Information:</strong> `;

                    if (nextUnavailable) {
                        infoText +=
                            `Prochaine date indisponible: <strong>${formatDateForDisplay(nextUnavailable)}</strong>. `;
                    }

                    infoText +=
                        `Au total, ${unavailableCount} date${unavailableCount > 1 ? 's sont' : ' est'} actuellement indisponible${unavailableCount > 1 ? 's' : ''} (réservations ou maintenance). Les dates en <span style="color: #dc3545; font-weight: bold;">rouge ✕</span> sont indisponibles.`;

                    dateInfo.innerHTML = `<small>${infoText}</small>`;

                    // Ajouter l'info après le champ check_out s'il n'existe pas déjà
                    const existingInfo = document.querySelector('.date-availability-info');
                    if (!existingInfo) {
                        checkOutInput.parentNode.appendChild(dateInfo);
                    }
                }
            }

            // Fonction pour vérifier si une date est indisponible
            function isDateUnavailable(dateString) {
                if (!dateString) return false;
                return unavailableDates.includes(dateString);
            }

            // Fonction pour appliquer les restrictions visuelles sur les champs de date
            function applyDateRestrictions() {
                if (unavailableDates.length > 0) {
                    // Créer un message informatif sur les dates indisponibles
                    const dateInfo = document.createElement('div');
                    dateInfo.className = 'alert alert-info date-availability-info mt-2';

                    const unavailableCount = unavailableDates.length;
                    const nextUnavailable = getNextUnavailableDate();

                    let infoText = `<i class="fas fa-info-circle me-2"></i><strong>Information:</strong> `;

                    if (nextUnavailable) {
                        infoText +=
                            `Prochaine date indisponible: <strong>${formatDateForDisplay(nextUnavailable)}</strong>. `;
                    }

                    infoText +=
                        `Au total, ${unavailableCount} date${unavailableCount > 1 ? 's sont' : ' est'} actuellement indisponible${unavailableCount > 1 ? 's' : ''} (réservations ou maintenance).`;

                    dateInfo.innerHTML = `<small>${infoText}</small>`;

                    // Ajouter l'info après le champ check_out s'il n'existe pas déjà
                    const existingInfo = document.querySelector('.date-availability-info');
                    if (!existingInfo) {
                        checkOutInput.parentNode.appendChild(dateInfo);
                    }
                }
            }

            // Fonction pour obtenir la prochaine date indisponible
            function getNextUnavailableDate() {
                const today = new Date().toISOString().split('T')[0];
                return unavailableDates
                    .filter(date => date >= today)
                    .sort()[0];
            }

            // Fonction pour formater une date pour l'affichage
            function formatDateForDisplay(dateString) {
                const date = new Date(dateString + 'T00:00:00');
                return date.toLocaleDateString('fr-FR', {
                    weekday: 'short',
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            // Fonction pour vérifier si une période contient des dates indisponibles
            function hasUnavailableDatesInRange(startDate, endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const current = new Date(start);

                const unavailableDatesInRange = [];

                while (current < end) {
                    const dateString = current.toISOString().split('T')[0];
                    if (isDateUnavailable(dateString)) {
                        unavailableDatesInRange.push(dateString);
                    }
                    current.setDate(current.getDate() + 1);
                }

                // Afficher les dates problématiques si trouvées
                if (unavailableDatesInRange.length > 0) {
                    console.log('Dates indisponibles dans la période:', unavailableDatesInRange);
                    const formattedDates = unavailableDatesInRange
                        .slice(0, 3)
                        .map(date => formatDateForDisplay(date))
                        .join(', ');

                    const moreText = unavailableDatesInRange.length > 3 ?
                        ` et ${unavailableDatesInRange.length - 3} autre${unavailableDatesInRange.length - 3 > 1 ? 's' : ''}...` :
                        '';

                    showMessage(
                        `Dates indisponibles dans votre sélection: ${formattedDates}${moreText}`,
                        'danger'
                    );
                }

                return unavailableDatesInRange.length > 0;
            }

            // Fonction améliorée pour gérer les dates invalides avec animation
            function handleInvalidDate(input, message) {
                input.classList.add('date-input-invalid');
                showMessage(message, 'warning');

                // Retirer la classe après l'animation
                setTimeout(() => {
                    input.classList.remove('date-input-invalid');
                }, 500);

                input.value = '';
                input.focus();
            }
        });

        function changeMainImage(src, thumbnail) {
            document.getElementById('mainImage').src = src;

            // Remove active class from all thumbnails
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));

            // Add active class to clicked thumbnail
            thumbnail.classList.add('active');
        }

        function toggleAllAmenities() {
            const hiddenAmenities = document.querySelectorAll('.amenities-hidden');
            const btn = document.getElementById('toggleAmenitiesBtn');
            const isShowing = hiddenAmenities[0] && hiddenAmenities[0].classList.contains('show');

            hiddenAmenities.forEach(amenity => {
                if (isShowing) {
                    amenity.classList.remove('show');
                } else {
                    amenity.classList.add('show');
                }
            });

            if (isShowing) {
                btn.innerHTML = '<i class="fas fa-plus me-2"></i>Voir tous les équipements';
            } else {
                btn.innerHTML = '<i class="fas fa-minus me-2"></i>Voir moins d\'équipements';
            }
        }

        // Fonction pour faire défiler vers la section de réservation (bouton flottant mobile)
        function scrollToBooking() {
            const bookingCard = document.querySelector('.booking-card');
            if (bookingCard) {
                const yOffset = -130; // Offset pour la navbar fixe
                const y = bookingCard.getBoundingClientRect().top + window.pageYOffset + yOffset;

                window.scrollTo({
                    top: y,
                    behavior: 'smooth'
                });

                // Focus sur le premier champ de date
                setTimeout(() => {
                    const checkInInput = document.getElementById('check_in');
                    if (checkInInput) {
                        checkInInput.focus();
                    }
                }, 500);
            }
        }

        // Gestionnaire pour le retour en arrière du navigateur (cache)
        window.addEventListener('pageshow', function(event) {
            // Si la page est chargée depuis le cache (bfcache)
            if (event.persisted) {
                const bookNowBtn = document.getElementById('bookNowBtn');
                const checkAvailabilityBtn = document.getElementById('checkAvailabilityBtn');

                // Réinitialiser l'état des boutons
                if (bookNowBtn) {
                    bookNowBtn.disabled = false;
                    bookNowBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Réserver maintenant';
                }

                if (checkAvailabilityBtn) {
                    checkAvailabilityBtn.disabled = false;
                    checkAvailabilityBtn.innerHTML = 'Vérifier la disponibilité';
                }
            }
        });
    </script>

    <!-- Flatpickr JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
@endpush
