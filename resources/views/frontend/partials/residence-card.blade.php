{{--
    Carte résidence partagée.
    @param \App\Models\Residence $residence
    @param bool  $eager   charger l'image immédiatement (cartes au-dessus de la ligne de flottaison)
    @param array $search  (optionnel) check_in, check_out, guests -> affiche le total du séjour
    @param bool  $showType afficher le badge du type
--}}
@php
    $eager = $eager ?? false;
    $search = $search ?? null;
    $showType = $showType ?? true;
    $url = route('residences.show', $residence->slug)
        . ($search ? '?' . http_build_query(array_filter($search)) : '');
    $location = collect([$residence->commune, $residence->ville])
        ->filter()
        ->map(fn ($s) => ucwords(strtolower($s)))
        ->implode(', ');
    $amenities = array_slice($residence->getFormattedAmenities(), 0, 3);
@endphp

<article class="residence-card">
    <div class="rc-media">
        @if ($residence->primaryImage)
            <img src="{{ $residence->primaryImage->thumb(800) }}" srcset="{{ $residence->primaryImage->srcset([480, 800]) }}"
                sizes="(max-width: 575px) 92vw, (max-width: 991px) 46vw, 380px" alt="{{ $residence->name }}"
                class="img-fade" width="800" height="600"
                loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async"
                @if ($eager) fetchpriority="high" @endif>
        @else
            <div class="rc-placeholder" aria-hidden="true">
                <img src="{{ asset('images/logo-mark.webp') }}" alt="" width="64" height="64">
            </div>
        @endif

        <div class="rc-badges">
            @if ($residence->is_featured)
                <span class="rc-badge rc-badge-gold"><i class="fas fa-star"></i>Coup de cœur</span>
            @endif
            @if ($showType && $residence->residenceType)
                <span class="rc-badge">{{ $residence->residenceType->name }}</span>
            @endif
        </div>

        @if ($location)
            <div class="rc-location"><i class="fas fa-map-marker-alt me-1"></i>{{ $location }}</div>
        @endif
    </div>

    <div class="rc-body">
        <h3 class="rc-title">
            <a href="{{ $url }}" class="stretched-link">{{ $residence->name }}</a>
        </h3>

        @if ($residence->description)
            <p class="rc-desc">{{ $residence->description }}</p>
        @endif

        <div class="rc-meta">
            <span><i class="fas fa-user-friends"></i>Jusqu'à {{ $residence->capacity }} {{ $residence->capacity > 1 ? 'voyageurs' : 'voyageur' }}</span>
        </div>

        @if (count($amenities))
            <div class="rc-amenities">
                @foreach ($amenities as $amenity)
                    <span class="chip"><i class="fas fa-check"></i>{{ Str::limit($amenity, 22) }}</span>
                @endforeach
            </div>
        @endif

        @if ($search && isset($residence->total_price))
            <div class="rc-total">
                <div class="d-flex justify-content-between text-muted">
                    <span>{{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA × {{ $residence->nights }} {{ $residence->nights > 1 ? 'nuits' : 'nuit' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-baseline mt-1">
                    <strong class="text-green">Total du séjour</strong>
                    <strong class="fs-6 text-green">{{ number_format($residence->total_price, 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        @endif

        <div class="rc-footer">
            <div>
                <span class="rc-price-label">{{ $search ? 'Par nuit' : 'À partir de' }}</span>
                <div class="rc-price">
                    {{ number_format($residence->price_per_night, 0, ',', ' ') }} <small>FCFA / nuit</small>
                </div>
                <span class="rc-price-eur">≈ {{ number_format(fcfa_to_eur($residence->price_per_night), 2, ',', ' ') }} €</span>
            </div>

            @if ($search)
                @auth
                    <a href="{{ route('booking.create', $residence) }}?{{ http_build_query($search) }}"
                        class="btn btn-primary rc-cta">Réserver</a>
                @else
                    <a href="{{ route('login') }}?redirect={{ urlencode($url) }}" class="btn btn-primary rc-cta">Réserver</a>
                @endauth
            @else
                <span class="rc-arrow" aria-hidden="true"><i class="fas fa-arrow-right"></i></span>
            @endif
        </div>
    </div>
</article>
