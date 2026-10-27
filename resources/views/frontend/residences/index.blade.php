@extends('layouts.app')

@php
    $currentType = $residenceTypes->firstWhere('id', request('type'));
    $filterKeys = ['ville', 'commune', 'capacity', 'min_price', 'max_price'];
    $activeFilters = collect(request()->only($filterKeys))->filter(fn ($v) => filled($v));
    $hasFilters = $activeFilters->isNotEmpty() || $currentType;
    $sort = request('sort', 'recommended');
    $nice = fn ($s) => ucwords(strtolower((string) $s));
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    // Libellés lisibles des filtres actifs (chips supprimables)
    $filterLabels = [
        'ville' => fn ($v) => $nice($v),
        'commune' => fn ($v) => $nice($v),
        'capacity' => fn ($v) => $v . ($v >= 8 ? '+' : '') . ' voyageur' . ($v > 1 ? 's' : ''),
        'min_price' => fn ($v) => 'Dès ' . $fmt($v) . ' FCFA',
        'max_price' => fn ($v) => 'Jusqu\'à ' . $fmt($v) . ' FCFA',
    ];
    // Lien vers la liste en conservant les autres paramètres
    $linkWith = fn (array $changes) => route('residences.index', array_filter(
        array_merge(request()->except('page'), $changes),
        fn ($v) => filled($v)
    ));
    $count = $residences->total();
@endphp

@section('title', ($currentType ? $currentType->name . ' meublés à Abidjan' : 'Nos résidences meublées à Abidjan') . ' | Sages Home')
@section('meta_description', $currentType?->description
    ? Str::limit($currentType->description, 155)
    : 'Parcourez toutes les résidences meublées Sages Home à Abidjan : studios et appartements équipés, filtrez par quartier, capacité et budget, et réservez en ligne.')

