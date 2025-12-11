@extends('layouts.app')

@section('title', 'Mot de passe oublié - Sages Home')

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

    .back-link {
        text-align: center;
        margin-top: 1.5rem;
    }

    .back-link a {
        color: var(--sage-green-primary);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .back-link a:hover {
        color: var(--sage-green-dark);
    }

    .info-text {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><i class="fas fa-lock"></i> Mot de passe oublié ?</h1>
            <p>Réinitialisez votre mot de passe</p>
        </div>

        <div class="auth-body">
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="info-text">
                <i class="fas fa-info-circle"></i> Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           placeholder="votre@email.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-paper-plane"></i> Envoyer le lien de réinitialisation
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
