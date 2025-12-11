@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe - Sages Home')

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
    }

    .auth-header h1 {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .auth-header p {
        margin: 0;
        opacity: 0.9;
    }

    .auth-body {
        padding: 2rem;
    }

    .form-control {
        padding: 0.875rem 1rem;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--sage-green-primary);
        box-shadow: 0 0 0 0.2rem rgba(47, 74, 51, 0.1);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary));
        color: white;
        padding: 0.875rem 2rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(47, 74, 51, 0.3);
        color: white;
    }

    .alert {
        border-radius: 10px;
        border: none;
    }

    .password-requirements {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
    }

    .password-requirements ul {
        margin: 0.5rem 0 0 0;
        padding-left: 1.5rem;
    }

    .password-requirements li {
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .toggle-password {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }

    .toggle-password:hover {
        color: var(--sage-green-primary);
    }
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><i class="fas fa-key"></i> Nouveau mot de passe</h1>
            <p>Choisissez un mot de passe sécurisé</p>
        </div>

        <div class="auth-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="password-requirements">
                <strong><i class="fas fa-shield-alt"></i> Exigences du mot de passe :</strong>
                <ul>
                    <li>Au moins 8 caractères</li>
                    <li>Utilisez un mélange de lettres et de chiffres</li>
                    <li>Évitez les mots de passe évidents</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           value="{{ $email }}" 
                           disabled>
                </div>

                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           required 
                           autofocus
                           placeholder="••••••••">
                    <span class="toggle-password" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 position-relative">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" 
                           class="form-control" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           placeholder="••••••••">
                    <span class="toggle-password" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye" id="password_confirmation-icon"></i>
                    </span>
                </div>

                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-check"></i> Réinitialiser le mot de passe
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection
