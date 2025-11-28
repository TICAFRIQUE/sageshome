@extends('backend.layouts.master')

@section('title', 'Détails de la Résidence')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ $residence->name }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.residences.index') }}">Résidences</a></li>
                    <li class="breadcrumb-item active">{{ $residence->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-8">
        <!-- Images de la résidence -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Galerie d'images</h4>
            </div>
            <div class="card-body">
                @if($residence->images->count() > 0)
                    <div class="row">
                        @foreach($residence->images as $image)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="position-relative">
                                    <img src="{{ Storage::url($image->image_path) }}" 
                                         class="card-img-top" 
                                         style="height: 200px; object-fit: cover;"
                                         alt="{{ $image->alt_text }}">
                                    @if($image->is_primary)
                                        <span class="position-absolute top-0 start-0 badge bg-primary m-2">
                                            Image principale
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ri-image-line fs-48 text-muted"></i>
                        <p class="text-muted mt-2">Aucune image disponible</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Réservations récentes -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Réservations récentes</h4>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-primary">{{ $residence->bookings->count() }} au total</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($residence->bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Dates</th>
                                    <th>Client</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($residence->bookings->take(10) as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary">
                                            {{ $booking->id }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}
                                    </td>
                                    <td>{{ $booking->first_name }} {{ $booking->last_name }}</td>
                                    <td>{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</td>
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
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ri-calendar-line fs-48 text-muted"></i>
                        <p class="text-muted mt-2">Aucune réservation pour cette résidence</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Informations de la résidence -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Informations</h4>
                    </div>
                    <div class="col-auto">
                        <div class="dropdown">
                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ri-more-fill"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.residences.edit', $residence) }}">
                                    <i class="ri-pencil-fill me-2"></i> Modifier
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('residences.show', $residence->slug) }}" target="_blank">
                                    <i class="ri-external-link-line me-2"></i> Voir sur le site
                                </a></li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.residences.destroy', $residence) }}" method="POST"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette résidence ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="ri-delete-bin-fill me-2"></i> Supprimer
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-medium">Type :</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{  ucfirst($residence->residenceType->name ?? 'Inconnu') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Capacité :</td>
                                <td>
                                    <i class="ri-user-line text-muted"></i> {{ $residence->capacity }} personnes
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Prix/nuit :</td>
                                <td>
                                    <strong>{{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA</strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Disponibilité :</td>
                                <td>
                                    @if($residence->is_available)
                                        <span class="badge bg-success">Disponible</span>
                                    @else
                                        <span class="badge bg-danger">Indisponible</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Mise en avant :</td>
                                <td>
                                    @if($residence->is_featured)
                                        <span class="badge bg-warning">Oui</span>
                                    @else
                                        <span class="text-muted">Non</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Ajoutée le :</td>
                                <td>{{ $residence->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Description</h4>
            </div>
            <div class="card-body">
                <p class="mb-3"><strong>Description courte :</strong></p>
                <p class="text-muted">{{ $residence->description }}</p>
                
                @if($residence->full_description)
                    <p class="mb-2 mt-3"><strong>Description complète :</strong></p>
                    <p class="text-muted">{{ $residence->full_description }}</p>
                @endif
            </div>
        </div>

        <!-- Équipements -->
        @if($residence->amenities && count($residence->amenities) > 0)
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Équipements</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($residence->getFormattedAmenities() as $amenity)
                    <div class="col-12 mb-2">
                        <i class="ri-check-line text-success me-1"></i>
                        <span>{{ $amenity }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Localisation -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Localisation</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ri-map-pin-line text-primary me-2"></i>
                        <strong>
                            @if($residence->commune)
                                {{ $residence->commune }}, {{ $residence->ville }}
                            @else
                                {{ $residence->ville }}
                            @endif
                        </strong>
                    </div>
                    <p class="text-muted mb-0">{{ $residence->address }}</p>
                </div>
                
                @if($residence->google_maps_url)
                <div class="mb-3">
                    <a href="{{ $residence->google_maps_url }}" 
                       target="_blank" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="ri-external-link-line me-1"></i>
                        Voir sur Google Maps
                    </a>
                </div>
                @endif
                
                @if($residence->latitude && $residence->longitude)
                <p class="text-muted small mb-0">
                    <i class="ri-navigation-line me-1"></i>
                    Coordonnées : {{ $residence->latitude }}, {{ $residence->longitude }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection