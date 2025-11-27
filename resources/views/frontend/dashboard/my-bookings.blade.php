@extends('layouts.app')

@section('title', 'Mes Réservations')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-green mb-0">Mes Réservations</h2>
                <a href="{{ route('residences.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-plus-lg me-2"></i>Nouvelle réservation
                </a>
            </div>
        </div>
    </div>

    @if($bookings->count() > 0)
        <div class="row">
            @foreach($bookings as $booking)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($booking->residence->primaryImage)
                            <img src="{{ Storage::url($booking->residence->primaryImage->image_path) }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" 
                                 alt="{{ $booking->residence->name }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                @switch($booking->status)
                                    @case('pending')
                                        <span class="badge bg-warning">En attente</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-success">Confirmée</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Annulée</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-info">Terminée</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                @endswitch
                            </div>
                            
                            <h5 class="card-title">{{ $booking->residence->name }}</h5>
                            <p class="text-muted mb-2">
                                <small>{{ $booking->residence->residenceType->name }}</small>
                            </p>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-calendar-event me-2 text-primary"></i>
                                    <small><strong>Arrivée :</strong> {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</small>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-calendar-x me-2 text-primary"></i>
                                    <small><strong>Départ :</strong> {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</small>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-people me-2 text-primary"></i>
                                    <small><strong>Voyageurs :</strong> {{ $booking->guests }} personne{{ $booking->guests > 1 ? 's' : '' }}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-moon me-2 text-primary"></i>
                                    <small><strong>Nuits :</strong> {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }}</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Total :</span>
                                    <span class="h5 text-gold mb-0">{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('booking.show', $booking) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Voir les détails
                                    </a>
                                    
                                    @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                        @if(\Carbon\Carbon::parse($booking->check_in)->isFuture())
                                            <form action="{{ route('booking.cancel', $booking) }}" method="POST" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                    <i class="bi bi-x-circle me-1"></i>Annuler
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">
                                <i class="bi bi-calendar-plus me-1"></i>
                                Réservé le {{ $booking->created_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="text-muted mb-3">Aucune réservation trouvée</h4>
                    <p class="text-muted mb-4">
                        Vous n'avez pas encore effectué de réservation.<br>
                        Découvrez nos magnifiques résidences et réservez dès maintenant !
                    </p>
                    <a href="{{ route('residences.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-house me-2"></i>Découvrir nos résidences
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.text-green {
    color: #2F4A33 !important;
}

.text-gold {
    color: #C29B32 !important;
}

.card {
    border: none;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.badge {
    font-size: 0.75rem;
}

.btn-outline-primary {
    border-color: #2F4A33;
    color: #2F4A33;
}

.btn-outline-primary:hover {
    background-color: #2F4A33;
    border-color: #2F4A33;
}

.btn-primary {
    background-color: #2F4A33;
    border-color: #2F4A33;
}

.btn-primary:hover {
    background-color: #1e3022;
    border-color: #1e3022;
}
</style>
@endsection