@extends('backend.layouts.master')

@section('title', isset($client) ? 'Modifier Client' : 'Nouveau Client')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ isset($client) ? 'Modifier Client' : 'Nouveau Client' }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                    <li class="breadcrumb-item active">
                        {{ isset($client) ? 'Modifier' : 'Nouveau' }}
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    {{ isset($client) ? 'Modifier les informations du client' : 'Ajouter un nouveau client' }}
                </h5>
            </div>
            <form action="{{ isset($client) ? route('admin.clients.update', $client) : route('admin.clients.store') }}" 
                  method="POST" class="needs-validation" novalidate>
                @csrf
                @if(isset($client))
                    @method('PUT')
                @endif

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="list-unstyled mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="ri-error-warning-line me-2"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Informations personnelles -->
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="ri-user-line me-2"></i>Informations Personnelles
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" 
                                   value="{{ old('username', $client->username ?? '') }}" 
                                   placeholder="Nom d'utilisateur" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" 
                                   value="{{ old('email', $client->email ?? '') }}" 
                                   placeholder="email@exemple.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" 
                                   value="{{ old('phone', $client->phone ?? '') }}" 
                                   placeholder="+33 1 23 45 67 89">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(!isset($client))
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Mot de passe" required>
                                <button type="button" class="btn btn-outline-secondary" id="toggle-password">
                                    <i class="ri-eye-line" id="password-icon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimum 8 caractères</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <!-- Adresse -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="ri-map-pin-line me-2"></i>Adresse
                            </h6>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Adresse complète</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2" 
                                      placeholder="Numéro, rue, quartier...">{{ old('address', $client->address ?? '') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">Ville</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" 
                                   value="{{ old('city', $client->city ?? '') }}" 
                                   placeholder="Ville">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Pays</label>
                            <select class="form-select @error('country') is-invalid @enderror" 
                                    id="country" name="country">
                                <option value="">Sélectionner un pays</option>
                                <option value="Sénégal" {{ old('country', $client->country ?? '') === 'Sénégal' ? 'selected' : '' }}>Sénégal</option>
                                <option value="France" {{ old('country', $client->country ?? '') === 'France' ? 'selected' : '' }}>France</option>
                                <option value="Côte d'Ivoire" {{ old('country', $client->country ?? '') === "Côte d'Ivoire" ? 'selected' : '' }}>Côte d'Ivoire</option>
                                <option value="Mali" {{ old('country', $client->country ?? '') === 'Mali' ? 'selected' : '' }}>Mali</option>
                                <option value="Burkina Faso" {{ old('country', $client->country ?? '') === 'Burkina Faso' ? 'selected' : '' }}>Burkina Faso</option>
                                <option value="Maroc" {{ old('country', $client->country ?? '') === 'Maroc' ? 'selected' : '' }}>Maroc</option>
                                <option value="Algérie" {{ old('country', $client->country ?? '') === 'Algérie' ? 'selected' : '' }}>Algérie</option>
                                <option value="Tunisie" {{ old('country', $client->country ?? '') === 'Tunisie' ? 'selected' : '' }}>Tunisie</option>
                                <option value="Canada" {{ old('country', $client->country ?? '') === 'Canada' ? 'selected' : '' }}>Canada</option>
                                <option value="États-Unis" {{ old('country', $client->country ?? '') === 'États-Unis' ? 'selected' : '' }}>États-Unis</option>
                                <option value="Autre" {{ old('country', $client->country ?? '') === 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut et notifications -->
                        @if(isset($client))
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="ri-settings-3-line me-2"></i>Paramètres du Compte
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email_verified_at" class="form-label">Email vérifié</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="email_verified" name="email_verified" 
                                       {{ old('email_verified', $client->email_verified_at ? 'on' : '') ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_verified">
                                    Email vérifié
                                </label>
                            </div>
                            <small class="text-muted">
                                @if($client->email_verified_at)
                                    Vérifié le {{ $client->email_verified_at->format('d/m/Y à H:i') }}
                                @else
                                    Email non vérifié
                                @endif
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut du compte</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="account_active" name="account_active" 
                                       {{ old('account_active', !$client->deleted_at ? 'on' : '') ? 'checked' : '' }}>
                                <label class="form-check-label" for="account_active">
                                    Compte actif
                                </label>
                            </div>
                            <small class="text-muted">Désactiver empêche la connexion du client</small>
                        </div>
                        @endif

                        <!-- Notes administratives -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="ri-file-text-line me-2"></i>Notes Administratives
                            </h6>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="admin_notes" class="form-label">Notes internes</label>
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                      id="admin_notes" name="admin_notes" rows="3" 
                                      placeholder="Notes visibles uniquement par les administrateurs...">{{ old('admin_notes', $client->admin_notes ?? '') }}</textarea>
                            <small class="text-muted">Ces notes ne sont pas visibles par le client</small>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-light">
                            <i class="ri-arrow-left-line me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line me-1"></i>
                            {{ isset($client) ? 'Mettre à jour' : 'Créer le client' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
// Validation du formulaire
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Toggle password visibility
document.getElementById('toggle-password')?.addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('ri-eye-line');
        passwordIcon.classList.add('ri-eye-off-line');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('ri-eye-off-line');
        passwordIcon.classList.add('ri-eye-line');
    }
});

// Validation en temps réel de l'email
document.getElementById('email').addEventListener('input', function() {
    const emailInput = this;
    const email = emailInput.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        emailInput.classList.add('is-invalid');
        emailInput.classList.remove('is-valid');
    } else if (email) {
        emailInput.classList.remove('is-invalid');
        emailInput.classList.add('is-valid');
    } else {
        emailInput.classList.remove('is-invalid', 'is-valid');
    }
});

// Auto-format phone number
document.getElementById('phone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.startsWith('33')) {
        value = '+' + value.substring(0, 2) + ' ' + value.substring(2);
    } else if (value.startsWith('221')) {
        value = '+' + value.substring(0, 3) + ' ' + value.substring(3);
    }
    this.value = value;
});
</script>
@endpush