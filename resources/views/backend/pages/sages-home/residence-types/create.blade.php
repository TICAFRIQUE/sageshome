@extends('backend.layouts.master')

@section('title', 'Ajouter un Type de Résidence')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Ajouter un Type de Résidence</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.residence-types.index') }}">Types de Résidences</a></li>
                    <li class="breadcrumb-item active">Ajouter</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.residence-types.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Informations du Type</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom du type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                <div class="form-text">Ce nom sera affiché publiquement</div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="min_capacity" class="form-label">Capacité minimale <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('min_capacity') is-invalid @enderror" 
                                       id="min_capacity" name="min_capacity" value="{{ old('min_capacity') }}" min="1" required>
                                @error('min_capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="max_capacity" class="form-label">Capacité maximale <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_capacity') is-invalid @enderror" 
                                       id="max_capacity" name="max_capacity" value="{{ old('max_capacity') }}" min="1" required>
                                @error('max_capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Ordre d'affichage</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                   id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Type actif</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="text-end">
                        <a href="{{ route('admin.residence-types.index') }}" class="btn btn-secondary me-2">
                            <i class="ri-close-line me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line me-1"></i>Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Aide</h5>
            </div>
            <div class="card-body">
                <h6>Nom du type</h6>
                <p class="text-muted small mb-3">
                    Le nom qui sera visible par les utilisateurs. Un slug sera automatiquement généré pour les URLs.
                </p>

                <h6>Description</h6>
                <p class="text-muted small mb-3">
                    Description détaillée du type de résidence (optionnel).
                </p>

                <h6>Capacités</h6>
                <p class="text-muted small mb-3">
                    Définissez les capacités minimale et maximale en nombre de personnes pour ce type de résidence.
                </p>

                <h6>Ordre d'affichage</h6>
                <p class="text-muted small">
                    Plus le nombre est petit, plus le type apparaîtra en premier dans les listes.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const minCapacity = document.getElementById('min_capacity');
    const maxCapacity = document.getElementById('max_capacity');

    function validateCapacity() {
        const min = parseInt(minCapacity.value) || 0;
        const max = parseInt(maxCapacity.value) || 0;

        if (min > 0 && max > 0 && min > max) {
            maxCapacity.setCustomValidity('La capacité maximale doit être supérieure ou égale à la capacité minimale');
        } else {
            maxCapacity.setCustomValidity('');
        }
    }

    minCapacity.addEventListener('input', validateCapacity);
    maxCapacity.addEventListener('input', validateCapacity);
});
</script>
@endsection