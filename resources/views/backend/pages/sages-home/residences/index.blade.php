@extends('backend.layouts.master')

@section('title', 'Gestion des Résidences')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Gestion des Résidences</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item active">Résidences</li>
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
                        <h4 class="card-title mb-0">Liste des Résidences</h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.residences.create') }}" class="btn btn-success">
                            <i class="ri-add-line"></i> Ajouter une Résidence
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($residences->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Localisation</th>
                                    <th>Prix/nuit</th>
                                    <th>Capacité</th>
                                    <th>Statut</th>
                                    <th>Réservations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($residences as $residence)
                                <tr>
                                    <td>
                                        @if($residence->primaryImage)
                                            <img src="{{ Storage::url($residence->primaryImage->image_path) }}" 
                                                 alt="{{ $residence->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="ri-image-line text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <h6 class="mb-1">{{ $residence->name }}</h6>
                                        <p class="text-muted mb-0 small">{{ Str::limit($residence->description, 30) }}</p>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $residence->residenceType->name ?? 'Non défini' }}</span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="ri-map-pin-line text-primary me-1"></i>
                                            @if($residence->commune)
                                                <strong>{{ $residence->commune }}</strong>, {{ $residence->ville }}
                                            @else
                                                {{ $residence->ville }}
                                            @endif
                                            @if($residence->google_maps_url)
                                                <br><a href="{{ $residence->google_maps_url }}" 
                                                       target="_blank" 
                                                       class="text-decoration-none small">
                                                    <i class="ri-external-link-line"></i> Maps
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                    <td>
                                        <i class="ri-user-line"></i> {{ $residence->capacity }} pers.
                                    </td>
                                    <td>
                                        @if($residence->is_available)
                                            <span class="badge bg-success">Disponible</span>
                                        @else
                                            <span class="badge bg-danger">Indisponible</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $residence->bookings_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.residences.show', $residence) }}">
                                                    <i class="ri-eye-line align-bottom me-2 text-muted"></i> Voir
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.residences.edit', $residence) }}">
                                                    <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modifier
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('residences.show', $residence->slug) }}" target="_blank">
                                                    <i class="ri-external-link-line align-bottom me-2 text-muted"></i> Voir sur le site
                                                </a></li>
                                                <li class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.residences.destroy', $residence) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette résidence ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="ri-delete-bin-fill align-bottom me-2"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($residences->hasPages())
                        <div class="row justify-content-between align-items-center mt-4">
                            <div class="col-auto">
                                <div class="text-muted">
                                    Affichage {{ $residences->firstItem() }} à {{ $residences->lastItem() }} 
                                    sur {{ $residences->total() }} résidences
                                </div>
                            </div>
                            <div class="col-auto">
                                {{ $residences->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <div class="avatar-md mx-auto mb-4">
                            <div class="avatar-title bg-light rounded-circle">
                                <i class="ri-building-line fs-24"></i>
                            </div>
                        </div>
                        <h5>Aucune résidence trouvée</h5>
                        <p class="text-muted">Commencez par ajouter votre première résidence.</p>
                        <a href="{{ route('admin.residences.create') }}" class="btn btn-success">
                            <i class="ri-add-line me-1"></i> Ajouter une Résidence
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection