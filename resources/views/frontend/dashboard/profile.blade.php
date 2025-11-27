@extends('layouts.app')

@section('title', 'Mon Profil - Sages Home')

@section('content')
    <div class="container py-5">
        <!-- Header du dashboard -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="dashboard-header">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-avatar me-3">
                            <i class="fas fa-user-circle text-sage-primary" style="font-size: 4rem;"></i>
                        </div>
                        <div>
                            <h2 class="text-sage-primary mb-1">
                                <i class="fas fa-user me-2"></i>
                                Bienvenue, {{ $user->username }} !
                            </h2>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Membre depuis {{ $user->created_at->format('F Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Informations principales du profil -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-sage-light border-0">
                        <h5 class="mb-0 text-sage-primary">
                            <i class="fas fa-user-edit me-2"></i>Informations personnelles
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dashboard.profile.update') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label text-sage-primary">
                                        <i class="fas fa-user me-2"></i>Nom d'utilisateur <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control border-sage @error('username') is-invalid @enderror"
                                        id="username" name="username" value="{{ old('username', $user->username) }}"
                                        required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label text-sage-primary">
                                        <i class="fas fa-envelope me-2"></i>Adresse email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                        class="form-control border-sage @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label text-sage-primary">
                                        <i class="fas fa-phone me-2"></i>Téléphone <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel"
                                        class="form-control border-sage @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone', $user->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label text-sage-primary">
                                        <i class="fas fa-city me-2"></i>Ville
                                    </label>
                                    <input type="text"
                                        class="form-control border-sage @error('city') is-invalid @enderror" id="city"
                                        name="city" value="{{ old('city', $user->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="address" class="form-label text-sage-primary">
                                        <i class="fas fa-map-marker-alt me-2"></i>Adresse
                                    </label>
                                    <textarea class="form-control border-sage @error('address') is-invalid @enderror" id="address" name="address"
                                        rows="3">{{ old('address', $user->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="country" class="form-label text-sage-primary">
                                        <i class="fas fa-flag me-2"></i>Pays
                                    </label>
                                    <select class="form-select border-sage @error('country') is-invalid @enderror"
                                        id="country" name="country">
                                        <option value="">Sélectionnez un pays</option>
                                        <option value="Sénégal"
                                            {{ old('country', $user->country) === 'Sénégal' ? 'selected' : '' }}>Sénégal
                                        </option>
                                        <option value="Mali"
                                            {{ old('country', $user->country) === 'Mali' ? 'selected' : '' }}>Mali</option>
                                        <option value="Burkina Faso"
                                            {{ old('country', $user->country) === 'Burkina Faso' ? 'selected' : '' }}>
                                            Burkina Faso</option>
                                        <option value="Côte d'Ivoire"
                                            {{ old('country', $user->country) === "Côte d'Ivoire" ? 'selected' : '' }}>Côte
                                            d'Ivoire</option>
                                        <option value="Niger"
                                            {{ old('country', $user->country) === 'Niger' ? 'selected' : '' }}>Niger
                                        </option>
                                        <option value="Guinea"
                                            {{ old('country', $user->country) === 'Guinea' ? 'selected' : '' }}>Guinée
                                        </option>
                                        <option value="Mauritanie"
                                            {{ old('country', $user->country) === 'Mauritanie' ? 'selected' : '' }}>
                                            Mauritanie</option>
                                        <option value="France"
                                            {{ old('country', $user->country) === 'France' ? 'selected' : '' }}>France
                                        </option>
                                        <option value="Belgique"
                                            {{ old('country', $user->country) === 'Belgique' ? 'selected' : '' }}>Belgique
                                        </option>
                                        <option value="Canada"
                                            {{ old('country', $user->country) === 'Canada' ? 'selected' : '' }}>Canada
                                        </option>
                                        <option value="Autre"
                                            {{ old('country', $user->country) === 'Autre' ? 'selected' : '' }}>Autre
                                        </option>
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end pt-3" style="border-top: 1px solid #e9ecef;">
                                <button type="submit" class="btn btn-sage-primary btn-lg px-4">
                                    <i class="fas fa-save me-2"></i>Mettre à jour le profil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modification du mot de passe -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-sage-light border-0">
                        <h5 class="mb-0 text-sage-primary">
                            <i class="fas fa-shield-alt me-2"></i>Sécurité
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dashboard.profile.password') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="current_password" class="form-label text-sage-primary">
                                    <i class="fas fa-lock me-2"></i>Mot de passe actuel <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                    class="form-control border-sage @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label text-sage-primary">
                                    <i class="fas fa-key me-2"></i>Nouveau mot de passe <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                    class="form-control border-sage @error('new_password') is-invalid @enderror"
                                    id="new_password" name="new_password" required minlength="8">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Le mot de passe doit contenir au moins 8 caractères.</div>
                            </div>

                            <div class="mb-4">
                                <label for="new_password_confirmation" class="form-label text-sage-primary">
                                    <i class="fas fa-check-double me-2"></i>Confirmer le mot de passe <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control border-sage" id="new_password_confirmation"
                                    name="new_password_confirmation" required minlength="8">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-sage-secondary btn-lg">
                                    <i class="fas fa-sync-alt me-2"></i>Changer le mot de passe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques du compte -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-sage-light border-0">
                        <h5 class="mb-0 text-sage-primary">
                            <i class="fas fa-chart-bar me-2"></i>Statistiques de votre compte
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="stat-card p-4 bg-gradient-primary">
                                    <i class="fas fa-calendar-check text-white mb-3" style="font-size: 2.5rem;"></i>
                                    <h3 class="text-white mb-2">{{ $user->bookings()->count() }}</h3>
                                    <p class="text-white-50 mb-0">Réservations totales</p>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="stat-card p-4 bg-gradient-success">
                                    <i class="fas fa-check-circle text-white mb-3" style="font-size: 2.5rem;"></i>
                                    <h3 class="text-white mb-2">
                                        {{ $user->bookings()->where('status', 'completed')->count() }}</h3>
                                    <p class="text-white-50 mb-0">Séjours terminés</p>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="stat-card p-4 bg-gradient-warning">
                                    <i class="fas fa-money-bill-wave text-white mb-3" style="font-size: 2.5rem;"></i>
                                    <h3 class="text-white mb-2">
                                        {{ number_format($user->bookings()->whereIn('status', ['confirmed', 'completed'])->sum('total_price'),0,',',' ') }}
                                    </h3>
                                    <p class="text-white-50 mb-0">FCFA dépensés</p>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="stat-card p-4 bg-gradient-info">
                                    <i class="fas fa-user-plus text-white mb-3" style="font-size: 2.5rem;"></i>
                                    <h3 class="text-white mb-2">{{ $user->created_at->format('m/Y') }}</h3>
                                    <p class="text-white-50 mb-0">Membre depuis</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid #dee2e6;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .text-sage-primary {
            color: var(--green-dark) !important;
        }

        .btn-sage-primary {
            background: linear-gradient(135deg, var(--green-dark), #4CAF50);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-sage-primary:hover {
            background: linear-gradient(135deg, #4CAF50, var(--green-dark));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
        }

        .btn-sage-secondary {
            background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-sage-secondary:hover {
            background: linear-gradient(135deg, var(--gold-end), var(--gold-start));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.3);
        }

        .bg-sage-light {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
        }

        .border-sage {
            border: 2px solid #e9ecef !important;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .border-sage:focus {
            border-color: var(--gold-end) !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.15) !important;
        }

        .card {
            border-radius: 15px;
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
        }

        .stat-card {
            border-radius: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
            pointer-events: none;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff, #0056b3) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745, #1e7e34) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8, #117a8b) !important;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem;
                text-align: center;
            }

            .dashboard-header .d-flex {
                flex-direction: column;
                align-items: center !important;
            }

            .dashboard-avatar {
                margin-bottom: 1rem;
                margin-right: 0 !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Validation côté client pour les mots de passe
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('new_password_confirmation');

            function validatePassword() {
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas.');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }

            newPassword.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);
        });
    </script>
@endpush
