@extends('layouts.app')

@section('title', 'Sages Home - Résidences de luxe')

@section('content')
    <!-- Hero Banner Section -->
    <section class="hero-banner position-relative overflow-hidden">
        <div class="container position-relative">
            <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
                <div class="col-12 text-center">
                    <div class="hero-content text-white mb-4">
                        <h1 class="display-4 fw-bold mb-3 text-white">
                            Bienvenue chez Sages Home
                        </h1>
                        <p class="lead mb-4 text-white">
                            Vivez l'expérience du confort raffiné, au cœur de nos résidences sélectionnées avec soin.
                        </p>
                    </div>

                    <a href="{{ route('residences.index') }}" class="btn btn-primary btn-lg px-4 py-3">
                        <i class="fas fa-home me-2"></i>Découvrir nos résidences
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Residences Section -->
    @if ($featuredResidences->count() > 0)
        <section id="residences-vedette" class="py-5 bg-light">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-8 mx-auto text-center">
                        <h2 class="display-5 fw-bold mb-3" style="color: var(--sage-green-dark);">
                            <i class="fas fa-star me-3" style="color: var(--sage-gold-end);"></i>
                            Résidences en Vedette
                        </h2>
                        <p class="lead text-muted">
                            Découvrez notre sélection exclusive des meilleures résidences,
                            choisies pour leur emplacement privilégié et leurs services exceptionnels.
                        </p>
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    @foreach ($featuredResidences as $residence)
                        <div class="col-lg-4 col-md-6">
                            <div class="card residence-card h-100 border-0 shadow-sm">
                                <div class="position-relative">
                                    @if ($residence->primaryImage)
                                        <img src="{{ $residence->primaryImage->full_url }}"
                                            class="card-img-top residence-image" alt="{{ $residence->name }}"
                                            style="height: 250px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center"
                                            style="height: 250px; background: linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end));">
                                            <i class="fas fa-home text-white fa-3x"></i>
                                        </div>
                                    @endif

                                    <div class="position-absolute top-0 end-0 p-3">
                                        <span class="badge badge-gold fs-6 px-3 py-2">
                                            <i class="fas fa-star me-1"></i>Vedette
                                        </span>
                                    </div>

                                    <div class="position-absolute bottom-0 start-0 p-3">
                                        <div
                                            class="d-flex align-items-center bg-white bg-opacity-90 rounded-pill px-3 py-1">
                                            <i class="fas fa-map-marker-alt me-1" style="color: var(--sage-gold-end);"></i>
                                            <small class="fw-medium">
                                                {{ $residence->ville ?? 'Abidjan' }}{{ $residence->commune ? ', ' . $residence->commune : '' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-2" style="color: var(--sage-green-dark);">
                                        {{ $residence->name }}
                                    </h5>

                                    <!-- Localisation -->
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                        <small class="text-muted">
                                            {{ $residence->ville ?? 'Abidjan' }}{{ $residence->commune ? ', ' . $residence->commune : '' }}
                                            @if ($residence->address)
                                                <br><span
                                                    class="text-muted">{{ Str::limit($residence->address, 30) }}</span>
                                            @endif
                                        </small>
                                    </div>

                                    <p class="card-text text-muted mb-3">
                                        {{ Str::limit($residence->description, 80) }}
                                    </p>



                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-users me-2" style="color: var(--sage-gold-end);"></i>
                                                <small class="text-muted">{{ $residence->capacity }} voyageurs</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-bed me-2" style="color: var(--sage-gold-end);"></i>
                                                <small class="text-muted">{{ $residence->bedrooms ?? 'N/A' }}
                                                    chambres</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span
                                                class="text-gold fw-bold fs-4">{{ number_format($residence->price_per_night, 0, ',', ' ') }}
                                                FCFA</span>
                                            <small class="text-muted d-block">({{ number_format(fcfa_to_eur($residence->price_per_night), 2, ',', ' ') }} €) par nuit</small>
                                        </div>

                                        <a href="{{ route('residences.show', $residence->slug) }}"
                                            class="btn btn-primary btn-lg">
                                            <i class="fas fa-arrow-right me-2"></i>Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center">
                    <a href="{{ route('residences.index') }}" class="btn btn-outline-primary btn-lg px-5 py-3">
                        <i class="fas fa-th-large me-2"></i>Voir toutes les résidences vedettes
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Residences by Category -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-3" style="color: var(--sage-green-dark);">
                        <i class="fas fa-th-large me-3" style="color: var(--sage-gold-end);"></i>
                        Nos Types de Résidences
                    </h2>
                    <p class="lead text-muted">
                        Que vous recherchiez un studio cosy ou un appartement spacieux,
                        nous avons la résidence parfaite pour vos besoins.
                    </p>
                </div>
            </div>

            <div class="row">
                @php
                    $gradients = [
                        'linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary))',
                        'linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end))',
                        'linear-gradient(135deg, #2F4A33, #4A6B42)',
                        'linear-gradient(135deg, var(--sage-green-secondary), var(--sage-green-dark))',
                        'linear-gradient(135deg, #4A6B42, var(--sage-gold-start))',
                    ];

                    $icons = ['fas fa-home', 'fas fa-building', 'fas fa-city', 'fas fa-hotel', 'fas fa-warehouse'];
                @endphp

                @foreach ($allResidenceTypes as $index => $residenceType)
                    @if ($residenceType->residences->count() > 0)
                        <div class="col-12 mb-5">
                            <!-- En-tête du type de résidence -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header text-white text-center py-4"
                                            style="background: {{ $gradients[$index % count($gradients)] }} !important;">
                                            <div class="row align-items-center">
                                                <div class="col-md-2 text-center">
                                                    <i class="{{ $icons[$index % count($icons)] }} text-gold fa-3x"></i>
                                                </div>
                                                <div class="col-md-8 text-center text-md-start">
                                                    <h3 class="fw-bold text-white mb-1">{{ $residenceType->name }}</h3>
                                                    <p class="text-white-75 mb-0">
                                                        {{ $residenceType->description ?? 'Découvrez nos résidences de qualité' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <span class="badge bg-white text-dark px-3 py-2 fs-6">
                                                        {{ $residenceType->residences->count() }}
                                                        résidence{{ $residenceType->residences->count() > 1 ? 's' : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Liste des résidences en colonnes de 3 -->
                            <div class="row g-4">
                                @foreach ($residenceType->residences->take(6) as $residence)
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card residence-card h-100 border-0 shadow-sm">
                                            <div class="position-relative">
                                                @if ($residence->primaryImage)
                                                    <img src="{{ $residence->primaryImage->full_url }}"
                                                        class="card-img-top residence-image" alt="{{ $residence->name }}"
                                                        style="height: 200px; object-fit: cover;">
                                                @else
                                                    <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center"
                                                        style="height: 200px; background: {{ $gradients[$index % count($gradients)] }};">
                                                        <i class="fas fa-home text-white fa-2x"></i>
                                                    </div>
                                                @endif

                                                <div class="position-absolute top-0 end-0 p-3">
                                                    <span class="badge badge-gold fs-6 px-2 py-1">
                                                        <i class="fas fa-star me-1"></i>{{ $residenceType->name }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="card-body p-3">
                                                <h5 class="card-title fw-bold mb-2"
                                                    style="color: var(--sage-green-dark);">
                                                    {{ $residence->name }}
                                                </h5>

                                                <!-- Localisation -->
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-map-marker-alt me-1 text-muted"
                                                        style="font-size: 0.8rem;"></i>
                                                    <small class="text-muted">
                                                        {{ $residence->ville ?? 'Abidjan' }}{{ $residence->commune ? ', ' . $residence->commune : '' }}
                                                    </small>
                                                </div>

                                                <p class="card-text text-muted mb-2 small">
                                                    {{ Str::limit($residence->description, 60) }}
                                                </p>

                                                <!-- Équipements compacts -->
                                                @if ($residence->equipements && count($residence->equipements) > 0)
                                                    <div class="mb-2">
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach ($residence->equipements->take(2) as $equipement)
                                                                <span class="badge bg-light text-dark border"
                                                                    style="font-size: 0.7rem;">
                                                                    <i class="fas fa-check me-1"
                                                                        style="color: var(--sage-gold-end); font-size: 0.7rem;"></i>
                                                                    {{ Str::limit($equipement->name, 8) }}
                                                                </span>
                                                            @endforeach
                                                            @if (count($residence->equipements) > 2)
                                                                <span class="badge bg-secondary"
                                                                    style="font-size: 0.7rem;">
                                                                    +{{ count($residence->equipements) - 2 }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row g-2 mb-3 text-center">
                                                    <div class="col-4">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-users mb-1"
                                                                style="color: var(--sage-gold-end);"></i>
                                                            <small class="text-muted">{{ $residence->capacity }}p</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-bed mb-1"
                                                                style="color: var(--sage-gold-end);"></i>
                                                            <small
                                                                class="text-muted">{{ $residence->bedrooms ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-star mb-1"
                                                                style="color: var(--sage-gold-end);"></i>
                                                            <small class="text-muted">5.0</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span
                                                            class="text-gold fw-bold fs-5">{{ number_format($residence->price_per_night, 0, ',', ' ') }}
                                                            FCFA</span>
                                                        <small class="text-muted d-block">({{ number_format(fcfa_to_eur($residence->price_per_night), 2, ',', ' ') }} €) par nuit</small>
                                                    </div>

                                                    <a href="{{ route('residences.show', $residence->slug) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-arrow-right me-1"></i>Voir détails
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($residenceType->residences->count() > 6)
                                <div class="text-center mt-4">
                                    <a href="{{ route('residences.index', ['type' => $residenceType->id]) }}"
                                        class="btn btn-primary btn-lg px-4">
                                        <i class="fas fa-eye me-2"></i>Voir toutes les {{ $residenceType->name }}
                                        ({{ $residenceType->residences->count() }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <div style="position: relative; top: -100px;"></div>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-3" style="color: var(--sage-green-dark);">
                        <i class="fas fa-heart me-3" style="color: var(--sage-gold-end);"></i>
                        Pourquoi Choisir Sages Home ?
                    </h2>
                    <p class="lead text-muted">
                        Notre engagement envers l'excellence se reflète dans chaque aspect de votre séjour.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-5">
                            <div class="feature-icon mb-4">
                                <div class="icon-circle mx-auto d-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary)); border-radius: 50%;">
                                    <i class="fas fa-shield-alt text-white fa-2x"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-3 text-green">Sécurisé & Vérifié</h4>
                            <p class="text-muted">
                                Toutes nos résidences sont rigoureusement vérifiées et sécurisées pour garantir
                                votre tranquillité d'esprit pendant tout votre séjour.
                            </p>
                            <ul class="list-unstyled mt-3">
                                <li><i class="fas fa-check text-success me-2"></i>Propriétés vérifiées</li>
                                <li><i class="fas fa-check text-success me-2"></i>Sécurité 24h/24</li>
                                <li><i class="fas fa-check text-success me-2"></i>Assurance incluse</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-5">
                            <div class="feature-icon mb-4">
                                <div class="icon-circle mx-auto d-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end)); border-radius: 50%;">
                                    <i class="fas fa-gem text-white fa-2x"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-3 text-green">Luxe & Raffinement</h4>
                            <p class="text-muted">
                                Nos résidences sont sélectionnées avec soin pour offrir le summum du confort
                                et de l'élégance dans les quartiers les plus prestigieux.
                            </p>
                            <ul class="list-unstyled mt-3">
                                <li><i class="fas fa-check text-success me-2"></i>Équipements haut de gamme</li>
                                <li><i class="fas fa-check text-success me-2"></i>Design moderne</li>
                                <li><i class="fas fa-check text-success me-2"></i>Services premium</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mx-auto">
                    <div class="card feature-card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-5">
                            <div class="feature-icon mb-4">
                                <div class="icon-circle mx-auto d-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px; background: linear-gradient(135deg, #2F4A33, #4A6B42); border-radius: 50%;">
                                    <i class="fas fa-headset text-white fa-2x"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-3 text-green">Support Dédié 24/7</h4>
                            <p class="text-muted">
                                Notre équipe d'experts est disponible à tout moment pour vous accompagner
                                et répondre à tous vos besoins durant votre séjour.
                            </p>
                            <ul class="list-unstyled mt-3">
                                <li><i class="fas fa-check text-success me-2"></i>Support multilingue</li>
                                <li><i class="fas fa-check text-success me-2"></i>Assistance urgente</li>
                                <li><i class="fas fa-check text-success me-2"></i>Conciergerie premium</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section avec Contact -->
            <div class="row mt-5" id="contact" style="scroll-margin-top: 170px;">
                <div class="col-12">
                    <div class="card cta-card border-0 overflow-hidden">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <div class="col-md-8">
                                    <div class="p-5 h-100 d-flex flex-column justify-content-center"
                                        style="background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary));">
                                        <h3 class="text-white fw-bold mb-3">
                                            <i class="fas fa-envelope me-3" style="color: var(--sage-gold-end);"></i>
                                            Contactez-Nous
                                        </h3>
                                        <p class="text-white-50 mb-4">
                                            Notre équipe est à votre disposition pour vous accompagner
                                        </p>

                                        <!-- Informations de contact -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="contact-icon me-3">
                                                        <div class="icon-circle d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                                            <i class="fas fa-map-marker-alt text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="text-white fw-bold mb-1">Adresse</h6>
                                                        <small class="text-white-50">
                                                            {{ $parametre?->localisation ?? 'Abidjan , Cote d\'Ivoire' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="contact-icon me-3">
                                                        <div class="icon-circle d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                                            <i class="fas fa-phone text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="text-white fw-bold mb-1">Téléphone</h6>
                                                        <small class="text-white-50"> <a
                                                                href="tel:{{ $parametre?->contact1 }}"
                                                                class="text-white-50 text-decoration-none">{{ $parametre?->contact1 }}</a>
                                                        </small><br>
                                                        <small class="text-white-50"> <a
                                                                href="tel:{{ $parametre?->contact2 }}"
                                                                class="text-white-50 text-decoration-none">{{ $parametre?->contact2 }}</a>
                                                        </small><br>
                                                         <small class="text-white-50"> <a
                                                                href="tel:{{ $parametre?->contact3 }}"
                                                                class="text-white-50 text-decoration-none">{{ $parametre?->contact3 }}</a>
                                                        </small>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="contact-icon me-3">
                                                        <div class="icon-circle d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                                            <i class="fas fa-envelope text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="text-white fw-bold mb-1">Email</h6>
                                                        <small class="text-white-50">
                                                            <a href="mailto:{{ $parametre?->email1 ?? 'infos@sageshome.ci' }}"
                                                                class="text-white-50 text-decoration-none">{{ $parametre?->email1 ?? 'infos@sageshome.ci' }}</a>
                                                        </small><br>
                                                        <small class="text-white-50">
                                                            <a href="mailto:{{ $parametre?->email2 }}"
                                                                class="text-white-50 text-decoration-none">{{ $parametre?->email2 }}</a>
                                                        </small>
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="contact-icon me-3">
                                                        <div class="icon-circle d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                                                            <i class="fas fa-share-alt text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="text-white fw-bold mb-1">Suivez-nous</h6>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ $parametre?->lien_facebook ?? '#' }}" class="social-link" title="Facebook">
                                                                <div class="social-icon-small"
                                                                    style="background: #1877f2;">
                                                                    <i class="fab fa-facebook-f text-white"></i>
                                                                </div>
                                                            </a>
                                                            <a href="{{ $parametre?->lien_instagram ?? '#' }}" class="social-link" title="Instagram">
                                                                <div class="social-icon-small"
                                                                    style="background: #e1306c;">
                                                                    <i class="fab fa-instagram text-white"></i>
                                                                </div>
                                                            </a>
                                                            
                                                            <a href="https://wa.me/{{$parametre?->contact2}}?text=Bonjour, je suis interessé(e) par vos residences Sages Home." target="_blank" class="social-link" title="WhatsApp">
                                                                <div class="social-icon-small"
                                                                    style="background: #25d366;">
                                                                    <i class="fab fa-whatsapp text-white"></i>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-3">
                                            <a href="{{ route('residences.index') }}" class="btn btn-light btn-lg px-4">
                                                <i class="fas fa-search me-2"></i>Explorer nos résidences
                                            </a>
                                            <a href="tel:+22527200000" class="btn btn-outline-light btn-lg px-4">
                                                <i class="fas fa-phone me-2"></i>Appelez-nous
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="h-100 d-flex align-items-center justify-content-center p-5"
                                        style="background: linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end));">
                                        <div class="text-center">
                                            <i class="fas fa-award text-white fa-5x mb-3"></i>
                                            <h4 class="text-white fw-bold">Excellence Garantie</h4>
                                            <p class="text-white-75">5 étoiles sur 5</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
    <style>
        /* Hero Banner Styles */
        .hero-banner {
            min-height: 60vh;
            position: relative;
            background:
                linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
                url('{{ asset('images/banner2.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .min-vh-75 {
            min-height: 75vh;
        }

        .min-vh-100 {
            min-height: 100vh;
        }

        .hero-title {
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.5);
            line-height: 1.2;
        }

        .hero-content p {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
        }

        .search-card {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 3;
        }

        .search-card .form-control:focus,
        .search-card .form-select:focus {
            border-color: var(--sage-gold-end);
            box-shadow: 0 0 0 0.2rem rgba(242, 209, 138, 0.25);
        }

        /* Animation for scroll indicator */
        .animate-bounce {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            53%,
            80%,
            100% {
                transform: translate3d(0, 0, 0);
            }

            40%,
            43% {
                transform: translate3d(0, -10px, 0);
            }

            70% {
                transform: translate3d(0, -5px, 0);
            }

            90% {
                transform: translate3d(0, -2px, 0);
            }
        }

        /* Residence Cards */
        .residence-card {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .residence-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15) !important;
        }

        .residence-image {
            transition: transform 0.3s ease;
        }

        .residence-card:hover .residence-image {
            transform: scale(1.05);
        }

        /* Category Cards */
        .category-card {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .mini-residence-card {
            transition: transform 0.2s ease;
            overflow: hidden;
        }

        .mini-residence-card:hover {
            transform: scale(1.05);
        }

        /* Feature Cards */
        .feature-card {
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .icon-circle {
            transition: transform 0.3s ease;
        }

        .feature-card:hover .icon-circle {
            transform: scale(1.1);
        }

        /* CTA Card */
        .cta-card {
            overflow: hidden;
        }

        /* Contact Section Styles */
        .contact-card {
            transition: all 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .social-link {
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .social-link:hover {
            transform: scale(1.1);
        }

        .social-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .social-icon-small {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        .contact-icon .icon-circle {
            transition: transform 0.3s ease;
        }

        .contact-card:hover .contact-icon .icon-circle {
            transform: scale(1.05);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-banner {
                min-height: 50vh;
                background-attachment: scroll;
            }

            .contact-card {
                margin-bottom: 1rem;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-banner {
                min-height: 60vh;
            }

            .hero-title {
                font-size: 3rem !important;
            }

            .lead {
                font-size: 1.3rem !important;
            }

            .btn-primary {
                font-size: 1.1rem !important;
                padding: 0.8rem 1.5rem !important;
            }
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validation des dates
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');

            if (checkInInput && checkOutInput) {
                checkInInput.addEventListener('change', function() {
                    const checkInDate = new Date(this.value);
                    const nextDay = new Date(checkInDate);
                    nextDay.setDate(checkInDate.getDate() + 1);

                    checkOutInput.min = nextDay.toISOString().split('T')[0];

                    if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
                        checkOutInput.value = nextDay.toISOString().split('T')[0];
                    }
                });
            }

            // Smooth scroll pour les ancres
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Animation des cartes au scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Appliquer l'animation aux éléments
            document.querySelectorAll('.residence-card, .category-card, .feature-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Parallax effect pour le hero banner
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const heroBackground = document.querySelector('.hero-background');

                if (heroBackground && scrolled < window.innerHeight) {
                    const rate = scrolled * -0.3;
                    heroBackground.style.transform = `translate3d(0, ${rate}px, 0)`;
                }
            });

            // Animation du scroll indicator
            const scrollIndicator = document.querySelector('.scroll-indicator');
            if (scrollIndicator) {
                window.addEventListener('scroll', function() {
                    const scrolled = window.pageYOffset;
                    if (scrolled > 100) {
                        scrollIndicator.style.opacity = '0';
                    } else {
                        scrollIndicator.style.opacity = '1';
                    }
                });
            }
        });
    </script>
@endpush
