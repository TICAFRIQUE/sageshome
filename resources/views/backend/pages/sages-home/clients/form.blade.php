@extends('backend.layouts.master')

@php
    $editing = isset($client);
    $countries = ["Côte d'Ivoire", 'Sénégal', 'Mali', 'Burkina Faso', 'Guinée', 'Togo', 'Bénin', 'Ghana', 'Cameroun', 'Maroc', 'France', 'Belgique', 'Canada', 'États-Unis', 'Autre'];
    $currentCountry = old('country', $client->country ?? "Côte d'Ivoire");
@endphp

@section('title', $editing ? 'Modifier le client' : 'Nouveau client')

@section('content')
    <div class="sh-page-head">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                <li class="breadcrumb-item active">{{ $editing ? 'Modifier' : 'Nouveau' }}</li>
            </ol>
            <h1>{{ $editing ? 'Modifier ' . $client->username : 'Nouveau client' }}</h1>
        </div>
        @if ($editing)
            <div class="sh-actions">
                <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-light"><i class="ri-user-line me-1"></i>Voir la fiche</a>
            </div>
        @endif
    </div>

    <div class="row justify-content-center g-3">
        <div class="col-xl-8">
            <form action="{{ $editing ? route('admin.clients.update', $client) : route('admin.clients.store') }}"
                method="POST" class="card needs-validation" novalidate autocomplete="off">
                @csrf
                @if ($editing)
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

                    <div class="form-section-title mt-0"><i class="ri-user-line"></i>Identité</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username"
                                value="{{ old('username', $client->username ?? '') }}" placeholder="Ex. Kouadio Jean" required autofocus>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Le nom est obligatoire.</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-phone-line"></i></span>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                    value="{{ old('phone', $client->phone ?? '') }}" placeholder="07 00 00 00 00" inputmode="tel">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="email" class="form-label">Adresse e-mail <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                    value="{{ old('email', $client->email ?? '') }}" placeholder="client@exemple.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Saisissez une adresse e-mail valide.</div>@enderror
                            </div>
                            <div class="form-text">Sert d'identifiant de connexion sur le site.</div>
                        </div>
                    </div>

                    @unless ($editing)
                        <div class="form-section-title"><i class="ri-lock-password-line"></i>Mot de passe</div>
                        <div class="password-box">
                            <label for="password" class="form-label">Mot de passe généré</label>
                            <div class="input-group">
                                <input type="text" class="form-control password-input @error('password') is-invalid @enderror" id="password" name="password"
                                    value="{{ old('password', $generatedPassword ?? '') }}" minlength="8" maxlength="64" spellcheck="false"
                                    autocomplete="new-password" aria-describedby="passwordHelp">
                                <button type="button" class="btn btn-light" id="regenerate-password" title="Générer un autre mot de passe" data-bs-toggle="tooltip">
                                    <i class="ri-refresh-line"></i>
                                </button>
                                <button type="button" class="btn btn-light" id="copy-password" title="Copier" data-bs-toggle="tooltip">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-text" id="passwordHelp">
                                8 caractères, lettres majuscules et chiffres. Il sera réaffiché après l'enregistrement
                                pour être transmis au client (e-mail, WhatsApp…).
                            </div>
                        </div>
                    @endunless

                    <div class="form-section-title"><i class="ri-map-pin-line"></i>Adresse</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="address" class="form-label">Adresse</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', $client->address ?? '') }}" placeholder="Quartier, rue, repère…">
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label">Ville</label>
                            <input type="text" class="form-control" id="city" name="city"
                                value="{{ old('city', $client->city ?? '') }}" placeholder="Abidjan">
                        </div>
                        <div class="col-md-6">
                            <label for="country" class="form-label">Pays</label>
                            <select class="form-select" id="country" name="country">
                                <option value="">Non précisé</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country }}" @selected($currentCountry === $country)>{{ $country }}</option>
                                @endforeach
                                @if ($currentCountry && !in_array($currentCountry, $countries))
                                    <option value="{{ $currentCountry }}" selected>{{ $currentCountry }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    @if ($editing)
                        <div class="form-section-title"><i class="ri-settings-3-line"></i>Compte</div>
                        <input type="hidden" name="email_verified_present" value="1">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="email_verified" name="email_verified" value="1"
                                @checked(old('email_verified', $client->email_verified_at ? 1 : 0))>
                            <label class="form-check-label" for="email_verified">Adresse e-mail vérifiée</label>
                        </div>
                        <div class="form-text">
                            @if ($client->email_verified_at)
                                Vérifiée le {{ $client->email_verified_at->format('d/m/Y à H:i') }}
                            @else
                                Pas encore vérifiée
                            @endif
                        </div>
                    @endif
                </div>

                <div class="card-footer d-flex justify-content-between gap-2">
                    <a href="{{ $editing ? route('admin.clients.show', $client) : route('admin.clients.index') }}" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ri-save-line me-1"></i>{{ $editing ? 'Enregistrer' : 'Créer le client' }}
                    </button>
                </div>
            </form>
        </div>

        @if ($editing)
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="ri-lock-password-line me-1"></i>Mot de passe</h5></div>
                    <div class="card-body">
                        <p class="text-muted small">Génère un nouveau mot de passe de 8 caractères (majuscules et chiffres). L'ancien ne fonctionnera plus.</p>
                        <form method="POST" action="{{ route('admin.clients.reset-password', $client) }}"
                            data-confirm="Réinitialiser le mot de passe de {{ $client->username }} ? L'ancien mot de passe ne fonctionnera plus."
                            data-confirm-button="Réinitialiser" data-confirm-danger="true">
                            @csrf
                            <button type="submit" class="btn btn-soft-warning w-100"><i class="ri-key-2-line me-1"></i>Réinitialiser le mot de passe</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .password-box { padding: 1rem; border-radius: 12px; background: var(--sh-gold-soft); border: 1px dashed rgba(194, 155, 50, .45); }
        .password-input { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 1.1rem; letter-spacing: .18em; font-weight: 700; text-transform: uppercase; }
    </style>
@endpush

@section('script')
    <script>
        (function () {
            var input = document.getElementById('password');
            if (!input) return;
            var LETTERS = 'ABCDEFGHJKLMNPQRSTUVWXYZ', DIGITS = '23456789';

            function rand(max) {
                var a = new Uint32Array(1);
                window.crypto.getRandomValues(a);
                return a[0] % max;
            }
            function pick(s) { return s.charAt(rand(s.length)); }
            function generate() {
                var chars = [pick(LETTERS), pick(LETTERS), pick(DIGITS), pick(DIGITS)];
                while (chars.length < 8) chars.push(pick(LETTERS + DIGITS));
                for (var i = chars.length - 1; i > 0; i--) { var j = rand(i + 1); var t = chars[i]; chars[i] = chars[j]; chars[j] = t; }
                return chars.join('');
            }

            input.addEventListener('input', function () { this.value = this.value.toUpperCase().replace(/\s/g, ''); });

            document.getElementById('regenerate-password').addEventListener('click', function () {
                input.value = generate();
                input.classList.remove('is-invalid');
                input.focus();
                input.select();
            });

            document.getElementById('copy-password').addEventListener('click', function () {
                var btn = this;
                navigator.clipboard.writeText(input.value).then(function () {
                    btn.innerHTML = '<i class="ri-check-line text-success"></i>';
                    setTimeout(function () { btn.innerHTML = '<i class="ri-file-copy-line"></i>'; }, 1500);
                });
            });
        })();
    </script>
@endsection
