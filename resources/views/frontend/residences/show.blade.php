@extends('layouts.app')

@section('title', $residence->name . ' - Sages Home')

@push('styles')
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
                        <p class="text-muted mb-3">
                            <i class="bi bi-geo-alt me-2"></i>{{ $residence->address }}
                        </p>

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
                        'baignoire' => 'fas fa-bath'
                    ];
                    
                    function getAmenityIcon($amenity, $amenityIcons) {
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
                                <button class="btn btn-show-amenities" onclick="toggleAllAmenities()" id="toggleAmenitiesBtn">
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
                                    <label for="guests" class="form-label fw-semibold">Voyageurs</label>
                                    <select class="form-control" id="guests" name="guests" required>
                                        <option value="">Nombre de voyageurs</option>
                                        @for ($i = 1; $i <= $residence->capacity; $i++)
                                            <option value="{{ $i }}">
                                                {{ $i }} {{ $i === 1 ? 'voyageur' : 'voyageurs' }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div id="priceCalculation" class="mb-3" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <span>Prix par nuit:</span>
                                                <span
                                                    id="pricePerNight">{{ number_format($residence->price_per_night, 0) }}
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
                                    <p class="text-muted text-center mb-3">
                                        <a href="{{ route('login') }}">Connectez-vous</a> pour effectuer une réservation
                                    </p>
                                    <a href="{{ route('login') }}" class="btn btn-green w-100">
                                        Se connecter pour réserver
                                    </a>
                                @else
                                    <button type="button" id="checkAvailabilityBtn"
                                        class="btn btn-outline-primary w-100 mb-2">
                                        Vérifier la disponibilité
                                    </button>

                                    <button type="submit" id="bookNowBtn" class="btn btn-green w-100"
                                        style="display: none;" disabled>
                                        Réserver maintenant
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
                                                {{ number_format($similar->price_per_night, 0) }} FCFA/nuit
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

            // Auto-populate dates from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('check_in')) checkInInput.value = urlParams.get('check_in');
            if (urlParams.get('check_out')) checkOutInput.value = urlParams.get('check_out');
            if (urlParams.get('guests')) guestsInput.value = urlParams.get('guests');

            // Update minimum checkout date when checkin changes
            checkInInput.addEventListener('change', function() {
                const checkInDate = new Date(this.value);
                const nextDay = new Date(checkInDate);
                nextDay.setDate(checkInDate.getDate() + 1);
                checkOutInput.min = nextDay.toISOString().split('T')[0];

                if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
                    checkOutInput.value = nextDay.toISOString().split('T')[0];
                }

                hideAvailabilityResults();
            });

            checkOutInput.addEventListener('change', hideAvailabilityResults);
            guestsInput.addEventListener('change', hideAvailabilityResults);

            // Check availability
            if (checkAvailabilityBtn) {
                checkAvailabilityBtn.addEventListener('click', function() {
                    if (!checkInInput.value || !checkOutInput.value) {
                        showMessage('Veuillez sélectionner les dates d\'arrivée et de départ.', 'warning');
                        return;
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
                                check_in: checkInInput.value,
                                check_out: checkOutInput.value
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

                    if (!checkInInput.value || !checkOutInput.value || !guestsInput.value) {
                        showMessage('Veuillez remplir tous les champs.', 'warning');
                        return;
                    }

                    // Redirect to booking creation page
                    const params = new URLSearchParams({
                        check_in: checkInInput.value,
                        check_out: checkOutInput.value,
                        guests: guestsInput.value
                    });

                    window.location.href =
                    `{{ route('booking.create', $residence) }}?${params.toString()}`;
                });
            }

            function showAvailability(priceInfo) {
                document.getElementById('nightsCount').textContent = priceInfo.nights;
                document.getElementById('subtotal').textContent = formatPrice(priceInfo.total_price);

                const taxes = priceInfo.total_price * 0.10;
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
    </script>
@endpush
