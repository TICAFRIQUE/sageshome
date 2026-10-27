@extends('layouts.app')

@section('title', 'Sages Home - Résidences meublées haut de gamme à Abidjan')
@section('body_class', 'page-home')

@push('preload')
    <link rel="preload" as="image" type="image/webp" fetchpriority="high"
        imagesrcset="{{ asset('images/hero-640.webp') }} 640w, {{ asset('images/hero-1100.webp') }} 1100w, {{ asset('images/hero-1920.webp') }} 1920w"
        imagesizes="100vw">
@endpush

@php
    $phone = $parametre?->contact1 ?: $parametre?->contact2;
    $email = $parametre?->email1 ?: 'infos@sageshome.ci';
@endphp

@section('content')
    {{-- ================= HERO ================= --}}
    <section class="home-hero">
        <picture class="home-hero-bg" aria-hidden="true">
            <source type="image/webp"
                srcset="{{ asset('images/hero-640.webp') }} 640w, {{ asset('images/hero-1100.webp') }} 1100w, {{ asset('images/hero-1920.webp') }} 1920w"
                sizes="100vw">
            <img src="{{ asset('images/hero-1100.jpg') }}" alt="" width="1920" height="1280" fetchpriority="high"
                decoding="async">
        </picture>

        <div class="container position-relative">
            <div class="hero-content">
                <span class="hero-eyebrow"><i class="fas fa-location-dot me-2"></i>Résidences meublées · Abidjan</span>
                <h1 class="hero-title">Votre chez-vous <span class="text-gold-gradient">haut de gamme</span>, le temps d'un séjour</h1>
                <p class="hero-lead">Appartements et studios entièrement équipés, sécurisés et prêts à vous accueillir.
                    Réservez en quelques clics, nous nous occupons du reste.</p>

                <ul class="hero-trust list-unstyled">
                    <li><i class="fas fa-shield-halved"></i>Résidences vérifiées</li>
                    <li><i class="fas fa-headset"></i>Assistance 24h/24</li>
                    <li><i class="fas fa-lock"></i>Paiement sécurisé</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Recherche (chevauche le hero) --}}
    <div class="container hero-search">
        @include('frontend.partials.search-form', ['prefix' => 'hero'])
    </div>

    {{-- Chiffres clés --}}
    <section class="container">
        <div class="stats-strip" data-reveal>
            <div class="stat">
                <strong>{{ $stats['residences'] }}</strong>
                <span>{{ $stats['residences'] > 1 ? 'résidences disponibles' : 'résidence disponible' }}</span>
            </div>
            <div class="stat">
                <strong>{{ max($stats['communes'], 1) }}</strong>
                <span>{{ $stats['communes'] > 1 ? 'quartiers prisés' : 'quartier prisé' }}</span>
            </div>
            <div class="stat">
                <strong>24/7</strong>
                <span>assistance sur place</span>
            </div>
            <div class="stat">
                <strong><i class="fas fa-bolt"></i></strong>
                <span>réservation instantanée</span>
            </div>
        </div>
    </section>

    {{-- ================= COUPS DE CŒUR ================= --}}
    @if ($featuredResidences->count() > 0)
        <section class="section" id="residences-vedette">
            <div class="container">
                <div class="section-head d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                    <div data-reveal>
                        <span class="eyebrow">Sélection</span>
                        <h2 class="section-title">Nos coups de cœur</h2>
                        <p class="section-lead mb-0">Des adresses choisies pour leur emplacement, leur confort et la qualité
                            de leurs prestations.</p>
                    </div>
                    <a href="{{ route('residences.index') }}" class="btn btn-outline-primary flex-shrink-0" data-reveal>
                        Toutes les résidences<i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @foreach ($featuredResidences as $residence)
                        <div class="col-sm-6 col-lg-4" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 0.08 }}s">
                            @include('frontend.partials.residence-card', [
                                'residence' => $residence,
                                'eager' => $loop->index < 3,
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= PAR TYPE ================= --}}
    @if ($allResidenceTypes->count() > 0)
        <section class="section bg-sage-light" id="types">
            <div class="container">
                <div class="section-head text-center" data-reveal>
                    <span class="eyebrow">Explorer</span>
                    <h2 class="section-title">Trouvez le logement qui vous ressemble</h2>
                    <p class="section-lead mx-auto mb-0">Du studio cosy pour un voyage d'affaires à l'appartement familial,
                        choisissez le format adapté à votre séjour.</p>
                </div>

                <div class="type-tabs-wrap" data-reveal>
                    <ul class="nav type-tabs" role="tablist">
                        @foreach ($allResidenceTypes as $type)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="type-tab-{{ $type->id }}"
                                    data-bs-toggle="pill" data-bs-target="#type-pane-{{ $type->id }}" type="button"
                                    role="tab" aria-controls="type-pane-{{ $type->id }}"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ $type->name }}
                                    <span class="count">{{ $type->available_residences_count }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="tab-content">
                    @foreach ($allResidenceTypes as $type)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="type-pane-{{ $type->id }}"
                            role="tabpanel" aria-labelledby="type-tab-{{ $type->id }}" tabindex="0">
                            @if ($type->description)
                                <p class="text-center text-muted mb-4">{{ $type->description }}</p>
                            @endif
                            <div class="row g-4">
                                @foreach ($type->residences as $residence)
                                    <div class="col-sm-6 col-lg-4">
                                        @include('frontend.partials.residence-card', [
                                            'residence' => $residence,
                                            'showType' => false,
                                        ])
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-4 pt-2">
                                <a href="{{ route('residences.index', ['type' => $type->id]) }}" class="btn btn-green btn-lg">
                                    @if ($type->available_residences_count > $type->residences->count())
                                        Voir les {{ $type->available_residences_count }} résidences « {{ $type->name }} »
                                    @else
                                        Explorer la catégorie « {{ $type->name }} »
                                    @endif
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= COMMENT ÇA MARCHE ================= --}}
    <section class="section">
        <div class="container">
            <div class="section-head text-center" data-reveal>
                <span class="eyebrow">Simple et rapide</span>
                <h2 class="section-title">Réservez en 3 étapes</h2>
            </div>
            <div class="row g-4 steps">
                @foreach ([
                    ['icon' => 'fa-magnifying-glass', 'title' => 'Choisissez', 'text' => 'Indiquez vos dates et le nombre de voyageurs, puis comparez les résidences disponibles.'],
                    ['icon' => 'fa-calendar-check', 'title' => 'Réservez', 'text' => 'Vérifiez la disponibilité en temps réel et confirmez votre séjour en ligne.'],
                    ['icon' => 'fa-key', 'title' => 'Installez-vous', 'text' => 'Recevez votre confirmation par e-mail et profitez d\'un logement prêt à vivre.'],
                ] as $step)
                    <div class="col-md-4" data-reveal style="--reveal-delay: {{ $loop->index * 0.1 }}s">
                        <div class="step">
                            <span class="step-number">0{{ $loop->iteration }}</span>
                            <div class="step-icon"><i class="fas {{ $step['icon'] }}"></i></div>
                            <h3 class="h5">{{ $step['title'] }}</h3>
                            <p class="text-muted mb-0">{{ $step['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= POURQUOI ================= --}}
    <section class="section bg-sage-light" id="pourquoi">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5" data-reveal>
                    <span class="eyebrow">Pourquoi Sages Home</span>
                    <h2 class="section-title">Le confort d'un hôtel, la liberté d'un chez-soi</h2>
                    <p class="section-lead">Chaque résidence est visitée, équipée et entretenue par nos équipes pour vous
                        garantir un séjour sans mauvaise surprise.</p>
                    <a href="{{ route('residences.index') }}" class="btn btn-primary btn-lg mt-2">
                        Découvrir les résidences<i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3">
                        @foreach ([
                            ['icon' => 'fa-shield-halved', 'title' => 'Sécurité 24h/24', 'text' => 'Résidences sécurisées et propriétés vérifiées par nos soins.'],
                            ['icon' => 'fa-couch', 'title' => 'Tout équipé', 'text' => 'Wi-Fi, climatisation, cuisine équipée : il ne manque que vos valises.'],
                            ['icon' => 'fa-broom', 'title' => 'Ménage assuré', 'text' => 'Logements préparés et nettoyés avec soin avant chaque arrivée.'],
                            ['icon' => 'fa-headset', 'title' => 'Équipe disponible', 'text' => 'Une question pendant votre séjour ? Nous répondons jour et nuit.'],
                        ] as $feature)
                            <div class="col-sm-6" data-reveal style="--reveal-delay: {{ $loop->index * 0.08 }}s">
                                <div class="feature">
                                    <div class="feature-icon"><i class="fas {{ $feature['icon'] }}"></i></div>
                                    <div>
                                        <h3 class="h6 mb-1">{{ $feature['title'] }}</h3>
                                        <p class="text-muted small mb-0">{{ $feature['text'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= CONTACT ================= --}}
    <section class="section pb-0" id="contact">
        <div class="container">
            <div class="contact-card" data-reveal>
                <div class="row g-0">
                    <div class="col-lg-7 contact-main">
                        <span class="eyebrow text-gold-light">Contact</span>
                        <h2 class="section-title text-white">Parlons de votre prochain séjour</h2>
                        <p class="text-white-75 mb-4">Une demande particulière, un séjour longue durée ou un groupe ?
                            Notre équipe vous répond rapidement.</p>

                        <div class="row g-3">
                            @if ($parametre?->localisation)
                                <div class="col-sm-6">
                                    <div class="contact-item">
                                        <i class="fas fa-location-dot"></i>
                                        <div><span>Adresse</span>{{ $parametre->localisation }}</div>
                                    </div>
                                </div>
                            @endif
                            @php $tels = array_filter([$parametre?->contact1, $parametre?->contact2, $parametre?->contact3]); @endphp
                            @if (count($tels))
                                <div class="col-sm-6">
                                    <div class="contact-item">
                                        <i class="fas fa-phone"></i>
                                        <div>
                                            <span>Téléphone</span>
                                            @foreach ($tels as $tel)
                                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $tel) }}">{{ $tel }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-6">
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <span>E-mail</span>
                                        @foreach (array_unique(array_filter([$email, $parametre?->email2])) as $mail)
                                            <a href="mailto:{{ $mail }}">{{ $mail }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="contact-item">
                                    <i class="fas fa-clock"></i>
                                    <div><span>Disponibilité</span>7j/7, 24h/24</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 contact-aside">
                        <img src="{{ asset('images/logo-mark.webp') }}" alt="" width="72" height="72" loading="lazy" class="mb-3">
                        <h3 class="h4 mb-2">Prêt à réserver ?</h3>
                        <p class="mb-4">Consultez les disponibilités en temps réel et réservez en ligne.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('residences.index') }}" class="btn btn-green btn-lg">
                                <i class="fas fa-magnifying-glass me-2"></i>Voir les résidences
                            </a>
                            @if ($phone)
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="btn btn-outline-green btn-lg">
                                    <i class="fas fa-phone me-2"></i>Nous appeler
                                </a>
                            @else
                                <a href="mailto:{{ $email }}" class="btn btn-outline-green btn-lg">
                                    <i class="fas fa-envelope me-2"></i>Nous écrire
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* ---------- Hero ---------- */
        .home-hero {
            position: relative;
            min-height: clamp(520px, 78vh, 760px);
            display: flex;
            align-items: center;
            padding: 4rem 0 7rem;
            color: #fff;
            overflow: hidden;
            isolation: isolate;
        }
        .home-hero-bg,
        .home-hero-bg img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
        }
        .home-hero-bg img { animation: heroZoom 18s var(--ease) both; }
        .home-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            background:
                linear-gradient(90deg, rgba(22, 34, 16, .88) 0%, rgba(30, 46, 22, .7) 45%, rgba(30, 46, 22, .25) 100%),
                linear-gradient(0deg, rgba(22, 34, 16, .45), transparent 40%);
        }
        @keyframes heroZoom {
            from { transform: scale(1.08); }
            to { transform: scale(1); }
        }
        .hero-content { max-width: 640px; animation: heroIn .9s var(--ease) both; }
        @keyframes heroIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: none; }
        }
        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            padding: .45rem 1rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .22);
            -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
            font-size: .85rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
        }
        .hero-eyebrow i { color: var(--sage-gold-start); }
        .hero-title {
            color: #fff;
            font-size: clamp(2.1rem, 5.2vw, 3.75rem);
            font-weight: 700;
            line-height: 1.08;
            letter-spacing: -0.03em;
            margin-bottom: 1.25rem;
        }
        .text-gold-gradient {
            background: linear-gradient(135deg, #F7E39B, #F1D46E 40%, #D9B04A);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .text-gold-light { color: var(--sage-gold-start) !important; }
        .hero-lead {
            font-size: clamp(1.02rem, 1.6vw, 1.2rem);
            color: rgba(255, 255, 255, .88);
            max-width: 560px;
            margin-bottom: 1.75rem;
        }
        .hero-trust { display: flex; flex-wrap: wrap; gap: .5rem 1.5rem; margin: 0; font-size: .92rem; color: rgba(255, 255, 255, .9); }
        .hero-trust i { color: var(--sage-gold-start); margin-right: .45rem; }

        .hero-search {
            position: relative;
            z-index: 5;
            margin-top: -4.5rem;
        }

        /* ---------- Chiffres clés ---------- */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-top: 2.5rem;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--sage-border);
            background: #fff;
        }
        .stat { text-align: center; padding: .25rem; }
        .stat + .stat { border-left: 1px solid var(--sage-border); }
        .stat strong {
            display: block;
            font-family: var(--font-heading);
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 700;
            color: var(--sage-green);
            line-height: 1.1;
        }
        .stat strong i { color: var(--sage-gold-deep); font-size: .85em; }
        .stat span { font-size: .85rem; color: var(--sage-text-secondary); }
        @media (max-width: 767.98px) {
            .stats-strip { grid-template-columns: repeat(2, 1fr); padding: 1rem; }
            .stat:nth-child(3) { border-left: 0; }
            .stat:nth-child(n+3) { border-top: 1px solid var(--sage-border); padding-top: 1rem; }
        }

        /* ---------- Onglets types ---------- */
        .type-tabs-wrap { margin-bottom: 2rem; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
        .type-tabs-wrap::-webkit-scrollbar { display: none; }
        .type-tabs {
            display: inline-flex;
            flex-wrap: nowrap;
            gap: .4rem;
            padding: .4rem;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--sage-border);
            box-shadow: var(--shadow-xs);
            position: relative;
            left: 50%;
            transform: translateX(-50%);
        }
        @media (max-width: 767.98px) {
            .type-tabs { left: 0; transform: none; }
        }
        .type-tabs .nav-link {
            white-space: nowrap;
            border-radius: 999px;
            padding: .6rem 1.1rem;
            color: var(--sage-text);
            font-weight: 500;
            font-size: .93rem;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            transition: background .25s var(--ease), color .25s var(--ease);
        }
        .type-tabs .nav-link:hover { background: var(--sage-gold-soft); color: var(--sage-green-dark); }
        .type-tabs .nav-link.active { background: var(--sage-green); color: #fff; }
        .type-tabs .count {
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            font-weight: 600;
            background: var(--sage-bg-light);
            color: var(--sage-green-dark);
        }
        .type-tabs .nav-link.active .count { background: var(--sage-gold-start); }

        /* ---------- Étapes ---------- */
        .step {
            position: relative;
            height: 100%;
            padding: 2rem 1.75rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--sage-border);
            background: #fff;
            transition: transform .3s var(--ease), box-shadow .3s var(--ease);
        }
        .step:hover { transform: translateY(-4px); box-shadow: var(--shadow); }
        .step-number {
            position: absolute;
            top: 1.25rem;
            right: 1.5rem;
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 2.25rem;
            color: var(--sage-gold-soft);
            -webkit-text-stroke: 1px rgba(194, 155, 50, .35);
            line-height: 1;
        }
        .step-icon,
        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--sage-gradient-gold);
            color: var(--sage-green-dark);
            font-size: 1.3rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 8px 18px rgba(194, 155, 50, .25);
        }

        /* ---------- Atouts ---------- */
        .feature {
            display: flex;
            gap: 1rem;
            height: 100%;
            padding: 1.35rem;
            border-radius: var(--radius);
            background: #fff;
            border: 1px solid var(--sage-border);
            transition: transform .3s var(--ease), box-shadow .3s var(--ease);
        }
        .feature:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
        .feature-icon { width: 48px; height: 48px; flex-shrink: 0; margin-bottom: 0; font-size: 1.1rem; border-radius: 14px; }

        /* ---------- Contact ---------- */
        .contact-card { border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); }
        .contact-main { background: var(--sage-gradient-green); padding: clamp(2rem, 5vw, 3.5rem); }
        .contact-aside {
            background: var(--sage-gradient-gold);
            color: var(--sage-green-dark);
            padding: clamp(2rem, 5vw, 3.5rem);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .contact-aside h3 { color: var(--sage-green-dark); }
        .contact-aside .btn-outline-green { --bs-btn-border-color: rgba(38, 59, 29, .5); --bs-btn-color: var(--sage-green-dark); }
        .contact-item {
            display: flex;
            gap: .85rem;
            padding: 1rem;
            border-radius: var(--radius);
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .1);
            color: #fff;
            height: 100%;
            font-size: .93rem;
            word-break: break-word;
        }
        .contact-item > i {
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(241, 212, 110, .18);
            color: var(--sage-gold-start);
        }
        .contact-item span { display: block; font-size: .75rem; text-transform: uppercase; letter-spacing: .08em; color: rgba(255, 255, 255, .6); margin-bottom: .15rem; }
        .contact-item a { display: block; color: #fff; text-decoration: none; }
        .contact-item a:hover { color: var(--sage-gold-start); }

        @media (max-width: 991.98px) {
            .home-hero { padding: 3rem 0 6rem; min-height: 520px; }
            .home-hero::before {
                background: linear-gradient(180deg, rgba(22, 34, 16, .55) 0%, rgba(22, 34, 16, .85) 100%);
            }
            .hero-search { margin-top: -4rem; }
        }
    </style>
@endpush
