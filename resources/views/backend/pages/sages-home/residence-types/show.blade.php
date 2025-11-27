@extends('backend.layouts.master')

@section('title', 'Détails du Type de Résidence')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ $residenceType->name }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.residence-types.index') }}">Types de Résidences</a></li>
                    <li class="breadcrumb-item active">{{ $residenceType->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Informations du Type</h4>
                    <div>
                        <a href="{{ route('admin.residence-types.edit', $residenceType) }}" class="btn btn-primary btn-sm me-2">
                            <i class="ri-pencil-line me-1"></i> Modifier
                        </a>
                        @if($residenceType->residences_count == 0)
                            <form action="{{ route('admin.residence-types.destroy', $residenceType) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="ri-delete-bin-line me-1"></i> Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Nom :</label>
                            <p class="text-muted mb-0">{{ $residenceType->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Slug (URL) :</label>
                            <p class="text-muted mb-0"><code>{{ $residenceType->slug }}</code></p>
                        </div>
                    </div>
                </div>

                @if($residenceType->description)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Description :</label>
                        <p class="text-muted mb-0">{{ $residenceType->description }}</p>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Capacité minimale :</label>
                            <p class="text-muted mb-0">
                                <i class="ri-user-line me-1"></i>
                                {{ $residenceType->min_capacity }} personne{{ $residenceType->min_capacity > 1 ? 's' : '' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Capacité maximale :</label>
                            <p class="text-muted mb-0">
                                <i class="ri-user-line me-1"></i>
                                {{ $residenceType->max_capacity }} personne{{ $residenceType->max_capacity > 1 ? 's' : '' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Capacité affichée :</label>
                            <p class="text-muted mb-0">
                                <span class="badge bg-info">{{ $residenceType->formatted_capacity }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Statut :</label>
                            <p class="mb-0">
                                @if($residenceType->is_active)
                                    <span class="badge bg-success"><i class="ri-check-line me-1"></i>Actif</span>
                                @else
                                    <span class="badge bg-secondary"><i class="ri-close-line me-1"></i>Inactif</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Ordre d'affichage :</label>
                            <p class="text-muted mb-0">{{ $residenceType->sort_order }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Créé le :</label>
                            <p class="text-muted mb-0">{{ $residenceType->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des résidences utilisant ce type -->
        @if($residenceType->residences->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        Résidences de ce type 
                        <span class="badge bg-primary">{{ $residenceType->residences->count() }}</span>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Capacité</th>
                                    <th>Prix/nuit</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($residenceType->residences as $residence)
                                <tr>
                                    <td>
                                        @if($residence->primaryImage)
                                            <img src="{{ Storage::url($residence->primaryImage->image_path) }}" 
                                                 alt="{{ $residence->name }}" class="img-thumbnail" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="ri-image-line text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.residences.show', $residence) }}" class="fw-medium">
                                            {{ $residence->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <i class="ri-user-line"></i> {{ $residence->capacity }} pers.
                                    </td>
                                    <td>
                                        <strong>{{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                    <td>
                                        @if($residence->is_available)
                                            <span class="badge bg-success">Disponible</span>
                                        @else
                                            <span class="badge bg-danger">Indisponible</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.residences.show', $residence) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <a href="{{ route('admin.residences.edit', $residence) }}" 
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="ri-pencil-line"></i>
                                        </a>
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

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Total résidences :</span>
                    <span class="badge bg-primary fs-6">{{ $residenceType->residences->count() }}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Résidences disponibles :</span>
                    <span class="badge bg-success fs-6">
                        {{ $residenceType->residences->where('is_available', true)->count() }}
                    </span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Résidences mises en avant :</span>
                    <span class="badge bg-warning fs-6">
                        {{ $residenceType->residences->where('is_featured', true)->count() }}
                    </span>
                </div>

                @if($residenceType->residences->count() > 0)
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Prix moyen/nuit :</span>
                        <strong>{{ number_format($residenceType->residences->avg('price_per_night'), 0, ',', ' ') }} FCFA</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Prix min/nuit :</span>
                        <span>{{ number_format($residenceType->residences->min('price_per_night'), 0, ',', ' ') }} FCFA</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Prix max/nuit :</span>
                        <span>{{ number_format($residenceType->residences->max('price_per_night'), 0, ',', ' ') }} FCFA</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.residences.create') }}?type={{ $residenceType->id }}" 
                       class="btn btn-success">
                        <i class="ri-add-line me-1"></i> Ajouter une résidence
                    </a>
                    
                    <a href="{{ route('admin.residence-types.edit', $residenceType) }}" 
                       class="btn btn-primary">
                        <i class="ri-pencil-line me-1"></i> Modifier ce type
                    </a>
                    
                    <a href="{{ route('admin.residence-types.index') }}" 
                       class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection