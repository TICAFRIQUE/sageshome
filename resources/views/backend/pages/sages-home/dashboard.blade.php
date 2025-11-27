@extends('backend.layouts.master')

@section('title', 'Dashboard - Sages Home')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Dashboard Sages Home</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item active">Sages Home</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="sages-home-dashboard">
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-tachometer-alt"></i> Navigation</h5>
                </div>
                <div class="card-body p-0">
                    <nav class="nav flex-column">
                        <a class="nav-link {{ Request::routeIs('admin.sages-home.dashboard') ? 'active' : '' }}" 
                           href="{{ route('admin.sages-home.dashboard') }}">
                            <i class="fas fa-home"></i> Tableau de bord
                        </a>
                        <a class="nav-link {{ Request::routeIs('admin.residences.*') ? 'active' : '' }}" 
                           href="{{ route('admin.residences.index') }}">
                            <i class="fas fa-building"></i> Résidences
                        </a>
                        <a class="nav-link {{ Request::routeIs('admin.bookings.*') ? 'active' : '' }}" 
                           href="{{ route('admin.bookings.index') }}">
                            <i class="fas fa-calendar-check"></i> Réservations
                        </a>
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-eye"></i> Voir le site
                        </a>
                    </nav>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white sage-gradient">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $totalResidences }}</h4>
                                    <p class="mb-0">Résidences</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-building fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white sage-gradient">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $totalBookings }}</h4>
                                    <p class="mb-0">Réservations</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white sage-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</h4>
                                    <p class="mb-0">Revenus</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-coins fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-clock"></i> Réservations récentes</h5>
                        </div>
                        <div class="card-body">
                            @if($recentBookings->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Client</th>
                                                <th>Résidence</th>
                                                <th>Dates</th>
                                                <th>Statut</th>
                                                <th>Montant</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentBookings as $booking)
                                            <tr>
                                                <td>{{ $booking->first_name }} {{ $booking->last_name }}</td>
                                                <td>{{ $booking->residence->name }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m') }} - 
                                                    {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Aucune réservation récente.</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-pie"></i> Statut des réservations</h5>
                        </div>
                        <div class="card-body">
                            @if($bookingStats->count() > 0)
                                @foreach($bookingStats as $stat)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ ucfirst($stat->status) }}</span>
                                    <span class="badge bg-primary">{{ $stat->count }}</span>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted">Aucune donnée disponible.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sages-home-dashboard .sage-gradient {
    background: linear-gradient(135deg, #F2D18A, #C29B32) !important;
}

.sages-home-dashboard .sage-success {
    background: linear-gradient(135deg, #2F4A33, #1a7e20) !important;
}

.sages-home-dashboard .nav-link.active {
    background-color: #2F4A33;
    color: white;
    border-radius: 0.375rem;
}

.sages-home-dashboard .nav-link:hover {
    background-color: #2F4A33;
    color: white;
    border-radius: 0.375rem;
}
</style>
@endsection