@extends('backend.layouts.master')

@section('title', 'Détails de la Réservation')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Réservation {{ $booking->booking_number }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Réservations</a></li>
                    <li class="breadcrumb-item active">{{ $booking->booking_number }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-8">
        <!-- Informations de la réservation -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Détails de la réservation</h4>
                    </div>
                    <div class="col-auto">
                        @switch($booking->status)
                            @case('pending')
                                <span class="badge bg-warning fs-12">En attente</span>
                                @break
                            @case('confirmed')
                                <span class="badge bg-success fs-12">Confirmée</span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-danger fs-12">Annulée</span>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <td class="fw-medium">Référence :</td>
                                    <td>{{ $booking->booking_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Résidence :</td>
                                    <td>
                                        <a href="{{ route('admin.residences.show', $booking->residence) }}" class="text-primary">
                                            {{ $booking->residence->name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Dates :</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}
                                        <small class="text-muted">
                                            ({{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }} nuits)
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Voyageurs :</td>
                                    <td>{{ $booking->guests_count }} personne(s)</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Réservé le :</td>
                                    <td>{{ $booking->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <td class="fw-medium">Prix par nuit :</td>
                                    <td>{{ number_format($booking->price_per_night, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Nombre de nuits :</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Sous-total :</td>
                                    <td>{{ number_format($booking->subtotal_amount, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @if($booking->tax_amount > 0)
                                <tr>
                                    <td class="fw-medium">Taxes :</td>
                                    <td>{{ number_format($booking->tax_amount, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-medium"><strong>Total :</strong></td>
                                    <td><strong>{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($booking->special_requests)
                <div class="mt-3">
                    <h6>Demandes spéciales :</h6>
                    <p class="text-muted">{{ $booking->special_requests }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Informations du client -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Informations du client</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-borderless table-sm">
                            <tbody>
                                <tr>
                                    <td class="fw-medium">Nom complet :</td>
                                    <td>{{ $booking->first_name }} {{ $booking->last_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Email :</td>
                                    <td>
                                        <a href="mailto:{{ $booking->email }}" class="text-primary">
                                            {{ $booking->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Téléphone :</td>
                                    <td>
                                        <a href="tel:{{ $booking->phone }}" class="text-primary">
                                            {{ $booking->phone }}
                                        </a>
                                    </td>
                                </tr>
                                @if($booking->country)
                                <tr>
                                    <td class="fw-medium">Pays :</td>
                                    <td>{{ $booking->country }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des paiements -->
        @if($booking->payments->count() > 0)
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Historique des paiements</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Méthode</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Référence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @switch($payment->payment_method)
                                        @case('wave')
                                            <span class="badge bg-primary">Wave</span>
                                            @break
                                        @case('paypal')
                                            <span class="badge bg-info">PayPal</span>
                                            @break
                                        @case('cash')
                                            <span class="badge bg-secondary">Espèces</span>
                                            @break
                                    @endswitch
                                </td>
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
                                    @endswitch
                                </td>
                                <td>
                                    @if($payment->transaction_id)
                                        <code>{{ $payment->transaction_id }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Actions</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($booking->status === 'pending')
                        <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100" 
                                    onclick="return confirm('Confirmer cette réservation ?')">
                                <i class="ri-check-line me-1"></i> Confirmer
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Annuler cette réservation ?')">
                                <i class="ri-close-line me-1"></i> Annuler
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-light">
                        <i class="ri-arrow-left-line me-1"></i> Retour à la liste
                    </a>

                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="ri-printer-line me-1"></i> Imprimer
                    </button>

                    <a href="mailto:{{ $booking->email }}" class="btn btn-outline-info">
                        <i class="ri-mail-line me-1"></i> Contacter le client
                    </a>
                </div>
            </div>
        </div>

        <!-- Résumé -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Résumé</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    @if($booking->residence->images->where('is_primary', true)->first())
                        <img src="{{ Storage::url($booking->residence->images->where('is_primary', true)->first()->image_path) }}" 
                             class="img-fluid rounded" 
                             style="height: 120px; width: 100%; object-fit: cover;"
                             alt="{{ $booking->residence->name }}">
                    @endif
                </div>
                
                <h6 class="mb-2">{{ $booking->residence->name }}</h6>
                <p class="text-muted small mb-2">
                    <i class="ri-map-pin-line me-1"></i> {{ $booking->residence->address }}
                </p>
                
                <div class="border-top pt-2 mt-2">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Entrée :</small>
                        <small class="fw-medium">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</small>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Sortie :</small>
                        <small class="fw-medium">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</small>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Voyageurs :</small>
                        <small class="fw-medium">{{ $booking->guests_count }}</small>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-1">
                        <small><strong>Total :</strong></small>
                        <small><strong>{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</strong></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques de la résidence -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Performance de la résidence</h4>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-2">
                        <h6 class="text-primary mb-0">{{ $booking->residence->bookings->count() }}</h6>
                        <small class="text-muted">Réservations totales</small>
                    </div>
                    <div class="col-6 mb-2">
                        <h6 class="text-success mb-0">{{ $booking->residence->bookings->where('status', 'confirmed')->count() }}</h6>
                        <small class="text-muted">Confirmées</small>
                    </div>
                    <div class="col-12">
                        <h6 class="text-info mb-0">
                            {{ number_format($booking->residence->bookings->where('status', 'confirmed')->sum('total_amount'), 0, ',', ' ') }}
                        </h6>
                        <small class="text-muted">FCFA générés</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Script pour l'impression
window.onbeforeprint = function() {
    document.querySelector('.page-title-box').style.display = 'none';
    document.querySelector('.card .card-header').style.display = 'none';
    document.querySelectorAll('.btn').forEach(btn => btn.style.display = 'none');
};

window.onafterprint = function() {
    document.querySelector('.page-title-box').style.display = 'block';
    document.querySelector('.card .card-header').style.display = 'block';
    document.querySelectorAll('.btn').forEach(btn => btn.style.display = 'inline-block');
};
</script>
@endsection