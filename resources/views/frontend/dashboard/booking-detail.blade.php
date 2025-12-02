@extends('layouts.app')

@section('title', 'Détail de la réservation - Sages Home')

@section('content')
<div class="container py-5">
    <!-- En-tête avec titre et actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="text-primary mb-0">Réservation #{{ $booking->id }}</h2>
                    <p class="text-muted mb-0">Détails complets de votre réservation</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('dashboard.bookings') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Retour à la liste
                    </a>
                    @if($booking->status === 'pending' || $booking->status === 'confirmed')
                        @if(\Carbon\Carbon::parse($booking->check_in)->isFuture())
                            <form action="{{ route('dashboard.booking.cancel', $booking) }}" method="POST" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i>Annuler
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

<div class="row">
    <!-- Informations principales -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Détails de la réservation
                    </h5>
                    @switch($booking->status)
                        @case('pending')
                            <span class="badge bg-warning fs-6">En attente</span>
                            @break
                        @case('confirmed')
                            <span class="badge bg-success fs-6">Confirmée</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-danger fs-6">Annulée</span>
                            @break
                        @case('completed')
                            <span class="badge bg-info fs-6">Terminée</span>
                            @break
                    @endswitch
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Image de la résidence -->
                    <div class="col-md-5 mb-3">
                        @if($booking->residence->primaryImage)
                            <img src="{{ Storage::url($booking->residence->primaryImage->image_path) }}" 
                                 class="img-fluid rounded shadow" 
                                 style=\"height: 250px; width: 100%; object-fit: cover;\" 
                                 alt="{{ $booking->residence->name }}">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center shadow" 
                                 style="height: 250px;">
                                <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Informations de la résidence -->
                    <div class="col-md-7">
                        <h4 class="text-primary mb-2">{{ $booking->residence->name }}</h4>
                        <p class="text-muted mb-3">{{ $booking->residence->residenceType->name }}</p>
                        
                        @if($booking->residence->description)
                            <p class="mb-3">{{ Str::limit($booking->residence->description, 150) }}</p>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    <span>{{ $booking->residence->address }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-people text-primary me-2"></i>
                                    <span>Jusqu'à {{ $booking->residence->residenceType->capacity }} personne{{ $booking->residence->residenceType->capacity > 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-currency-exchange text-primary me-2"></i>
                                    <span>{{ number_format($booking->residence->price_per_night, 0, ',', ' ') }} FCFA/nuit</span>
                                </div>
                                @if($booking->residence->residenceType->bedrooms)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-door-closed text-primary me-2"></i>
                                        <span>{{ $booking->residence->residenceType->bedrooms }} chambre{{ $booking->residence->residenceType->bedrooms > 1 ? 's' : '' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Récapitulatif de la réservation -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>Récapitulatif
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Arrivée</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</div>
                            <div class="text-muted small">{{ \Carbon\Carbon::parse($booking->check_in)->format('l') }}</div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Départ</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</div>
                            <div class="text-muted small">{{ \Carbon\Carbon::parse($booking->check_out)->format('l') }}</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Durée</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }} nuit{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) > 1 ? 's' : '' }}</div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Voyageurs</label>
                            <div class="fw-bold">{{ $booking->guests }} personne{{ $booking->guests > 1 ? 's' : '' }}</div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Détails financiers -->
                <div class="mb-4">
                    <h6 class="text-primary mb-3">Détails financiers</h6>
                    
                    @php
                        $nights = \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out));
                        $pricePerNight = $booking->residence->price_per_night;
                        $subtotal = $nights * $pricePerNight;
                        $taxes = $booking->total_price - $subtotal;
                    @endphp
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ number_format($pricePerNight, 0, ',', ' ') }} FCFA × {{ $nights }} nuit{{ $nights > 1 ? 's' : '' }}</span>
                        <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    
                    @if($taxes > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Taxes et frais</span>
                            <span>{{ number_format($taxes, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-0">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold h5 text-primary">{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                
                <div class="small text-muted">
                    <i class="bi bi-calendar-plus me-1"></i>
                    Réservé le {{ $booking->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informations complémentaires -->
@if($booking->special_requests || $booking->notes)
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-text me-2"></i>Informations complémentaires
                    </h5>
                </div>
                <div class="card-body">
                    @if($booking->special_requests)
                        <div class="mb-3">
                            <h6>Demandes spéciales</h6>
                            <p class="mb-0">{{ $booking->special_requests }}</p>
                        </div>
                    @endif
                    
                    @if($booking->notes)
                        <div class="mb-0">
                            <h6>Notes</h6>
                            <p class="mb-0">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

</div> <!-- Fermeture du container -->
@endsection