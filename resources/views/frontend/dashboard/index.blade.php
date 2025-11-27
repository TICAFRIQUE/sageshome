@extends('frontend.layouts.dashboard')

@section('title', 'Dashboard - Sages Home')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de vos réservations et activités')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="stat-number">{{ $bookingsStats['total'] }}</div>
            <div class="stat-label">Réservations totales</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-number">{{ $bookingsStats['pending'] }}</div>
            <div class="stat-label">En attente</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-number">{{ $bookingsStats['confirmed'] }}</div>
            <div class="stat-label">Confirmées</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-currency-exchange"></i>
            </div>
            <div class="stat-number">{{ number_format($totalSpent, 0, ',', ' ') }}</div>
            <div class="stat-label">FCFA dépensés</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Prochaine réservation -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="mb-0 text-sage-accent">
                    <i class="bi bi-calendar-event me-2"></i>Prochaine réservation
                </h5>
            </div>
            <div class="card-body">
                @if($upcomingBooking)
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            @if($upcomingBooking->residence->primaryImage)
                                <img src="{{ Storage::url($upcomingBooking->residence->primaryImage->image_path) }}" 
                                     class="img-fluid rounded" 
                                     style="height: 120px; width: 100%; object-fit: cover;" 
                                     alt="{{ $upcomingBooking->residence->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 120px;">
                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-sage-accent">{{ $upcomingBooking->residence->name }}</h6>
                            <p class="text-muted mb-2">{{ $upcomingBooking->residence->residenceType->name }}</p>
                            
                            <div class="row text-sm">
                                <div class="col-6">
                                    <strong>Arrivée :</strong><br>
                                    <span class="text-sage-accent">{{ \Carbon\Carbon::parse($upcomingBooking->check_in)->format('d/m/Y') }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Départ :</strong><br>
                                    <span class="text-sage-accent">{{ \Carbon\Carbon::parse($upcomingBooking->check_out)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <span class="badge badge-sage me-2">{{ ucfirst($upcomingBooking->status) }}</span>
                                <span class="fw-bold text-sage-secondary">{{ number_format($upcomingBooking->total_price, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-end">
                        <a href="{{ route('dashboard.booking.show', $upcomingBooking) }}" 
                           class="btn btn-sage-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>Voir les détails
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">Aucune réservation à venir</h6>
                        <p class="text-muted mb-3">Découvrez nos magnifiques résidences</p>
                        <a href="{{ route('residences.index') }}" class="btn btn-sage-primary">
                            <i class="bi bi-plus-lg me-2"></i>Nouvelle réservation
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Profil rapide -->
    <div class="col-lg-4 mb-4">
        <div class="dashboard-card">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="mb-0 text-sage-accent">
                    <i class="bi bi-person me-2"></i>Mon profil
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-circle text-sage-accent" style="font-size: 4rem;"></i>
                </div>
                
                <h6 class="text-sage-accent">{{ $user->username }}</h6>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <p class="text-muted mb-3">{{ $user->phone }}</p>
                
                <div class="d-grid">
                    <a href="{{ route('dashboard.profile') }}" class="btn btn-sage-secondary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Modifier mon profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Réservations récentes -->
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-sage-accent">
                    <i class="bi bi-clock-history me-2"></i>Réservations récentes
                </h5>
                <a href="{{ route('dashboard.bookings') }}" class="btn btn-sage-primary btn-sm">
                    <i class="bi bi-arrow-right me-1"></i>Voir toutes
                </a>
            </div>
            <div class="card-body">
                @if($recentBookings->count() > 0)
                    <div class="row">
                        @foreach($recentBookings as $booking)
                            <div class="col-lg-4 mb-3">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1">{{ $booking->residence->name }}</h6>
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
                                        @endswitch
                                    </div>
                                    
                                    <p class="text-muted small mb-2">{{ $booking->residence->residenceType->name }}</p>
                                    
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-sage-secondary small">{{ number_format($booking->total_price, 0, ',', ' ') }} FCFA</span>
                                        <a href="{{ route('dashboard.booking.show', $booking) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">Aucune réservation récente</h6>
                        <p class="text-muted mb-3">Commencez par réserver votre première résidence</p>
                        <a href="{{ route('residences.index') }}" class="btn btn-sage-primary">
                            <i class="bi bi-plus-lg me-2"></i>Faire une réservation
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection