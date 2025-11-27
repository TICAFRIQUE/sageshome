@extends('frontend.layouts.dashboard')

@section('title', 'Détail de la réservation - Sages Home')
@section('page-title', 'Réservation #' . $booking->id)
@section('page-subtitle', 'Détails complets de votre réservation')

@section('page-actions')
    <div class="btn-group">
        @if($booking->status === 'pending' || $booking->status === 'confirmed')
            @if(\Carbon\Carbon::parse($booking->check_in)->isFuture())
                <form action="{{ route('dashboard.booking.cancel', $booking) }}" method="POST" 
                      onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle me-1"></i>Annuler la réservation
                    </button>
                </form>
            @endif
        @endif
        <a href="{{ route('dashboard.bookings') }}" class="btn btn-sage-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour à la liste
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Informations principales -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-sage-accent">
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
                        <h4 class="text-sage-accent mb-2">{{ $booking->residence->name }}</h4>
                        <p class="text-muted mb-3">{{ $booking->residence->residenceType->name }}</p>
                        
                        @if($booking->residence->description)
                            <p class="mb-3">{{ Str::limit($booking->residence->description, 150) }}</p>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-geo-alt text-sage-accent me-2"></i>
                                    <span>{{ $booking->residence->location }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-people text-sage-accent me-2"></i>
                                    <span>Jusqu'à {{ $booking->residence->residenceType->capacity }} personne{{ $booking->residence->residenceType->capacity > 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-currency-exchange text-sage-accent me-2"></i>
                                    <span>{{ number_format($booking->residence->price_per_night, 0, ',', ' ') }} FCFA/nuit</span>
                                </div>
                                @if($booking->residence->residenceType->bedrooms)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-door-closed text-sage-accent me-2"></i>
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
        <div class="dashboard-card">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="mb-0 text-sage-accent">
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
                    <h6 class="text-sage-accent mb-3">Détails financiers</h6>
                    
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
                        <span class="fw-bold h5 text-sage-secondary">{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                
                <div class="small text-muted\">\n                    <i class=\"bi bi-calendar-plus me-1\"></i>\n                    Réservé le {{ $booking->created_at->format('d/m/Y à H:i') }}\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n\n<!-- Informations complémentaires -->\n@if($booking->special_requests || $booking->notes)\n    <div class=\"row\">\n        <div class=\"col-12 mb-4\">\n            <div class=\"dashboard-card\">\n                <div class=\"card-header bg-transparent border-bottom\">\n                    <h5 class=\"mb-0 text-sage-accent\">\n                        <i class=\"bi bi-chat-text me-2\"></i>Informations complémentaires\n                    </h5>\n                </div>\n                <div class=\"card-body\">\n                    @if($booking->special_requests)\n                        <div class=\"mb-3\">\n                            <h6 class=\"text-sage-accent\">Demandes spéciales</h6>\n                            <p class=\"mb-0\">{{ $booking->special_requests }}</p>\n                        </div>\n                    @endif\n                    \n                    @if($booking->notes)\n                        <div class=\"mb-0\">\n                            <h6 class=\"text-sage-accent\">Notes</h6>\n                            <p class=\"mb-0\">{{ $booking->notes }}</p>\n                        </div>\n                    @endif\n                </div>\n            </div>\n        </div>\n    </div>\n@endif\n\n<!-- Contact et assistance -->\n<div class=\"row\">\n    <div class=\"col-12\">\n        <div class=\"dashboard-card\">\n            <div class=\"card-header bg-transparent border-bottom\">\n                <h5 class=\"mb-0 text-sage-accent\">\n                    <i class=\"bi bi-headset me-2\"></i>Besoin d'aide ?\n                </h5>\n            </div>\n            <div class=\"card-body\">\n                <div class=\"row\">\n                    <div class=\"col-md-4 text-center mb-3\">\n                        <i class=\"bi bi-telephone text-sage-accent mb-2\" style=\"font-size: 2rem;\"></i>\n                        <h6>Appelez-nous</h6>\n                        <p class=\"mb-0\">+221 XX XXX XX XX</p>\n                    </div>\n                    <div class=\"col-md-4 text-center mb-3\">\n                        <i class=\"bi bi-envelope text-sage-accent mb-2\" style=\"font-size: 2rem;\"></i>\n                        <h6>Écrivez-nous</h6>\n                        <p class=\"mb-0\">contact@sageshome.com</p>\n                    </div>\n                    <div class=\"col-md-4 text-center mb-3\">\n                        <i class=\"bi bi-chat-dots text-sage-accent mb-2\" style=\"font-size: 2rem;\"></i>\n                        <h6>Chat en ligne</h6>\n                        <p class=\"mb-0\">Disponible 24h/24</p>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n@endsection