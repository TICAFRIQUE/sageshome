@extends('backend.layouts.master')

@section('title', 'Gestion des Réservations')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Gestion des Réservations</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item active">Réservations</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Liste des Réservations</h4>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="statusFilter">
                                <option value="">Tous les statuts</option>
                                <option value="pending">En attente</option>
                                <option value="confirmed">Confirmées</option>
                                <option value="cancelled">Annulées</option>
                            </select>
                            <a href="{{ route('admin.bookings.calendar') }}" class="btn btn-info btn-sm">
                                <i class="ri-calendar-line"></i> Calendrier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Client</th>
                                    <th>Résidence</th>
                                    <th>Dates</th>
                                    <th>Durée</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Paiement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $booking->id }}</strong>
                                        <br><small class="text-muted">{{ $booking->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <h6 class="mb-1">{{ $booking->first_name }} {{ $booking->last_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-mail-line"></i> {{ $booking->email }}
                                            <br><i class="ri-phone-line"></i> {{ $booking->phone }}
                                        </p>
                                    </td>
                                    <td>
                                        <h6 class="mb-1">{{ $booking->residence->name }}</h6>
                                        <p class="text-muted mb-0 small">{{ $booking->residence->location }}</p>
                                    </td>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</strong>
                                        <br>
                                        <span class="text-muted">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $booking->nights }} nuit(s)</span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</strong>
                                        @if($booking->guests > 1)
                                            <br><small class="text-muted">{{ $booking->guests }} personnes</small>
                                        @endif
                                    </td>
                                    <td>
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
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($booking->payment)
                                            @switch($booking->payment->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">En attente</span>
                                                    <br><small>{{ ucfirst($booking->payment->method) }}</small>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">Payé</span>
                                                    <br><small>{{ ucfirst($booking->payment->method) }}</small>
                                                    @break
                                                @case('failed')
                                                    <span class="badge bg-danger">Échec</span>
                                                    <br><small>{{ ucfirst($booking->payment->method) }}</small>
                                                    @break
                                            @endswitch
                                        @else
                                            <span class="badge bg-secondary">Aucun paiement</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.show', $booking) }}">
                                                    <i class="ri-eye-line align-bottom me-2 text-muted"></i> Voir détails
                                                </a></li>
                                                
                                                @if($booking->status === 'pending')
                                                <li>
                                                    <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="ri-check-line align-bottom me-2"></i> Confirmer
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                                
                                                @if($booking->payment && $booking->payment->status === 'pending')
                                                <li>
                                                    <form action="{{ route('admin.bookings.confirm-payment', $booking) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-info">
                                                            <i class="ri-money-dollar-circle-line align-bottom me-2"></i> Confirmer paiement
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                                
                                                @if($booking->status !== 'cancelled')
                                                <li class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                                        @csrf
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="ri-close-line align-bottom me-2"></i> Annuler
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($bookings->hasPages())
                        <div class="row justify-content-between align-items-center mt-4">
                            <div class="col-auto">
                                <div class="text-muted">
                                    Affichage {{ $bookings->firstItem() }} à {{ $bookings->lastItem() }} 
                                    sur {{ $bookings->total() }} réservations
                                </div>
                            </div>
                            <div class="col-auto">
                                {{ $bookings->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <div class="avatar-md mx-auto mb-4">
                            <div class="avatar-title bg-light rounded-circle">
                                <i class="ri-calendar-check-line fs-24"></i>
                            </div>
                        </div>
                        <h5>Aucune réservation trouvée</h5>
                        <p class="text-muted">Les réservations apparaîtront ici lorsque les clients effectueront des bookings.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary" target="_blank">
                            <i class="ri-external-link-line me-1"></i> Voir le site public
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($bookings->count() > 0)
<!-- Statistiques rapides -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">En attente</p>
                        <h4 class="mb-0">{{ $bookings->where('status', 'pending')->count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded">
                            <div class="avatar-title bg-warning-subtle text-warning rounded">
                                <i class="ri-time-line fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Confirmées</p>
                        <h4 class="mb-0">{{ $bookings->where('status', 'confirmed')->count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded">
                            <div class="avatar-title bg-success-subtle text-success rounded">
                                <i class="ri-check-line fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Annulées</p>
                        <h4 class="mb-0">{{ $bookings->where('status', 'cancelled')->count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded">
                            <div class="avatar-title bg-danger-subtle text-danger rounded">
                                <i class="ri-close-line fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Total Revenus</p>
                        <h4 class="mb-0">{{ number_format($bookings->where('status', 'confirmed')->sum('total_amount'), 0, ',', ' ') }} FCFA</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded">
                            <div class="avatar-title bg-info-subtle text-info rounded">
                                <i class="ri-money-dollar-circle-line fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection