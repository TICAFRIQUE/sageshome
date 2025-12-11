@extends('layouts.app')

@section('title', 'Connexion - Sages Home')

@push('styles')
<style>
    .auth-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 450px;
        width: 100%;
    }

    .auth-header {
        background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary));
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .auth-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(45deg);
        animation: shine 3s infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    }

    .auth-header h3 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 600;
        color: white;
        position: relative;
        z-index: 2;
    }

    .auth-header .subtitle {
        margin-top: 0.5rem;
        opacity: 0.9;
        font-size: 0.95rem;
        position: relative;
        z-index: 2;
    }

    .auth-body {
        padding: 2.5rem 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .form-label {
        font-weight: 600;
        color: var(--sage-green-dark);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .form-control:focus {
        border-color: var(--sage-green-secondary);
        box-shadow: 0 0 0 0.2rem rgba(74, 107, 66, 0.15);
        background: white;
    }

    .password-field {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .password-toggle:hover {
        color: var(--sage-green-dark);
        background: #f8f9fa;
    }

    .form-check {
        margin: 1.5rem 0;
    }

    .form-check-input {
        border-radius: 6px;
        border: 2px solid #dee2e6;
        margin-top: 0.1em;
    }

    .form-check-input:checked {
        background-color: var(--sage-green-secondary);
        border-color: var(--sage-green-secondary);
    }

    .form-check-label {
        color: #495057;
        font-size: 0.95rem;
    }

    .btn-auth {
        background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary));
        border: none;
        border-radius: 12px;
        padding: 0.875rem 2rem;
        font-weight: 600;
        font-size: 1.05rem;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-auth::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-auth:hover {
        background: linear-gradient(135deg, var(--sage-green-secondary), var(--sage-green-dark));
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(47, 74, 51, 0.3);
        color: white;
    }

    .btn-auth:hover::before {
        left: 100%;
    }

    .btn-auth:active {
        transform: translateY(0);
        box-shadow: 0 4px 15px rgba(47, 74, 51, 0.3);
    }

    .auth-links {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .auth-links a {
        color: var(--sage-green-dark);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .auth-links a:hover {
        color: white;
    }

    .logo-section {
        text-align: center;
        margin-bottom: 2rem;
    }

    .logo-section img {
        height: 60px;
        margin-bottom: 1rem;
    }

    .logo-section .brand-text {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        color: var(--sage-green-dark);
        font-size: 1.5rem;
        margin: 0;
    }

    /* Animation pour les erreurs */
    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.5rem;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        animation: shake 0.5s ease-in-out;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .auth-container {
            padding: 1rem;
            min-height: 70vh;
        }
        
        .auth-header {
            padding: 1.5rem;
        }
        
        .auth-body {
            padding: 2rem 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="auth-container pt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="auth-card mx-auto">
                    <!-- Logo Section -->
                   
                    <!-- Header -->
                    <div class="auth-header">
                        <h3>Connexion</h3>
                        <p class="subtitle mb-0">Accédez à votre espace personnel</p>
                    </div>
                    
                    <!-- Body -->
                    <div class="auth-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="votre@email.com"
                                       required 
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Mot de passe
                                </label>
                                <div class="password-field">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Votre mot de passe"
                                           required>
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    <i class="fas fa-user-check me-1"></i>Se souvenir de moi
                                </label>
                            </div> --}}

                            <div class="d-flex justify-content-end mb-3">
                                <a href="{{ route('password.request') }}" class="text-muted" style="font-size: 0.9rem;">
                                    <i class="fas fa-lock me-1"></i>Mot de passe oublié ?
                                </a>
                            </div>

                            <button type="submit" class="btn btn-auth w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </button>
                            
                            <div class="auth-links">
                                <p class="mb-0 text-muted">
                                    Vous n'avez pas de compte ? 
                                    <a href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1"></i>S'inscrire
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonctionnalité toggle password
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (togglePassword && passwordInput && eyeIcon) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    }

    // Animation au focus des champs
    const formControls = document.querySelectorAll('.form-control');
    formControls.forEach(function(control) {
        control.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        control.addEventListener('blur', function() {
            if (!this.value) {
                this.parentNode.classList.remove('focused');
            }
        });
    });

    // Auto-hide alerts après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
    });
});
</script>
@endpush