@section('content')
    {{-- En-tête compact --}}
    <section class="page-hero page-hero-compact">
        <div class="container">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-house" aria-hidden="true"></i><span class="visually-hidden">Accueil</span></a></li>
                    @if ($currentType)
                        <li class="breadcrumb-item"><a href="{{ route('residences.index') }}">Résidences</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $currentType->name }}</li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">Résidences</li>
                    @endif
                </ol>
            </nav>

            <div class="d-flex flex-wrap align-items-end justify-content-between gap-2">
                <div>
                    <h1 class="mb-1">{{ $currentType ? $currentType->name : 'Nos résidences' }}</h1>
                    <p class="text-muted mb-0">
                        {{ $currentType?->description ?: 'Logements meublés, équipés et sécurisés, sélectionnés avec soin.' }}
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="result-count" aria-live="polite">
                        <strong>{{ $count }}</strong> {{ $count > 1 ? 'résidences' : 'résidence' }}
                    </span>
                    @if ($count > 1)
                        <label for="sort" class="visually-hidden">Trier les résidences</label>
                        <select class="form-select form-select-sm sort-select" id="sort">
                            <option value="recommended" @selected($sort === 'recommended')>Recommandées</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>Prix croissant</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>Prix décroissant</option>
                            <option value="newest" @selected($sort === 'newest')>Nouveautés</option>
                        </select>
                    @endif
                </div>
            </div>

            {{-- Raccourcis par type --}}
            @if ($residenceTypes->where('available_residences_count', '>', 0)->count() > 1)
                <div class="type-chips" role="navigation" aria-label="Filtrer par type de logement">
                    <a href="{{ $linkWith(['type' => null]) }}" class="type-chip {{ !$currentType ? 'active' : '' }}"
                        @if (!$currentType) aria-current="true" @endif>
                        Toutes <span>{{ $totalAvailable }}</span>
                    </a>
                    @foreach ($residenceTypes as $type)
                        @if ($type->available_residences_count > 0 || $currentType?->id === $type->id)
                            <a href="{{ $linkWith(['type' => $type->id]) }}"
                                class="type-chip {{ $currentType?->id === $type->id ? 'active' : '' }}"
                                @if ($currentType?->id === $type->id) aria-current="true" @endif>
                                {{ $type->name }} <span>{{ $type->available_residences_count }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <div class="container py-4">
        {{-- Filtres --}}
        <div class="filters-card mb-3">
            <button class="filters-toggle d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#filtersBody"
                aria-expanded="{{ $activeFilters->isNotEmpty() ? 'true' : 'false' }}" aria-controls="filtersBody">
                <span><i class="fas fa-sliders me-2"></i>Affiner la recherche
                    @if ($activeFilters->isNotEmpty())
                        <span class="badge rounded-pill bg-green ms-1">{{ $activeFilters->count() }}</span>
                    @endif
                </span>
                <i class="fas fa-chevron-down toggle-chevron"></i>
            </button>

            <div class="collapse d-lg-block {{ $activeFilters->isNotEmpty() ? 'show' : '' }}" id="filtersBody">
                <form method="GET" action="{{ route('residences.index') }}" class="filters-form" data-search-form>
                    @foreach (['type', 'sort'] as $keep)
                        @if (filled(request($keep)))
                            <input type="hidden" name="{{ $keep }}" value="{{ request($keep) }}">
                        @endif
                    @endforeach
                    <div class="row g-2 g-lg-3 align-items-end">
                        <div class="col-6 col-md-3 col-lg">
                            <label for="ville" class="form-label">Ville</label>
                            <select class="form-select" id="ville" name="ville">
                                <option value="">Toutes</option>
                                @foreach ($availableVilles as $ville)
                                    <option value="{{ $ville }}" @selected(request('ville') == $ville)>{{ $nice($ville) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3 col-lg">
                            <label for="commune" class="form-label">Commune</label>
                            <select class="form-select" id="commune" name="commune" data-selected="{{ request('commune') }}">
                                <option value="">Toutes</option>
                                @if (request('commune'))
                                    <option value="{{ request('commune') }}" selected>{{ $nice(request('commune')) }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-12 col-md-3 col-lg">
                            <label for="capacity" class="form-label">Voyageurs</label>
                            <select class="form-select" id="capacity" name="capacity">
                                <option value="">Peu importe</option>
                                @foreach ([1, 2, 4, 6, 8] as $cap)
                                    <option value="{{ $cap }}" @selected(request('capacity') == $cap)>
                                        {{ $cap === 1 ? '1 voyageur' : ($cap === 8 ? '8 voyageurs et +' : "Au moins $cap voyageurs") }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3 col-lg-3">
                            <label class="form-label" for="min_price">Budget / nuit</label>
                            <div class="input-group price-group">
                                <input type="number" class="form-control" id="min_price" name="min_price"
                                    value="{{ request('min_price') }}" placeholder="Min" min="0" step="1000" inputmode="numeric">
                                <span class="input-group-text">à</span>
                                <input type="number" class="form-control" id="max_price" name="max_price"
                                    value="{{ request('max_price') }}" placeholder="Max" min="0" step="1000" inputmode="numeric"
                                    aria-label="Budget maximum par nuit">
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-auto">
                            <button type="submit" class="btn btn-primary w-100 px-4" style="min-height:46px">
                                <i class="fas fa-search me-2"></i>Filtrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Filtres actifs (supprimables) --}}
        @if ($activeFilters->isNotEmpty())
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                @foreach ($activeFilters as $key => $value)
                    <a href="{{ $linkWith([$key => null] + ($key === 'ville' ? ['commune' => null] : [])) }}" class="active-filter"
                        aria-label="Retirer le filtre {{ $filterLabels[$key]($value) }}">
                        {{ $filterLabels[$key]($value) }} <i class="fas fa-xmark"></i>
                    </a>
                @endforeach
                @if ($activeFilters->count() > 1)
                    <a href="{{ route('residences.index', array_filter(['type' => request('type'), 'sort' => request('sort')])) }}"
                        class="small fw-semibold ms-1">Tout effacer</a>
                @endif
            </div>
        @endif

        {{-- Grille --}}
        @if ($residences->count() > 0)
            <div class="row g-4">
                @foreach ($residences as $residence)
                    <div class="col-sm-6 col-lg-4" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 0.08 }}s">
                        @include('frontend.partials.residence-card', [
                            'residence' => $residence,
                            'eager' => $loop->index < 3,
                            'showType' => !$currentType,
                        ])
                    </div>
                @endforeach
            </div>

            @if ($residences->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    {{ $residences->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-house-circle-xmark"></i></div>
                <h2 class="h4">{{ $hasFilters ? 'Aucune résidence ne correspond à vos critères' : 'Aucune résidence disponible pour le moment' }}</h2>
                <p class="text-muted mb-4 mx-auto" style="max-width: 460px">
                    {{ $hasFilters ? 'Essayez d\'élargir votre budget, de changer de quartier ou de réduire le nombre de voyageurs.' : 'De nouvelles résidences arrivent bientôt. Contactez-nous pour une recherche personnalisée.' }}
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    @if ($hasFilters)
                        <a href="{{ route('residences.index') }}" class="btn btn-primary"><i class="fas fa-rotate-left me-2"></i>Voir toutes les résidences</a>
                    @endif
                    <a href="{{ route('home') }}#contact" class="btn btn-outline-primary"><i class="fas fa-envelope me-2"></i>Nous contacter</a>
                </div>
            </div>
        @endif

        {{-- CTA --}}
        <div class="cta-band mt-5 text-center text-md-start" data-reveal>
            <div class="row align-items-center g-4">
                <div class="col-md-8">
                    <h2 class="h3 mb-2">Vous ne trouvez pas votre bonheur ?</h2>
                    <p class="text-white-75 mb-0">Dites-nous ce que vous recherchez : notre équipe vous propose une résidence adaptée
                        à vos dates et à votre budget.</p>
                </div>
                <div class="col-md-4 d-flex flex-column flex-sm-row flex-md-column gap-2 justify-content-md-end">
                    @if ($parametre?->contact2 || $parametre?->contact1)
                        @php $tel = $parametre->contact1 ?: $parametre->contact2; @endphp
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $tel) }}" class="btn btn-primary btn-lg"><i class="fas fa-phone me-2"></i>Appelez-nous</a>
                    @endif
                    <a href="mailto:{{ $parametre?->email1 ?: 'infos@sageshome.ci' }}" class="btn btn-outline-light btn-lg"><i class="fas fa-envelope me-2"></i>Écrivez-nous</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .result-count {
            display: inline-flex;
            align-items: baseline;
            gap: .35rem;
            padding: .4rem .9rem;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--sage-border);
            font-size: .9rem;
            color: var(--sage-text-secondary);
            white-space: nowrap;
        }
        .result-count strong { color: var(--sage-green); font-size: 1rem; }

        .type-chips {
            display: flex;
            gap: .5rem;
            margin-top: 1.1rem;
            overflow-x: auto;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 2px;
        }
        .type-chips::-webkit-scrollbar { display: none; }
        .type-chip {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem .95rem;
            border-radius: 999px;
            border: 1px solid var(--sage-border);
            background: #fff;
            color: var(--sage-text);
            font-size: .9rem;
            font-weight: 500;
            text-decoration: none;
            transition: background .2s var(--ease), color .2s var(--ease), border-color .2s var(--ease);
        }
        .type-chip span {
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--sage-bg-light);
            font-size: .72rem;
            font-weight: 600;
            color: var(--sage-green-dark);
        }
        .type-chip:hover { border-color: var(--sage-gold-end); color: var(--sage-green-dark); }
        .type-chip.active { background: var(--sage-green); border-color: var(--sage-green); color: #fff; }
        .type-chip.active span { background: var(--sage-gold-start); }

        .filters-card {
            background: #fff;
            border: 1px solid var(--sage-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .filters-form { padding: 1rem 1.25rem 1.15rem; }
        .filters-toggle {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .9rem 1.25rem;
            background: none;
            border: 0;
            font-weight: 600;
            color: var(--sage-green-dark);
        }
        .filters-toggle .toggle-chevron { transition: transform .25s var(--ease); }
        .filters-toggle[aria-expanded="true"] .toggle-chevron { transform: rotate(180deg); }
        @media (max-width: 991.98px) { .filters-form { padding-top: 0; } }

        .price-group .input-group-text {
            background: var(--sage-bg-light);
            border-color: var(--sage-border);
            color: var(--sage-text-secondary);
            font-size: .8rem;
            padding: 0 .6rem;
        }
        .price-group .form-control { min-width: 0; }

        .active-filter {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .35rem .75rem;
            border-radius: 999px;
            background: var(--sage-gold-soft);
            border: 1px solid rgba(194, 155, 50, .35);
            color: var(--sage-green-dark);
            font-size: .84rem;
            font-weight: 500;
            text-decoration: none;
        }
        .active-filter i { font-size: .75rem; opacity: .7; }
        .active-filter:hover { background: #F6E9C2; color: var(--sage-green-dark); }
        .active-filter:hover i { opacity: 1; }
        .sort-select { min-height: 38px; width: auto; min-width: 170px; border-radius: 999px; font-size: .88rem; }
        .result-count { min-height: 38px; }
        .filters-card + .row { margin-top: .5rem; }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sortSelect = document.getElementById('sort');
            if (sortSelect) {
                sortSelect.addEventListener('change', function () {
                    var url = new URL(window.location.href);
                    url.searchParams.set('sort', this.value);
                    url.searchParams.delete('page');
                    var bar = document.getElementById('page-progress');
                    if (bar) bar.classList.add('is-loading');
                    window.location.href = url.toString();
                });
            }
        });
    </script>
@endpush
