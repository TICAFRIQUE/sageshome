@extends('layouts.app')

@section('title', 'Résultats de recherche - Sages Home')

@section('content')
<div class="container py-5">
    <!-- Search Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-green text-center mb-3">Résultats de recherche</h1>
            <div class="text-center text-muted">
                Du {{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }} 
                au {{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }} 
                • {{ $guests }} {{ $guests > 1 ? 'voyageurs' : 'voyageur' }}
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="search-form mx-auto" style="max-width: 800px;">
                <form method="GET" action="{{ route('search') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="check_in" class="form-label fw-semibold">Date d'arrivée</label>
                        <input type="date" class="form-control" id="check_in" name="check_in" 
                               value="{{ $checkIn }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="check_out" class="form-label fw-semibold">Date de départ</label>
                        <input type="date" class="form-control" id="check_out" name="check_out" 
                               value="{{ $checkOut }}" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="guests" class="form-label fw-semibold">Voyageurs</label>
                        <select class="form-control" id="guests" name="guests" required>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $guests == $i ? 'selected' : '' }}>
                                    {{ $i }} {{ $i === 1 ? 'voyageur' : 'voyageurs' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Rechercher
                        </button>
                    </div>

                    <!-- Additional Filters -->
                    <div class="col-md-4">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-control">
                            <option value="">Tous les types</option>
                            @foreach($residenceTypes as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="min_price" class="form-label">Prix min (FCFA)</label>
                        <input type="number" name="min_price" id="min_price" class="form-control" 
                               value="{{ request('min_price') }}" placeholder="0">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="max_price" class="form-label">Prix max (FCFA)</label>
                        <input type="number" name="max_price" id="max_price" class="form-control" 
                               value="{{ request('max_price') }}" placeholder="999999">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results -->
    @if($availableResidences->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="text-green">{{ $availableResidences->count() }} résidence(s) disponible(s)</h4>
            </div>
        </div>

        <div class="row g-4">
            @foreach($availableResidences as $residence)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 position-relative">
                        @if($residence->is_featured)
                            <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                <span class="badge badge-gold">Vedette</span>
                            </div>
                        @endif

                        @if($residence->primaryImage)
                            <img src="{{ $residence->primaryImage->full_url }}" 
                                 class="card-img-top" 
                                 alt="{{ $residence->name }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2">{{ $residence->name }}</h5>
                            
                            <p class="text-muted small mb-2">
                                <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($residence->address, 40) }}
                            </p>
                            
                            <p class="card-text text-muted mb-3">{{ Str::limit($residence->description, 100) }}</p>
                            
                            <!-- Amenities -->
                            @if($residence->amenities && count($residence->amenities) > 0)
                            <div class="mb-2">
                                    @foreach(array_slice($residence->getFormattedAmenities(), 0, 3) as $amenity)
                                        <span class="badge bg-light text-dark me-1">{{ $amenity }}</span>
                                    @endforeach
                                    @if(count($residence->amenities) > 3)
                                        <span class="badge bg-secondary">+{{ count($residence->amenities) - 3 }}</span>
                                    @endif
                            </div>
                            @endif
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">
                                    <i class="bi bi-people me-1"></i>{{ $residence->capacity }} voyageurs
                                </span>
                                <span class="badge bg-light text-dark">{{ $residence->type_display }}</span>
                            </div>
                            
                            <!-- Price Information -->
                            <div class="mt-auto">
                                <div class="price-info bg-light p-3 rounded mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">{{ number_format($residence->price_per_night, 0 , ',', ' ') }} FCFA x {{ $residence->nights }} nuits</span>
                                        <span class="small">{{ number_format($residence->total_price, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    {{-- <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Taxes (10%)</span>
                                        <span class="small">{{ number_format($residence->total_price * 0, 0 , ',', ' ' ) }} FCFA</span>
                                    </div> --}}
                                    <hr class="my-1">
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total</span>
                                        <span class="text-gold">{{ number_format($residence->total_price * 1 , 0 , ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('residences.show', $residence->slug) }}" class="btn btn-outline-primary flex-fill">
                                        Voir détails
                                    </a>
                                    
                                    @auth
                                        <a href="{{ route('booking.create', $residence) }}?check_in={{ $checkIn }}&check_out={{ $checkOut }}&guests={{ $guests }}" 
                                           class="btn btn-green flex-fill">
                                            Réserver
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-green flex-fill">
                                            Réserver
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- No Results -->
        <div class="row">
            <div class="col-12 text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted mb-3">Aucune résidence disponible</h4>
                <p class="text-muted mb-4">
                    Désolé, aucune résidence n'est disponible pour vos dates de voyage. 
                    <br>Essayez avec d'autres dates ou critères.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        Nouvelle recherche
                    </a>
                    <a href="{{ route('residences.index') }}" class="btn btn-outline-primary">
                        Voir toutes les résidences
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation des dates
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    
    checkInInput.addEventListener('change', function() {
        const checkInDate = new Date(this.value);
        const nextDay = new Date(checkInDate);
        nextDay.setDate(checkInDate.getDate() + 1);
        
        checkOutInput.min = nextDay.toISOString().split('T')[0];
        
        if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
            checkOutInput.value = nextDay.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush