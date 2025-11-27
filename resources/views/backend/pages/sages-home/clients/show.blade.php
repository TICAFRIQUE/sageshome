@extends('backend.layouts.master')

@section('title', 'Détails Client - ' . $client->username)

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Détails du Client</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                    <li class="breadcrumb-item active">{{ $client->username }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <!-- Informations du client -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0">Profil Client</h5>
                    <div class="ms-auto">
                        @if($client->deleted_at)
                            <span class="badge bg-danger">Inactif</span>
                        @else
                            <span class="badge bg-success">Actif</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar-xl mx-auto">
                        <div class="avatar-title bg-primary text-white rounded-circle fs-2">
                            {{ strtoupper(substr($client->username, 0, 2)) }}
                        </div>
                    </div>
                    <h5 class="mt-3 mb-1">{{ $client->username }}</h5>
                    <p class="text-muted">Client depuis le {{ $client->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <td class="ps-0">
                                    <i class="ri-mail-line text-primary me-2"></i>Email:
                                </td>
                                <td class="text-end">{{ $client->email }}</td>
                            </tr>
                            @if($client->phone)
                            <tr>
                                <td class="ps-0">
                                    <i class="ri-phone-line text-primary me-2"></i>Téléphone:
                                </td>
                                <td class="text-end">{{ $client->phone }}</td>
                            </tr>
                            @endif
                            @if($client->address)
                            <tr>
                                <td class="ps-0">
                                    <i class="ri-map-pin-line text-primary me-2"></i>Adresse:
                                </td>
                                <td class="text-end">{{ $client->address }}</td>
                            </tr>
                            @endif
                            @if($client->city)
                            <tr>
                                <td class="ps-0">
                                    <i class="ri-building-line text-primary me-2"></i>Ville:
                                </td>
                                <td class="text-end">{{ $client->city }}</td>
                            </tr>
                            @endif
                            @if($client->country)
                            <tr>
                                <td class="ps-0">
                                    <i class="ri-map-line text-primary me-2"></i>Pays:
                                </td>
                                <td class="text-end">{{ $client->country }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="ps-0">
                                    <i class="ri-user-add-line text-primary me-2"></i>Inscription:
                                </td>
                                <td class="text-end">{{ $client->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-primary btn-sm flex-fill">
                            <i class="ri-edit-line me-1"></i>Modifier
                        </a>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-light btn-sm flex-fill">
                            <i class="ri-arrow-left-line me-1"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques du client -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="mb-1 text-primary">{{ $analytics['total_bookings'] }}</h4>
                            <p class="text-muted mb-0">Réservations</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-1 text-success">{{ number_format($analytics['total_amount'], 2) }} €</h4>
                        <p class="text-muted mb-0">Total Dépensé</p>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="mb-1 text-info">{{ $analytics['pending_bookings'] }}</h5>
                        <p class="text-muted mb-0 small">En attente</p>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-1 text-success">{{ $analytics['confirmed_bookings'] }}</h5>
                        <p class="text-muted mb-0 small">Confirmées</p>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-1 text-danger">{{ $analytics['cancelled_bookings'] }}</h5>
                        <p class="text-muted mb-0 small">Annulées</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Réservations du client -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">Réservations ({{ $client->bookings->count() }})</h5>
            </div>
            <div class="card-body">
                @if($client->bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Résidence</th>
                                    <th>Dates</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->bookings->take(10) as $booking)
                                <tr>
                                    <td>
                                        <span class="fw-medium">#{{ $booking->reference ?? $booking->id }}</span><br>
                                        <small class="text-muted">{{ $booking->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        @if($booking->residence)
                                            <div>
                                                <span class="fw-medium">{{ $booking->residence->name }}</span><br>
                                                <small class="text-muted">{{ $booking->residence->location }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Résidence supprimée</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->check_in && $booking->check_out)
                                            <div>
                                                <strong>Du:</strong> {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}<br>
                                                <strong>Au:</strong> {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <span class="text-muted">Dates non définies</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->total_amount)
                                            <span class="fw-bold text-success">{{ number_format($booking->total_amount, 2) }} €</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($booking->status ?? 'pending')
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
                                                <span class="badge bg-warning">En attente</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="ri-eye-line align-bottom me-2 text-muted"></i> Voir
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="ri-edit-line align-bottom me-2 text-muted"></i> Modifier
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($client->bookings->count() > 10)
                        <div class="text-center mt-3">
                            <p class="text-muted">Affichage de 10 réservations sur {{ $client->bookings->count() }}</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Voir toutes les réservations</a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="ri-calendar-line display-1 text-muted"></i>
                        </div>
                        <h5>Aucune réservation</h5>
                        <p class="text-muted">Ce client n'a pas encore effectué de réservation</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Journal d'activité -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Journal d'Activité</h5>
            </div>
            <div class="card-body">
                <div class="timeline-item">
                    <div class="timeline-line"></div>
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Inscription</h6>
                        <p class="text-muted mb-1">Le client s'est inscrit sur la plateforme</p>
                        <small class="text-muted">{{ $client->created_at->format('d/m/Y à H:i') }}</small>
                    </div>
                </div>

                @foreach($client->bookings->sortByDesc('created_at')->take(5) as $booking)
                <div class="timeline-item">
                    <div class="timeline-line"></div>
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Nouvelle réservation</h6>
                        <p class="text-muted mb-1">
                            Réservation #{{ $booking->reference ?? $booking->id }}
                            @if($booking->residence)
                                pour {{ $booking->residence->name }}
                            @endif
                        </p>
                        <small class="text-muted">{{ $booking->created_at->format('d/m/Y à H:i') }}</small>
                    </div>
                </div>
                @endforeach

                @if($client->bookings->count() === 0)
                <div class="text-center py-3">
                    <p class="text-muted">Aucune activité récente</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.timeline-item {
    position: relative;
    padding-left: 2rem;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-line {
    position: absolute;
    left: 0.5rem;
    top: 1rem;
    width: 1px;
    height: calc(100% - 1rem);
    background-color: #e9ecef;
}

.timeline-item:last-child .timeline-line {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: 0.25rem;
    top: 0.25rem;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
}

.timeline-content {
    margin-left: 1rem;
}

.timeline-title {
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
    font-weight: 600;
}
</style>
@endpush