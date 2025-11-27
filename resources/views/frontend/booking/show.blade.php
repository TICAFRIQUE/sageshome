@extends('frontend.layouts.app')

@section('title', 'Détails de la Réservation')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('booking.my-bookings') }}">Mes Réservations</a></li>
                    <li class="breadcrumb-item active">Réservation #{{ $booking->id }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Statut de la réservation -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="text-green mb-0">Réservation #{{ $booking->id }}</h4>
                        @switch($booking->status)
                            @case('pending')
                                <span class="badge bg-warning fs-6">En attente de confirmation</span>
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
                            @default
                                <span class="badge bg-secondary fs-6">{{ ucfirst($booking->status) }}</span>
                        @endswitch
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar-plus me-2"></i>
                        Réservation effectuée le {{ $booking->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>
            </div>

            <!-- Informations sur la résidence -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="text-green mb-0">Résidence réservée</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($booking->residence->primaryImage)
                                <img src="{{ Storage::url($booking->residence->primaryImage->image_path) }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $booking->residence->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $booking->residence->name }}</h4>
                            <p class="text-muted mb-2">{{ $booking->residence->residenceType->name }}</p>
                            <p class="mb-3">{{ $booking->residence->description }}</p>
                            
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Capacité :</strong> {{ $booking->residence->capacity }} personnes</p>
                                    <p class="mb-1"><strong>Adresse :</strong> {{ $booking->residence->address }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Prix/nuit :</strong> {{ number_format($booking->residence->price_per_night, 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                            
                            <a href="{{ route('residences.show', $booking->residence->slug) }}" 
                               class="btn btn-outline-primary btn-sm mt-2">
                                <i class="bi bi-eye me-1"></i>Voir la résidence
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails du séjour -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="text-green mb-0">Détails du séjour</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Dates</label>
                                <p class="mb-0">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    Du {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }} 
                                    au {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}
                                </p>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }} nuit(s)
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Voyageurs</label>
                                <p class="mb-0">
                                    <i class="bi bi-people me-2"></i>
                                    {{ $booking->guests }} personne{{ $booking->guests > 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($booking->special_requests)
                        <div class="mb-3">
                            <label class="fw-bold text-muted">Demandes spéciales</label>
                            <p class="mb-0">{{ $booking->special_requests }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations de contact -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="text-green mb-0">Informations de contact</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Nom :</strong> {{ $booking->first_name }} {{ $booking->last_name }}</p>
                            <p class="mb-1"><strong>Email :</strong> {{ $booking->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Téléphone :</strong> {{ $booking->phone }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des paiements -->
            @if($booking->payments->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="text-green mb-0">Historique des paiements</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Référence</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($booking->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                @switch($payment->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">En attente</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success">Complété</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-danger">Échoué</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td><small>{{ $payment->transaction_reference ?? 'N/A' }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Résumé financier -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Résumé financier</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }} nuit(s) × {{ number_format($booking->residence->price_per_night, 0, ',', ' ') }} FCFA</span>
                        <span>{{ number_format($booking->subtotal ?? ($booking->total_price * 0.9), 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frais de service</span>
                        <span>{{ number_format($booking->service_fee ?? ($booking->total_price * 0.1), 0, ',', ' ') }} FCFA</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span class="text-gold">{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="text-green mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($booking->status === 'pending' || $booking->status === 'confirmed')
                            @if(\Carbon\Carbon::parse($booking->check_in)->isFuture())
                                <form action="{{ route('booking.cancel', $booking) }}" method="POST" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-x-circle me-2"></i>Annuler la réservation
                                    </button>
                                </form>
                            @endif
                        @endif
                        
                        <a href="{{ route('booking.my-bookings') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Retour à mes réservations
                        </a>
                        
                        <a href="{{ route('residences.index') }}" class="btn btn-primary">
                            <i class="bi bi-house me-2"></i>Nouvelles réservations
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="text-green mb-0">Besoin d'aide ?</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Notre équipe est à votre disposition pour toute question concernant votre réservation.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="tel:+225000000000" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-telephone me-2"></i>Nous appeler
                        </a>
                        <a href="mailto:contact@sageshome.ci" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-envelope me-2"></i>Nous écrire
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.8rem;
}

.btn-primary {
    background-color: #2F4A33;
    border-color: #2F4A33;
}

.btn-primary:hover {
    background-color: #1e3022;
    border-color: #1e3022;
}

.btn-outline-primary {
    border-color: #2F4A33;
    color: #2F4A33;
}

.btn-outline-primary:hover {
    background-color: #2F4A33;
    border-color: #2F4A33;
}

.border-primary {
    border-color: #2F4A33 !important;
}

.bg-primary {
    background-color: #2F4A33 !important;
}
</style>
@endsection