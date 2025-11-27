@extends('backend.layouts.master')

@section('title', 'Gestion des Clients')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Gestion des Clients</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item active">Clients</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<!-- Statistiques -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Total Clients</p>
                        <h4 class="mb-2">{{ number_format($stats['total']) }}</h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-primary rounded-3">
                            <i class="ri-user-line font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Clients Actifs</p>
                        <h4 class="mb-2">{{ number_format($stats['active']) }}</h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-success rounded-3">
                            <i class="ri-user-check-line font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Nouveaux ce mois</p>
                        <h4 class="mb-2">{{ number_format($stats['new_this_month']) }}</h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-info rounded-3">
                            <i class="ri-user-add-line font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Avec Réservations</p>
                        <h4 class="mb-2">{{ number_format($stats['with_bookings']) }}</h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-warning rounded-3">
                            <i class="ri-calendar-check-line font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres et actions -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Liste des Clients</h4>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.clients.export') }}" class="btn btn-outline-success btn-sm">
                                <i class="ri-download-line me-1"></i>Exporter
                            </a>
                            <a href="{{ route('admin.clients.create') }}" class="btn btn-success">
                                <i class="ri-add-line me-1"></i>Nouveau Client
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtres -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_from" 
                               placeholder="Date début" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_to" 
                               placeholder="Date fin" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-search-line me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-light">
                                <i class="ri-refresh-line me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if($clients->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Contact</th>
                                    <th>Localisation</th>
                                    <th>Réservations</th>
                                    <th>Inscription</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-primary text-white rounded-circle">
                                                    {{ strtoupper(substr($client->username, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">
                                                    <a href="{{ route('admin.clients.show', $client) }}" class="text-dark">
                                                        {{ $client->username }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">ID: {{ $client->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-medium">{{ $client->email }}</span><br>
                                            <small class="text-muted">{{ $client->phone ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $client->city ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $client->country ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary">{{ $client->bookings_count }}</span>
                                            @if($client->bookings->count() > 0)
                                                <small class="text-muted ms-2">
                                                    Dernière: {{ $client->bookings->first()->created_at->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $client->created_at->format('d/m/Y') }}</span><br>
                                        <small class="text-muted">{{ $client->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($client->deleted_at)
                                            <span class="badge bg-danger">Inactif</span>
                                        @else
                                            <span class="badge bg-success">Actif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.clients.show', $client) }}">
                                                    <i class="ri-eye-line align-bottom me-2 text-muted"></i> Voir
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.clients.edit', $client) }}">
                                                    <i class="ri-pencil-line align-bottom me-2 text-muted"></i> Modifier
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                @if($client->deleted_at)
                                                    <li>
                                                        <form action="{{ route('admin.clients.restore', $client) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="ri-restart-line align-bottom me-2"></i> Réactiver
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('admin.clients.destroy', $client) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Êtes-vous sûr de vouloir désactiver ce client ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-line align-bottom me-2"></i> Désactiver
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

                    <!-- Pagination -->
                    <div class="row">
                        <div class="col-lg-12">
                            {{ $clients->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="ri-user-line display-1 text-muted"></i>
                        </div>
                        <h5>Aucun client trouvé</h5>
                        <p class="text-muted">Commencez par ajouter des clients ou modifiez vos filtres</p>
                        <a href="{{ route('admin.clients.create') }}" class="btn btn-success">
                            <i class="ri-add-line me-1"></i> Ajouter le premier client
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection