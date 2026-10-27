@extends('layouts.app')

@section('title', 'Résidences disponibles - Sages Home')
@section('meta_description', 'Résidences Sages Home disponibles pour vos dates de séjour à Abidjan.')

@php
    \Carbon\Carbon::setLocale('fr');
    $in = \Carbon\Carbon::parse($checkIn);
    $out = \Carbon\Carbon::parse($checkOut);
    $nights = $in->diffInDays($out);
    $search = ['check_in' => $checkIn, 'check_out' => $checkOut, 'guests' => $guests];
    $count = $availableResidences->count();
@endphp

@section('content')
    <section class="page-hero page-hero-compact">
        <div class="container">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-house" aria-hidden="true"></i><span class="visually-hidden">Accueil</span></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Recherche</li>
                </ol>
            </nav>
            <h1 class="mb-3">{{ $count > 0 ? ($count > 1 ? "$count résidences disponibles" : '1 résidence disponible') : 'Aucune disponibilité' }}</h1>
            <div class="search-summary">
                <span><i class="fas fa-calendar-day"></i>{{ $in->translatedFormat('D j M') }} → {{ $out->translatedFormat('D j M Y') }}</span>
                <span><i class="fas fa-moon"></i>{{ $nights }} {{ $nights > 1 ? 'nuits' : 'nuit' }}</span>
                <span><i class="fas fa-user-friends"></i>{{ $guests }} {{ $guests > 1 ? 'voyageurs' : 'voyageur' }}</span>
                @if (request('commune') || request('ville'))
                    <span><i class="fas fa-location-dot"></i>{{ ucwords(strtolower(request('commune') ?: request('ville'))) }}</span>
                @endif
                <button class="btn btn-sm btn-outline-primary rounded-pill" type="button" data-bs-toggle="collapse"
                    data-bs-target="#refineSearch" aria-expanded="false" aria-controls="refineSearch">
                    <i class="fas fa-pen me-1"></i>Modifier
                </button>
            </div>
        </div>
    </section>

    <div class="container py-4 py-lg-5">
        <div class="collapse mb-4" id="refineSearch">
            @include('frontend.partials.search-form', ['prefix' => 'results', 'values' => $search])
        </div>

        @if ($count > 0)
            <div class="row g-4">
                @foreach ($availableResidences as $residence)
                    <div class="col-sm-6 col-lg-4" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 0.08 }}s">
                        @include('frontend.partials.residence-card', [
                            'residence' => $residence,
                            'eager' => $loop->index < 3,
                            'search' => $search,
                        ])
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-calendar-xmark"></i></div>
                <h2 class="h4">Aucune résidence n'est libre sur ces dates</h2>
                <p class="text-muted mb-4 mx-auto" style="max-width: 480px">
                    Essayez de décaler vos dates, de réduire le nombre de voyageurs ou d'élargir la zone de recherche.
                    Notre équipe peut aussi vous aider à trouver une solution.
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#refineSearch">
                        <i class="fas fa-pen me-2"></i>Modifier ma recherche
                    </button>
                    <a href="{{ route('residences.index') }}" class="btn btn-outline-primary">Voir toutes les résidences</a>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .search-summary {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .5rem;
        }
        .search-summary > span {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .4rem .85rem;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--sage-border);
            font-size: .88rem;
            font-weight: 500;
        }
        .search-summary i { color: var(--sage-gold-deep); }
    </style>
@endpush
