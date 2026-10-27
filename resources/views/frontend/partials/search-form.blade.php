{{--
    Formulaire de recherche partagé.
    @param string $prefix   préfixe unique des id (ex: 'hero', 'panel')
    @param array  $values   valeurs pré-remplies (check_in, check_out, guests, ville, commune)
--}}
@php
    $prefix = $prefix ?? 'search';
    $values = $values ?? [];
    $villes = array_keys(config('ville-commune', []));
    $v = fn ($key) => $values[$key] ?? request($key);
@endphp

<form class="search-box" method="GET" action="{{ route('search') }}" data-search-form role="search"
    aria-label="Rechercher une résidence">
    @foreach (['type', 'min_price', 'max_price'] as $keep)
        @if (filled(request($keep)) && request()->routeIs('search'))
            <input type="hidden" name="{{ $keep }}" value="{{ request($keep) }}">
        @endif
    @endforeach
    <div class="row g-2 g-lg-0 align-items-stretch">
        <div class="col-6 col-lg">
            <div class="search-field h-100">
                <label class="form-label" for="{{ $prefix }}_check_in"><i class="fas fa-calendar-day"></i>Arrivée</label>
                <input type="date" class="form-control" id="{{ $prefix }}_check_in" name="check_in"
                    value="{{ $v('check_in') }}" min="{{ date('Y-m-d') }}" required>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="search-field h-100">
                <label class="form-label" for="{{ $prefix }}_check_out"><i class="fas fa-calendar-check"></i>Départ</label>
                <input type="date" class="form-control" id="{{ $prefix }}_check_out" name="check_out"
                    value="{{ $v('check_out') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
            </div>
        </div>
        <div class="col-12 col-sm-4 col-lg">
            <div class="search-field h-100">
                <label class="form-label" for="{{ $prefix }}_guests"><i class="fas fa-user-friends"></i>Voyageurs</label>
                <select class="form-select" id="{{ $prefix }}_guests" name="guests" required>
                    @for ($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}" @selected((int) ($v('guests') ?: 2) === $i)>
                            {{ $i }} {{ $i > 1 ? 'voyageurs' : 'voyageur' }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-lg">
            <div class="search-field h-100">
                <label class="form-label" for="{{ $prefix }}_ville"><i class="fas fa-city"></i>Ville</label>
                <select class="form-select" id="{{ $prefix }}_ville" name="ville">
                    <option value="">Toutes</option>
                    @foreach ($villes as $ville)
                        <option value="{{ $ville }}" @selected($v('ville') === $ville)>{{ ucwords(strtolower($ville)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-lg">
            <div class="search-field h-100">
                <label class="form-label" for="{{ $prefix }}_commune"><i class="fas fa-map-marker-alt"></i>Commune</label>
                <select class="form-select" id="{{ $prefix }}_commune" name="commune" data-selected="{{ $v('commune') }}">
                    <option value="">Toutes</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-lg-auto ps-lg-2">
            <button type="submit" class="btn btn-primary btn-search w-100 px-4">
                <i class="fas fa-search me-2"></i>Rechercher
            </button>
        </div>
    </div>
</form>
