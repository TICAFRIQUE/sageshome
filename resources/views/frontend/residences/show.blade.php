@extends('layouts.app')

@section('title', $residence->name . ' - Résidence meublée | Sages Home')
@section('meta_description', Str::limit(strip_tags($residence->description), 155))
@if ($residence->images->count() > 0)
    @section('og_image', $residence->images->first()->full_url)
    @section('twitter_image', $residence->images->first()->full_url)
@endif
@section('body_class', 'has-mobile-bar')

@php
    $images = $residence->images;
    $location = collect([$residence->commune, $residence->ville])->filter()->map(fn ($s) => ucwords(strtolower($s)))->implode(', ');

    $amenitiesList = $residence->amenities ?? [];
    if (is_string($amenitiesList)) {
        $amenitiesList = json_decode($amenitiesList, true) ?? [];
    }
    if (!is_array($amenitiesList)) {
        $amenitiesList = [];
    }
    $amenityLabels = \App\Models\Residence::getAmenityLabels();

    // Icônes Font Awesome par mot-clé
    $amenityIcons = [
        'wifi' => 'fa-wifi', 'internet' => 'fa-wifi', 'canal' => 'fa-tv', 'television' => 'fa-tv', 'tv' => 'fa-tv',
        'climat' => 'fa-snowflake', 'air' => 'fa-wind', 'chauffe' => 'fa-temperature-high', 'chauffage' => 'fa-fire',
        'cuisini' => 'fa-fire-burner', 'four' => 'fa-fire', 'micro' => 'fa-bowl-food', 'bouilloire' => 'fa-mug-hot',
        'refrigerateur' => 'fa-snowflake', 'mixeur' => 'fa-blender', 'ustensile' => 'fa-kitchen-set',
        'couvert' => 'fa-utensils', 'cuisine' => 'fa-utensils', 'menage' => 'fa-broom', 'securite' => 'fa-shield-halved',
        'piscine' => 'fa-person-swimming', 'parking' => 'fa-square-parking', 'garage' => 'fa-warehouse',
        'arrivee' => 'fa-key', 'annulation' => 'fa-calendar-xmark', 'animaux' => 'fa-paw', 'jardin' => 'fa-tree',
        'balcon' => 'fa-building', 'terrasse' => 'fa-umbrella-beach', 'ascenseur' => 'fa-elevator', 'gym' => 'fa-dumbbell',
        'sport' => 'fa-dumbbell', 'lave' => 'fa-soap', 'laverie' => 'fa-shirt', 'fumeur' => 'fa-ban-smoking',
        'enfant' => 'fa-child', 'bebe' => 'fa-baby', 'douche' => 'fa-shower', 'baignoire' => 'fa-bath',
    ];
    $amenityIcon = function ($amenity) use ($amenityIcons) {
        $key = Str::lower(Str::ascii($amenity));
        foreach ($amenityIcons as $keyword => $icon) {
            if (str_contains($key, $keyword)) {
                return $icon;
            }
        }
        return 'fa-circle-check';
    };
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" media="print" onload="this.media='all'">
    <style>
        /* ---------- Galerie ---------- */
        .gallery-stage {
            position: relative;
            aspect-ratio: 16 / 10;
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: var(--sage-bg-light);
            cursor: zoom-in;
        }
        .gallery-stage img { width: 100%; height: 100%; object-fit: cover; transition: opacity .35s var(--ease); }
        .gallery-stage img.is-switching { opacity: .25; }
        .gallery-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 0;
            background: rgba(255, 255, 255, .92);
            color: var(--sage-green-dark);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            opacity: 0;
            transition: opacity .25s var(--ease), transform .25s var(--ease);
        }
        .gallery-nav.prev { left: 14px; }
        .gallery-nav.next { right: 14px; }
        .gallery-stage:hover .gallery-nav, .gallery-nav:focus-visible { opacity: 1; }
        @media (hover: none) { .gallery-nav { opacity: 1; } }
        .gallery-counter, .gallery-expand {
            position: absolute;
            bottom: 14px;
            z-index: 2;
            padding: .35rem .8rem;
            border-radius: 999px;
            background: rgba(20, 28, 16, .7);
            color: #fff;
            font-size: .8rem;
            font-weight: 500;
            border: 0;
        }
        .gallery-counter { left: 14px; }
        .gallery-expand { right: 14px; }
        .thumbs-wrap { position: relative; margin-top: .75rem; }
        .thumbnail-gallery {
            display: flex;
            gap: .6rem;
            overflow-x: auto;
            padding: .15rem;
            scroll-snap-type: x proximity;
            scroll-behavior: smooth;
            overscroll-behavior-x: contain;
            -webkit-overflow-scrolling: touch;
            cursor: grab;
            scrollbar-width: none;
        }
        .thumbnail-gallery::-webkit-scrollbar { display: none; }
        .thumbnail-gallery.is-dragging { cursor: grabbing; scroll-snap-type: none; scroll-behavior: auto; user-select: none; }
        /* Bords estompés : indiquent qu'il reste des photos de chaque côté */
        .thumbs-nav {
            position: absolute; top: 50%; z-index: 3; width: 32px; height: 32px; margin-top: -16px;
            border: 0; border-radius: 50%; background: #fff; color: var(--sage-green-dark);
            box-shadow: 0 2px 10px rgba(27,33,23,.25); display: flex; align-items: center; justify-content: center;
            font-size: .8rem; opacity: 0; visibility: hidden;
            transition: opacity .2s var(--ease), transform .2s var(--ease), background .2s var(--ease);
        }
        .thumbs-nav:hover { background: var(--sage-gold-start); transform: scale(1.08); }
        .thumbs-nav.prev { left: 4px; } .thumbs-nav.next { right: 4px; }
        .thumbs-wrap.can-left .thumbs-nav.prev, .thumbs-wrap.can-right .thumbs-nav.next { opacity: 1; visibility: visible; }
        .thumbs-wrap::before, .thumbs-wrap::after {
            content: ''; position: absolute; top: 0; bottom: 0; width: 36px; z-index: 2; pointer-events: none;
            opacity: 0; transition: opacity .2s var(--ease);
        }
        .thumbs-wrap::before { left: 0; background: linear-gradient(90deg, #fff 15%, rgba(255,255,255,0)); }
        .thumbs-wrap::after { right: 0; background: linear-gradient(270deg, #fff 15%, rgba(255,255,255,0)); }
        .thumbs-wrap.can-left::before, .thumbs-wrap.can-right::after { opacity: 1; }
        .thumbnail {
            flex: 0 0 auto;
            width: 96px;
            height: 68px;
            object-fit: cover;
            border-radius: 12px;
            cursor: pointer;
            opacity: .6;
            border: 2px solid transparent;
            scroll-snap-align: center;
            -webkit-user-drag: none;
            transition: opacity .2s var(--ease), border-color .2s var(--ease), transform .2s var(--ease);
        }
        .thumbnail:hover { transform: translateY(-2px); }
        .thumbnail:hover, .thumbnail.active { opacity: 1; }
        .thumbnail.active { border-color: var(--sage-gold-end); }

        /* Visionneuse plein écran */
        #galleryModal .modal-content { background: rgba(12, 16, 10, .97); border: 0; }
        #galleryModal .modal-body { display: flex; align-items: center; justify-content: center; position: relative; padding: 3.5rem 1rem 1rem; }
        #galleryModal .lightbox-img { max-width: 100%; max-height: calc(100vh - 6rem); object-fit: contain; border-radius: 8px; }
        #galleryModal .gallery-nav { opacity: 1; }
        #galleryModal .btn-close { position: absolute; top: 1rem; right: 1rem; z-index: 3; }
        #galleryModal .lightbox-counter { position: absolute; top: 1.2rem; left: 1.25rem; color: #fff; font-size: .9rem; }

        /* ---------- En-tête résidence ---------- */
        .residence-head h1 { font-size: clamp(1.6rem, 3.4vw, 2.4rem); font-weight: 700; margin-bottom: .5rem; }
        .residence-facts { display: flex; flex-wrap: wrap; gap: .5rem; }
        .fact {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .45rem .9rem;
            border-radius: 999px;
            background: var(--sage-bg-light);
            border: 1px solid var(--sage-border);
            font-size: .88rem;
            font-weight: 500;
        }
        .fact i { color: var(--sage-gold-deep); }
        .detail-block { padding: 2rem 0; border-top: 1px solid var(--sage-border); }
        .detail-block h2 { font-size: 1.3rem; margin-bottom: 1rem; }
        .description-text { color: var(--sage-text); font-size: 1rem; line-height: 1.75; }

        /* ---------- Équipements ---------- */
        .amenities-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: .75rem; }
        .amenity-item {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .8rem 1rem;
            border-radius: 14px;
            border: 1px solid var(--sage-border);
            background: #fff;
            font-size: .93rem;
            font-weight: 500;
            transition: border-color .2s var(--ease), background .2s var(--ease);
        }
        .amenity-item:hover { border-color: var(--sage-gold-end); background: var(--sage-gold-soft); }
        .amenity-icon {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--sage-green-soft);
            color: var(--sage-green);
        }
        @media (max-width: 575.98px) {
            .amenities-grid { grid-template-columns: 1fr 1fr; gap: .5rem; }
            .amenity-item { padding: .6rem .65rem; gap: .55rem; font-size: .84rem; }
            .amenity-icon { width: 30px; height: 30px; font-size: .8rem; }
        }
        .amenities-hidden { display: none; }
        .amenities-hidden.show { display: flex; animation: dropIn .25s var(--ease); }

        /* ---------- Encart de réservation ---------- */
        .booking-card { position: sticky; top: calc(var(--header-h) + 20px); }
        .booking-panel {
            border-radius: var(--radius-lg);
            border: 1px solid var(--sage-border);
            box-shadow: var(--shadow-lg);
            background: #fff;
            overflow: hidden;
        }
        .booking-panel-head { padding: 1.4rem 1.5rem 1.1rem; border-bottom: 1px solid var(--sage-border); }
        .booking-price { font-family: var(--font-heading); font-size: 1.7rem; font-weight: 700; color: var(--sage-green-dark); line-height: 1.1; }
        .booking-price small { font-size: .9rem; font-weight: 500; color: var(--sage-text-secondary); font-family: var(--font-body); }
        .booking-panel-body { padding: 1.4rem 1.5rem 1.5rem; }
        .booking-dates {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid var(--sage-border);
            border-radius: 14px;
            overflow: hidden;
        }
        .booking-dates > div { padding: .6rem .85rem; }
        .booking-dates > div + div { border-left: 1px solid var(--sage-border); }
        .booking-dates .form-label, .booking-guests .form-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 0; }
        .booking-dates .form-control, .booking-guests .form-select {
            border: 0;
            padding: 0;
            min-height: 30px;
            box-shadow: none;
            font-weight: 500;
            background-color: transparent;
        }
        .booking-guests { border: 1px solid var(--sage-border); border-radius: 14px; padding: .6rem .85rem; margin-top: .75rem; }
        .booking-dates:focus-within, .booking-guests:focus-within { border-color: var(--sage-gold-end); box-shadow: 0 0 0 .2rem rgba(241, 212, 110, .3); }
        .price-breakdown { background: var(--sage-bg-light); border-radius: 14px; padding: 1rem 1.1rem; font-size: .92rem; }
        .price-breakdown > div { display: flex; justify-content: space-between; gap: 1rem; padding: .2rem 0; }
        .price-breakdown .total { border-top: 1px solid var(--sage-border); margin-top: .5rem; padding-top: .65rem; font-weight: 700; font-size: 1.02rem; color: var(--sage-green-dark); }
        #availabilityMessage { border: 0; border-radius: 12px; font-size: .88rem; padding: .75rem 1rem; }
        .date-info { display: flex; gap: .55rem; align-items: flex-start; margin: 0 0 .9rem; padding: .55rem .75rem; border-radius: 10px; background: var(--sage-bg-light); color: var(--sage-text-secondary); font-size: .78rem; line-height: 1.45; }
        .date-info i { color: #C0504D; margin-top: .15rem; }
        .date-info s { color: #C0504D; text-decoration-thickness: 1.5px; }
        .booking-reassurance { list-style: none; padding: 0; margin: 1rem 0 0; font-size: .84rem; color: var(--sage-text-secondary); }
        .booking-reassurance li { display: flex; gap: .5rem; margin-bottom: .35rem; }
        .booking-reassurance i { color: var(--sage-green); margin-top: .2rem; }

        .btn-book-now {
            background: var(--sage-gradient-gold);
            border: 0;
            color: var(--sage-green-dark);
            font-weight: 700;
            font-size: 1.02rem;
            padding: .9rem 1.5rem;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(194, 155, 50, .3);
        }
        .btn-book-now:hover { color: var(--sage-green-dark); transform: translateY(-2px); box-shadow: 0 12px 26px rgba(194, 155, 50, .4); }
        .btn-book-now.show { animation: dropIn .4s var(--ease); }
        .btn-book-now:disabled { background: #E6E2D2; color: #8C8A80; box-shadow: none; transform: none; }
        .btn-login { background: var(--sage-green); color: #fff; font-weight: 600; padding: .9rem 1.5rem; border-radius: 14px; }
        .btn-login:hover { background: var(--sage-green-dark); color: #fff; }

        .date-input-invalid { border-color: #dc3545 !important; animation: shakeInput .4s ease; }
        @keyframes shakeInput { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-4px); } 75% { transform: translateX(4px); } }

        /* ---------- Flatpickr aux couleurs de la marque ---------- */
        .flatpickr-input[readonly] { background-color: transparent !important; cursor: pointer; }
        .flatpickr-calendar { border-radius: 16px; border: 1px solid var(--sage-border); box-shadow: var(--shadow-lg); font-family: var(--font-body); }
        .flatpickr-calendar.arrowTop::before, .flatpickr-calendar.arrowTop::after { display: none; }
        .flatpickr-months { padding: .4rem .25rem; }
        .flatpickr-current-month { font-size: 1rem; font-weight: 600; }
        .flatpickr-months .flatpickr-prev-month:hover svg, .flatpickr-months .flatpickr-next-month:hover svg { fill: var(--sage-gold-deep); }
        .flatpickr-day { border-radius: 10px; }
        .flatpickr-day:hover { background: var(--sage-gold-soft); border-color: var(--sage-gold-soft); }
        .flatpickr-day.today { border-color: var(--sage-gold-end); }
        .flatpickr-day.selected, .flatpickr-day.selected:hover {
            background: var(--sage-green) !important;
            border-color: var(--sage-green) !important;
            color: #fff !important;
        }
        .flatpickr-calendar .flatpickr-day.unavailable,
        .flatpickr-calendar .flatpickr-day.unavailable:hover,
        .flatpickr-calendar .flatpickr-day.unavailable[aria-disabled="true"] {
            background: #FBEAEA !important;
            border-color: transparent !important;
            color: #C0504D !important;
            text-decoration: line-through;
            cursor: not-allowed !important;
            pointer-events: none !important;
            opacity: 1;
        }
        @media (max-width: 575.98px) { .flatpickr-calendar { max-width: calc(100vw - 24px); } }

        /* ---------- Barre de réservation mobile ---------- */
        .floating-book-btn {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1035;
            background: rgba(255, 255, 255, .97);
            -webkit-backdrop-filter: blur(12px);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--sage-border);
            box-shadow: 0 -8px 24px rgba(27, 33, 23, .08);
            padding: .75rem 1rem calc(.75rem + env(safe-area-inset-bottom));
            animation: slideUp .4s var(--ease) both;
        }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: none; } }
        .floating-book-btn .mobile-price { font-family: var(--font-heading); font-weight: 700; color: var(--sage-green-dark); line-height: 1.1; }
        .floating-book-btn .mobile-price small { font-family: var(--font-body); font-weight: 500; font-size: .78rem; color: var(--sage-text-secondary); }
        @media (max-width: 991.98px) { .site-footer { padding-bottom: 6rem; } }
    </style>
@endpush

@section('content')
    <div class="container pt-3 pt-lg-4 pb-5">
        <nav aria-label="Fil d'Ariane">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-house" aria-hidden="true"></i><span class="visually-hidden">Accueil</span></a></li>
                <li class="breadcrumb-item"><a href="{{ route('residences.index') }}">Résidences</a></li>
                @if ($residence->residenceType)
                    <li class="breadcrumb-item d-none d-sm-inline-flex"><a href="{{ route('residences.index', ['type' => $residence->residence_type_id]) }}">{{ $residence->residenceType->name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $residence->name }}</li>
            </ol>
        </nav>

        <div class="row g-4 g-xl-5">
            {{-- Colonne gauche --}}
            <div class="col-lg-7 col-xl-8">
                {{-- Galerie --}}
                <div class="image-gallery mb-4">
                    @if ($images->count() > 0)
                        <div class="gallery-stage" id="galleryStage" role="button" tabindex="0"
                            aria-label="Agrandir les photos de {{ $residence->name }}">
                            <img id="mainImage" src="{{ $images->first()->thumb(1280) }}" srcset="{{ $images->first()->srcset([800, 1280]) }}" sizes="(max-width: 991px) 100vw, 720px" alt="{{ $residence->name }} - photo 1"
                                width="1200" height="750" fetchpriority="high" decoding="async" class="img-fade">
                            @if ($images->count() > 1)
                                <button type="button" class="gallery-nav prev" data-gallery-step="-1" aria-label="Photo précédente"><i class="fas fa-chevron-left"></i></button>
                                <button type="button" class="gallery-nav next" data-gallery-step="1" aria-label="Photo suivante"><i class="fas fa-chevron-right"></i></button>
                                <span class="gallery-counter"><span id="galleryIndex">1</span> / {{ $images->count() }}</span>
                            @endif
                            <span class="gallery-expand"><i class="fas fa-expand me-1"></i>Agrandir</span>
                        </div>

                        @if ($images->count() > 1)
                            <div class="thumbs-wrap" id="thumbsWrap">
                            <button type="button" class="thumbs-nav prev" data-thumbs="-1" aria-label="Photos précédentes"><i class="fas fa-chevron-left"></i></button>
                            <button type="button" class="thumbs-nav next" data-thumbs="1" aria-label="Photos suivantes"><i class="fas fa-chevron-right"></i></button>
                            <div class="thumbnail-gallery" id="thumbsScroller" role="list" aria-label="Miniatures des photos">
                                @foreach ($images as $index => $image)
                                    <img src="{{ $image->thumb(200) }}" class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                                        alt="Miniature {{ $index + 1 }}" width="96" height="68" loading="lazy" decoding="async"
                                        role="listitem" tabindex="0" data-index="{{ $index }}"
                                        onclick="changeMainImage('', this)">
                                @endforeach
                            </div>
                            </div>
                        @endif
                    @else
                        <div class="gallery-stage d-flex align-items-center justify-content-center" style="cursor: default; background: var(--sage-gradient-green)">
                            <img src="{{ asset('images/logo-mark.webp') }}" alt="" style="width: 96px; height: 96px; object-fit: contain; opacity: .85">
                        </div>
                    @endif
                </div>

                {{-- En-tête --}}
                <div class="residence-head mb-4">
                    @if ($residence->is_featured)
                        <span class="rc-badge rc-badge-gold mb-2"><i class="fas fa-star"></i>Coup de cœur</span>
                    @endif
                    <h1>{{ $residence->name }}</h1>
                    <p class="text-muted mb-3">
                        <i class="fas fa-location-dot me-1" style="color: var(--sage-gold-deep)"></i>
                        {{ $location }}@if ($residence->address) · {{ $residence->address }}@endif
                        @if ($residence->hasGoogleMaps())
                            · <a href="{{ $residence->google_maps_url }}" target="_blank" rel="noopener" class="fw-semibold">Voir sur la carte</a>
                        @endif
                    </p>
                    <div class="residence-facts">
                        <span class="fact"><i class="fas fa-house"></i>{{ $residence->type_display }}</span>
                        <span class="fact"><i class="fas fa-user-friends"></i>Jusqu'à {{ $residence->capacity }} {{ $residence->capacity > 1 ? 'voyageurs' : 'voyageur' }}</span>
                        @if (count($amenitiesList))
                            <span class="fact"><i class="fas fa-circle-check"></i>{{ count($amenitiesList) }} équipements</span>
                        @endif
                    </div>
                </div>

                {{-- Description --}}
                <div class="detail-block">
                    <h2>À propos de ce logement</h2>
                    <div class="description-text">{!! nl2br(e($residence->description)) !!}</div>
                    @if ($residence->full_description)
                        <div id="fullDescription" class="collapse">
                            <div class="description-text mt-3">{!! nl2br(e($residence->full_description)) !!}</div>
                        </div>
                        <button class="btn btn-link px-0 fw-semibold text-decoration-none mt-2" type="button" data-bs-toggle="collapse"
                            data-bs-target="#fullDescription" aria-expanded="false" aria-controls="fullDescription">
                            Lire la suite <i class="fas fa-chevron-down ms-1 small"></i>
                        </button>
                    @endif
                </div>

                {{-- Équipements --}}
                @if (count($amenitiesList) > 0)
                    <div class="detail-block">
                        <h2>Ce que propose ce logement</h2>
                        <div class="amenities-grid" id="amenitiesGrid">
                            @foreach ($amenitiesList as $index => $amenity)
                                @php $label = $amenityLabels[$amenity] ?? ucfirst(str_replace('_', ' ', trim($amenity))); @endphp
                                <div class="amenity-item {{ $index >= 8 ? 'amenities-hidden' : '' }}">
                                    <span class="amenity-icon"><i class="fas {{ $amenityIcon($amenity . ' ' . $label) }}"></i></span>
                                    <span class="amenity-text">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                        @if (count($amenitiesList) > 8)
                            <button class="btn btn-outline-primary mt-3" onclick="toggleAllAmenities()" id="toggleAmenitiesBtn" type="button">
                                <i class="fas fa-plus me-2"></i>Voir les {{ count($amenitiesList) }} équipements
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Colonne droite : réservation --}}
            <div class="col-lg-5 col-xl-4">
                <div class="booking-card" id="booking">
                    <div class="booking-panel">
                        <div class="booking-panel-head">
                            <div class="booking-price">
                                {{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA <small>/ nuit</small>
                            </div>
                            <div class="text-muted small">≈ {{ number_format(fcfa_to_eur($residence->price_per_night), 2, ',', ' ') }} € par nuit</div>
                        </div>

                        <div class="booking-panel-body">
                            <form id="bookingForm" novalidate>
                                @csrf
                                <input type="hidden" name="residence_id" value="{{ $residence->id }}">

                                <div class="booking-dates">
                                    <div>
                                        <label for="check_in" class="form-label">Arrivée</label>
                                        <input type="date" class="form-control" id="check_in" name="check_in"
                                            min="{{ date('Y-m-d') }}" placeholder="Choisir" required>
                                    </div>
                                    <div>
                                        <label for="check_out" class="form-label">Départ</label>
                                        <input type="date" class="form-control" id="check_out" name="check_out" placeholder="Choisir" required>
                                    </div>
                                </div>

                                <div class="booking-guests">
                                    <label for="guests" class="form-label">Voyageurs</label>
                                    <select class="form-select" id="guests" name="guests" required>
                                        <option selected disabled>Choisir</option>
                                        @for ($i = 1; $i <= $residence->capacity; $i++)
                                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'voyageur' : 'voyageurs' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <small class="text-muted d-block mt-1 mb-3">Capacité maximale : {{ $residence->capacity }} {{ $residence->capacity > 1 ? 'voyageurs' : 'voyageur' }}</small>
                                <div id="dateInfo" class="date-info" role="note" hidden></div>

                                <div id="priceCalculation" class="mb-3" style="display: none;" aria-live="polite">
                                    <div class="price-breakdown">
                                        <div>
                                            <span>Prix par nuit</span>
                                            <span id="pricePerNight">{{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA</span>
                                        </div>
                                        <div>
                                            <span>Nombre de nuits</span>
                                            <span id="nightsCount">0</span>
                                        </div>
                                        <div>
                                            <span>Sous-total</span>
                                            <span id="subtotal">0 FCFA</span>
                                        </div>
                                        <div class="d-none">
                                            <span>Taxes</span>
                                            <span id="taxes">0 FCFA</span>
                                        </div>
                                        <div class="total">
                                            <span>Total</span>
                                            <span id="totalPrice">0 FCFA</span>
                                        </div>
                                    </div>
                                </div>

                                <div id="availabilityMessage" class="alert" style="display: none;" role="status"></div>

                                @guest
                                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-login w-100">
                                        <i class="fas fa-right-to-bracket me-2"></i>Se connecter pour réserver
                                    </a>
                                    <p class="text-center small text-muted mt-2 mb-0">
                                        Pas encore de compte ? <a href="{{ route('register') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="fw-semibold">Inscrivez-vous</a>
                                    </p>
                                @else
                                    <button type="button" id="checkAvailabilityBtn" class="btn btn-outline-primary w-100 mb-2">
                                        Vérifier la disponibilité
                                    </button>

                                    <button type="submit" id="bookNowBtn" class="btn btn-book-now w-100" style="display: none;" disabled>
                                        <i class="fas fa-calendar-check me-2"></i>Réserver maintenant
                                    </button>
                                @endguest
                            </form>

                            <ul class="booking-reassurance">
                                <li><i class="fas fa-check"></i>Disponibilité vérifiée en temps réel</li>
                                <li><i class="fas fa-check"></i>Confirmation envoyée par e-mail</li>
                                <li><i class="fas fa-check"></i>Assistance 24h/24 pendant votre séjour</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Résidences similaires --}}
        @if ($similarResidences->count() > 0)
            <section class="mt-5 pt-4 border-top">
                <div class="d-flex justify-content-between align-items-end mb-4 gap-3">
                    <div>
                        <span class="eyebrow">À découvrir aussi</span>
                        <h2 class="h3 mb-0">Résidences similaires</h2>
                    </div>
                    <a href="{{ route('residences.index', ['type' => $residence->residence_type_id]) }}" class="btn btn-outline-primary d-none d-sm-inline-flex">Tout voir</a>
                </div>
                <div class="row g-4">
                    @foreach ($similarResidences as $similar)
                        <div class="col-sm-6 col-lg-4" data-reveal style="--reveal-delay: {{ $loop->index * 0.08 }}s">
                            @include('frontend.partials.residence-card', ['residence' => $similar])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- Visionneuse photos --}}
    @if ($images->count() > 0)
        <div class="modal fade" id="galleryModal" tabindex="-1" aria-label="Photos de {{ $residence->name }}" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-body">
                        <span class="lightbox-counter"><span id="lightboxIndex">1</span> / {{ $images->count() }}</span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        @if ($images->count() > 1)
                            <button type="button" class="gallery-nav prev" data-gallery-step="-1" aria-label="Photo précédente"><i class="fas fa-chevron-left"></i></button>
                            <button type="button" class="gallery-nav next" data-gallery-step="1" aria-label="Photo suivante"><i class="fas fa-chevron-right"></i></button>
                        @endif
                        <img id="lightboxImage" class="lightbox-img" src="{{ $images->first()->thumb(1600) }}" alt="{{ $residence->name }}">
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Barre de réservation mobile --}}
    <div class="floating-book-btn d-lg-none">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="mobile-price">
                {{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA <small>/ nuit</small>
                <small class="d-block">≈ {{ number_format(fcfa_to_eur($residence->price_per_night), 2, ',', ' ') }} €</small>
            </div>
            <button type="button" class="btn btn-primary px-4" onclick="scrollToBooking()">
                <i class="fas fa-calendar-check me-2"></i>Réserver
            </button>
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
                            'Choisissez vos dates d\'arrivée et de départ ainsi que le nombre de voyageurs pour voir le prix.',
                            'info');
                    } else {
                        const missingText = missingFields.length === 1 ?
                            missingFields[0] :
                            missingFields.slice(0, -1).join(', ') + ' et ' + missingFields.slice(-1);
                        showMessage(`Il manque encore : ${missingText}.`, 'warning');
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
                        showMessage(`Veuillez renseigner : ${missingText}.`, 'warning');
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
                const zone = document.getElementById('dateInfo');
                if (!zone || unavailableDates.length === 0) return;
                const next = getNextUnavailableDate();
                zone.innerHTML =
                    '<i class="fas fa-calendar-xmark"></i>' +
                    '<span>Certaines dates sont déjà prises' +
                    (next ? ' (prochaine : <strong>' + formatDateForDisplay(next) + '</strong>)' : '') +
                    '. Elles sont <s>barrées</s> dans le calendrier.</span>';
                zone.hidden = false;
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

        /* ---------- Galerie photos ---------- */
        const galleryImages = @json($images->map(fn ($img) => $img->thumb(1280))->values());
        let galleryIndex = 0;

        function showImage(index) {
            if (!galleryImages.length) return;
            galleryIndex = (index + galleryImages.length) % galleryImages.length;
            const src = galleryImages[galleryIndex];
            const main = document.getElementById('mainImage');

            if (main && main.getAttribute('src') !== src) {
                main.classList.add('is-switching');
                const preload = new Image();
                preload.onload = preload.onerror = () => {
                    main.removeAttribute('srcset'); // sinon le navigateur garde l'ancienne image
                    main.src = src;
                    main.alt = main.alt.replace(/photo \d+$/, 'photo ' + (galleryIndex + 1));
                    main.classList.remove('is-switching');
                };
                preload.src = src;
            }

            const lightbox = document.getElementById('lightboxImage');
            if (lightbox) lightbox.src = src;
            document.querySelectorAll('#galleryIndex, #lightboxIndex').forEach(el => el.textContent = galleryIndex + 1);

            document.querySelectorAll('.thumbnail').forEach(t => {
                const active = Number(t.dataset.index) === galleryIndex;
                t.classList.toggle('active', active);
                if (active) t.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' });
            });
        }

        // Conservé pour compatibilité (attribut onclick des miniatures)
        function changeMainImage(src, thumbnail) {
            showImage(Number(thumbnail.dataset.index) || 0);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const stage = document.getElementById('galleryStage');
            const modalEl = document.getElementById('galleryModal');
            const modal = modalEl && window.bootstrap ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;

            document.querySelectorAll('[data-gallery-step]').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.stopPropagation();
                    showImage(galleryIndex + Number(btn.dataset.galleryStep));
                });
            });

            document.querySelectorAll('.thumbnail').forEach(t => {
                t.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); showImage(Number(t.dataset.index)); }
                });
            });

            if (stage && modal) {
                stage.addEventListener('click', () => modal.show());
                stage.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); modal.show(); }
                });
            }

            // Flèches du clavier dans la visionneuse
            document.addEventListener('keydown', e => {
                if (!modalEl || !modalEl.classList.contains('show')) return;
                if (e.key === 'ArrowLeft') showImage(galleryIndex - 1);
                if (e.key === 'ArrowRight') showImage(galleryIndex + 1);
            });

            // Balayage tactile
            [stage, modalEl].forEach(el => {
                if (!el) return;
                let startX = null;
                el.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
                el.addEventListener('touchend', e => {
                    if (startX === null) return;
                    const dx = e.changedTouches[0].clientX - startX;
                    if (Math.abs(dx) > 45) showImage(galleryIndex + (dx < 0 ? 1 : -1));
                    startX = null;
                }, { passive: true });
            });

            // Miniatures : bords estompés, molette verticale -> horizontale, glisser à la souris
            const scroller = document.getElementById('thumbsScroller');
            const wrap = document.getElementById('thumbsWrap');
            if (scroller && wrap) {
                const updateEdges = () => {
                    const max = scroller.scrollWidth - scroller.clientWidth;
                    wrap.classList.toggle('can-left', scroller.scrollLeft > 4);
                    wrap.classList.toggle('can-right', scroller.scrollLeft < max - 4);
                };
                scroller.addEventListener('scroll', updateEdges, { passive: true });
                window.addEventListener('resize', updateEdges);
                updateEdges();

                wrap.querySelectorAll('[data-thumbs]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        scroller.scrollBy({ left: Number(btn.dataset.thumbs) * Math.max(scroller.clientWidth * 0.8, 120), behavior: 'smooth' });
                    });
                });

                let down = false, moved = false, startX = 0, startLeft = 0;
                scroller.addEventListener('mousedown', e => { down = true; moved = false; startX = e.pageX; startLeft = scroller.scrollLeft; });
                window.addEventListener('mousemove', e => {
                    if (!down) return;
                    const dx = e.pageX - startX;
                    if (Math.abs(dx) > 5) { moved = true; scroller.classList.add('is-dragging'); }
                    if (moved) scroller.scrollLeft = startLeft - dx;
                });
                window.addEventListener('mouseup', () => { down = false; scroller.classList.remove('is-dragging'); });
                // Un glisser ne doit pas être pris pour un clic sur une miniature
                scroller.addEventListener('click', e => { if (moved) { e.stopPropagation(); e.preventDefault(); moved = false; } }, true);
            }

            // Précharger les photos suivantes quand la page est au repos
            const idle = window.requestIdleCallback || (cb => setTimeout(cb, 1500));
            idle(() => galleryImages.slice(1, 4).forEach(src => { const i = new Image(); i.src = src; }));
        });

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
                const header = document.querySelector('.site-header');
                const yOffset = -((header ? header.offsetHeight : 70) + 16); // hauteur du header fixe
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/fr.js" defer></script>
@endpush
