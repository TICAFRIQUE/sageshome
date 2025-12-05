<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sages Home - Résidences de luxe')</title>
    <meta name="description" content="@yield('meta_description', 'Découvrez nos résidences de luxe avec services premium. Réservez votre séjour d\'exception avec Sages Home.')">


    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --sage-gold-start: #F2D18A;
            --sage-gold-end: #C29B32;
            --sage-green-dark: #2F4A33;
            --sage-green-secondary: #4A6B42;
            --sage-bg-light: #F8F8F8;
            --sage-text-secondary: #888888;
            --sage-black-premium: #1A1A1A;
            --sage-white: #FFFFFF;
            --sage-border: #e9ecef;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--sage-black-premium);
            background-color: var(--sage-white);
            line-height: 1.6;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--sage-green-dark);
        }

        /* Couleurs personnalisées */
        .text-gold {
            color: var(--sage-gold-end) !important;
        }

        .text-green {
            color: var(--sage-green-dark) !important;
        }

        .text-green-secondary {
            color: var(--sage-green-secondary) !important;
        }

        .bg-gold {
            background-color: var(--sage-gold-start) !important;
        }

        .bg-green {
            background-color: var(--sage-green-dark) !important;
        }

        .badge-gold {
            background-color: var(--sage-gold-start) !important;
            color: var(--sage-green-dark) !important;
        }

        /* Navbar styles */
        .navbar {
            background: var(--sage-white);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 16px 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
        }

        /* Sections de la navbar */
        .navbar-brand-section {
            flex: 0 0 auto;
        }

        .search-section {
            flex: 1 1 auto;
            max-width: 700px;
            margin: 0 2rem;
        }

        .navbar-icons-section {
            flex: 0 0 auto;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 45px;
            margin-right: 12px;
        }

        .navbar-brand .brand-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--sage-green-dark);
            font-size: 1.5rem;
            margin: 0;
        }

        .search-form {
            background: var(--sage-white);
            border-radius: 15px;
            padding: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 2px solid var(--sage-gold-start);
            width: 100%;
            max-width: 800px;
        }

        .search-form .form-control,
        .search-form .form-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 12px;
            font-family: 'Inter', sans-serif;
            background: var(--sage-white);
            min-height: 42px;
            transition: all 0.3s ease;
        }

        .search-form .form-control:focus,
        .search-form .form-select:focus {
            box-shadow: 0 0 0 2px rgba(47, 74, 51, 0.15);
            background: var(--sage-white);
            border-color: var(--sage-green-secondary);
        }

        .search-form .form-label {
            color: var(--sage-green-dark);
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 0.8rem;
        }

        .search-form .btn {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            background: linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end));
            border: none;
            color: var(--sage-white);
            min-height: 42px;
            transition: all 0.3s ease;
        }

        .search-form .btn:hover {
            background: linear-gradient(135deg, var(--sage-gold-end), var(--sage-gold-start));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(194, 155, 50, 0.3);
        }

        /* Texte de recherche */
        .search-text {
            margin-bottom: 0.5rem;
        }

        .search-subtitle {
            font-size: 0.9rem;
            color: var(--sage-green-dark);
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            opacity: 0.8;
            letter-spacing: 0.5px;
        }

        .contact-icon {
            background: linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end));
            color: var(--sage-white);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
        }

        .contact-icon:hover {
            background: linear-gradient(135deg, var(--sage-gold-end), var(--sage-gold-start));
            transform: scale(1.1);
            color: var(--sage-white);
        }

        .auth-links a {
            color: var(--sage-green-dark);
            text-decoration: none;
            margin: 0 12px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 8px;
        }

        .auth-links a:hover {
            color: var(--sage-white);
            background: var(--sage-green-secondary);
        }

        /* Icônes de navigation */
        .nav-icon-btn {
            background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary));
            color: var(--sage-white);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 1.1rem;
        }

        .nav-icon-btn:hover {
            background: linear-gradient(135deg, var(--sage-green-secondary), var(--sage-green-dark));
            transform: scale(1.1);
            color: var(--sage-white);
            box-shadow: 0 4px 15px rgba(47, 74, 51, 0.3);
        }

        .nav-icon-btn:focus {
            box-shadow: 0 0 0 3px rgba(47, 74, 51, 0.2);
            outline: none;
        }

        /* Dropdown de compte moderne */
        .account-dropdown {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
            min-width: 250px;
            margin-top: 10px;
        }

        .account-dropdown .dropdown-header {
            background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary));
            color: var(--sage-white);
            padding: 1rem 1.25rem;
            margin: 0 0 0.5rem 0;
            border-radius: 0;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .account-dropdown .dropdown-item {
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border: none;
        }

        .account-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end));
            color: var(--sage-white);
            transform: translateX(5px);
        }

        .account-dropdown .dropdown-item.text-danger:hover {
            background: rgb(200, 198, 198);
            color: rgb(12, 1, 1);
        }

        .account-dropdown .dropdown-divider {
            margin: 0.5rem 1rem;
            border-color: var(--sage-border);
        }

        /* Hero section */
        .hero-banner {
            height: 70vh;
            background: linear-gradient(rgba(47, 74, 51, 0.6), rgba(47, 74, 51, 0.6)), url('/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            color: var(--sage-white);
        }

        .hero-banner h1 {
            color: var(--sage-white);
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }

        .hero-banner .lead {
            font-size: 1.25rem;
            margin-bottom: 2rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end));
            border: none;
            font-weight: 500;
            padding: 12px 30px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--sage-gold-end), var(--sage-gold-start));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(194, 155, 50, 0.4);
        }

        .btn-outline-primary {
            color: var(--sage-gold-end);
            border-color: var(--sage-gold-end);
            font-weight: 500;
            padding: 12px 30px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end));
            border-color: var(--sage-gold-start);
            color: var(--sage-white);
            transform: translateY(-2px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            border-radius: 12px 12px 0 0;
            height: 220px;
            object-fit: cover;
        }

        /* Footer */
        .footer {
            background-color: var(--sage-green-dark);
            color: var(--sage-white);
            padding: 4rem 0 2rem;
            margin-top: 5rem;
        }

        .footer h5 {
            color: var(--sage-white);
            margin-bottom: 1.5rem;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: var(--sage-white);
        }

        /* Payment methods */
        .payment-method {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            transition: all 0.3s ease;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 100px;
        }

        .payment-method:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-3px);
        }

        .payment-method img {
            max-height: 40px;
            object-fit: contain;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 8px 0;
            }

            main {
                padding-top: 60px !important;
            }

            .navbar-brand img {
                height: 35px;
            }

            .navbar-brand .brand-text {
                font-size: 1.2rem;
            }

            .search-form {
                margin-top: 15px;
                padding: 15px;
            }

            .hero-banner h1 {
                font-size: 2rem;
            }

            .search-section {
                margin: 0;
            }

            .navbar-icons-section {
                gap: 10px !important;
            }
        }

        /* Boutons flottants */
        .floating-buttons {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .floating-btn {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1.4rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: none;
            cursor: pointer;
            backdrop-filter: blur(10px);
        }

        .floating-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        }

        .floating-btn:active {
            transform: translateY(0);
        }

        /* Bouton retour en haut */
        .back-to-top {
            background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary));
            color: white;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: linear-gradient(135deg, var(--sage-green-secondary), var(--sage-green-dark));
            color: white;
        }

        /* Bouton WhatsApp */
        .whatsapp-btn {
            background: #25D366;
            color: white;
            animation: whatsappPulse 2s infinite;
        }

        .whatsapp-btn:hover {
            background: #128C7E;
            color: white;
            animation: none;
        }

        /* Animation du bouton WhatsApp */
        @keyframes whatsappPulse {
            0% {
                box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3);
            }
            50% {
                box-shadow: 0 4px 25px rgba(37, 211, 102, 0.6), 0 0 0 15px rgba(37, 211, 102, 0.1);
            }
            100% {
                box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3);
            }
        }

        /* Responsive pour boutons flottants */
        @media (max-width: 768px) {
            .floating-buttons {
                right: 15px;
                bottom: 15px;
                gap: 12px;
            }
            
            .floating-btn {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <div class="d-flex w-100 align-items-center justify-content-between">
                <!-- Logo à gauche -->
                <div class="navbar-brand-section">
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Sages Home" style="height: 95px;"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                        {{-- <h4 class="brand-text d-none d-md-inline">SAGES HOME</h4> --}}
                    </a>
                </div>


                <!-- Formulaire de recherche au milieu (desktop) -->
                <div class="search-section d-none d-lg-flex flex-column justify-content-center flex-grow-1">
                    <div class="search-text text-center mb-2">
                        <span class="search-subtitle">Commencez votre recherche dès à présent</span>
                    </div>
                    <form class="search-form d-flex align-items-center" method="GET"
                        action="{{ route('search', ['residences']) }}">
                        <div class="row g-2 w-100 align-items-end">
                            <div class="col-2">
                                <label class="form-label small fw-medium text-dark mb-1">Arrivée</label>
                                <input type="date" class="form-control" name="check_in" required>
                            </div>
                            <div class="col-2">
                                <label class="form-label small fw-medium text-dark mb-1">Départ</label>
                                <input type="date" class="form-control" name="check_out" required>
                            </div>
                            <div class="col-2">
                                <label class="form-label small fw-medium text-dark mb-1">Voyageurs</label>
                                <select class="form-select" name="guests" required>
                                    <option value="">Choisir</option>
                                    <option value="1">1 voyageur</option>
                                    <option value="2">2 voyageurs</option>
                                    <option value="3">3 voyageurs</option>
                                    <option value="4">4 voyageurs</option>
                                    <option value="5">5+ voyageurs</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="form-label small fw-medium text-dark mb-1">Ville</label>
                                <select class="form-select" name="ville" id="header_ville">
                                    <option value="">Toutes</option>
                                    @php
                                        $villesCommunes = config('ville-commune');
                                        $availableVilles = collect(array_keys($villesCommunes));
                                    @endphp
                                    @foreach ($availableVilles as $ville)
                                        <option value="{{ $ville }}">{{ $ville }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="form-label small fw-medium text-dark mb-1">Commune</label>
                                <select class="form-select" name="commune" id="header_commune">
                                    <option value="">Toutes</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Icônes à droite -->
                <div class="navbar-icons-section d-flex align-items-center gap-3">
                    <!-- Icône d'accueil -->
                    <a href="{{ route('home') }}" class="nav-icon-btn" title="Retour à l'accueil">
                        <i class="fas fa-home"></i>
                    </a>

                    <!-- Icône de compte avec dropdown -->
                    <div class="dropdown">
                        <button class="nav-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" title="Mon compte">
                            <i class="fas fa-user"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end account-dropdown">
                            @auth
                                <li class="dropdown-header">
                                    <i class="fas fa-user-circle me-2"></i>
                                    {{ Auth::user()->name ?? 'Mon compte' }}
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                @role('developpeur')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dashboard.index') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                        </a>
                                    </li>
                                @endrole
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard.bookings') }}">
                                        <i class="fas fa-calendar-alt me-2"></i>Mes réservations
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard.profile') }}">
                                        <i class="fas fa-user-edit me-2"></i>Mon profil
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li class="dropdown-header">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Accès compte
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}">
                                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('register') }}?redirect={{ urlencode(request()->fullUrl()) }}">
                                        <i class="fas fa-user-plus me-2"></i>Inscription
                                    </a>
                                </li>
                            @endauth
                        </ul>
                    </div>



                    <!-- Menu toggle mobile -->
                    <button class="navbar-toggler nav-icon-btn d-lg-none border-0" type="button"
                        data-bs-toggle="collapse" data-bs-target="#navbarNav" title="Menu">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Icône de contact -->
                    <a href="/#contact" class="contact-icon" title="Nous contacter">
                        <i class="fas fa-phone"></i>
                    </a>
                </div>
            </div>

            <!-- Menu mobile -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="w-100 mt-3 d-lg-none">
                    <!-- Formulaire de recherche mobile -->
                    <form class="search-form mb-3" method="GET" action="{{ route('search', ['residences']) }}">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-medium text-dark mb-1">Arrivée</label>
                                <input type="date" class="form-control" name="check_in" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-medium text-dark mb-1">Départ</label>
                                <input type="date" class="form-control" name="check_out" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-medium text-dark mb-1">Voyageurs</label>
                                <select class="form-select" name="guests" required>
                                    <option value="">Choisir</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5+</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-medium text-dark mb-1">Ville</label>
                                <select class="form-select" name="ville" id="mobile_ville">
                                    <option value="">Toutes</option>
                                    @php
                                        $villesCommunes = config('ville-commune');
                                        $availableVilles = collect(array_keys($villesCommunes));
                                    @endphp
                                    @foreach ($availableVilles as $ville)
                                        <option value="{{ $ville }}">{{ $ville }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-medium text-dark mb-1">Commune</label>
                                <select class="form-select" name="commune" id="mobile_commune">
                                    <option value="">Toutes</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Navigation mobile -->
                    {{-- <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <i class="fas fa-home me-2"></i>Accueil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('residences.index') }}">
                                <i class="fas fa-building me-2"></i>Résidences
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">
                                <i class="fas fa-envelope me-2"></i>Contact
                            </a>
                        </li>
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}">
                                    <i class="fas fa-sign-in-alt me-2"></i>Connexion
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}?redirect={{ urlencode(request()->fullUrl()) }}">
                                    <i class="fas fa-user-plus me-2"></i>Inscription
                                </a>
                            </li>
                        @endguest
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard.index') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Mon compte
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard.bookings') }}">
                                    <i class="fas fa-calendar-alt me-2"></i>Mes réservations
                                </a>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                    @csrf
                                    <button type="submit" class="nav-link btn btn-link text-start text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        @endauth
                    </ul> --}}
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="padding-top: 120px;">
        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Sages Home" height="40" class="me-2">
                        <h5 class="mb-0">SAGES HOME</h5>
                    </div>
                    <p class="opacity-75">Vivez l'expérience du confort raffiné dans nos résidences sélectionnées avec
                        soin pour votre plus grand plaisir.</p>
                    <div class="social-links">
                        <a href="{{ $parametre?->lien_facebook }}" class="me-3"><i
                                class="fab fa-facebook fs-5"></i></a>
                        <a href="{{ $parametre?->lien_instagram }}" class="me-3"><i
                                class="fab fa-instagram fs-5"></i></a>
                        <a href="{{ $parametre?->lien_twitter }}" class="me-3"><i
                                class="fab fa-twitter fs-5"></i></a>
                        <a href="{{ $parametre?->lien_linkedin }}" class="me-3"><i
                                class="fab fa-linkedin fs-5"></i></a>
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <h5>Navigation</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}">Accueil</a></li>
                        <li class="mb-2"><a href="{{ route('residences.index') }}">Résidences</a></li>
                        <li class="mb-2"><a href="/#contact">Contact</a></li>
                        {{-- <li class="mb-2"><a href="#about">À propos</a></li> --}}
                    </ul>
                </div>

                <div class="col-md-3 mb-4">
                    <h5>Services</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#services">Nos services</a></li>
                        <li class="mb-2"><a href="#concierge">Conciergerie 24/7</a></li>
                        <li class="mb-2"><a href="#spa">Spa & Wellness</a></li>
                        <li class="mb-2"><a href="#restaurant">Restauration</a></li>
                    </ul>
                </div>

                <div class="col-md-3 mb-4">
                    <h5>Contact</h5>
                    <p class="opacity-75 mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ $parametre?->localisation ?? ' Cocody, Abidjan, Côte d\'Ivoire' }}

                    </p>
                    <p class="opacity-75 mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:{{ $parametre?->contact1  }}">
                            {{ $parametre?->contact1  }}</a>


                    </p>

                    <p class="opacity-75 mb-2">
                        <i class="fas fa-phone me-2"></i>

                        <a href="tel:{{ $parametre?->contact2  }}">
                            {{ $parametre?->contact2  }}</a>

                    </p>
                    <p class="opacity-75 mb-2">
                        <i class="fas fa-phone me-2"></i>

                        <a href="tel:{{ $parametre?->contact3  }}">
                            {{ $parametre?->contact3  }}</a>

                    </p>
                    <p class="opacity-75 mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:{{ $parametre?->email1  }}">{{ $parametre?->email1  }}</a> <br>
                    </p>
                    <p class="opacity-75 mb-2">
                        <i class="fas fa-envelope me-2"></i>
                      <a href="mailto:{{ $parametre?->email2  }}">{{ $parametre?->email2  }}</a>
                    </p>
                    <p class="opacity-75">
                        <i class="fas fa-clock me-2"></i>
                        Service 24h/24, 7j/7
                    </p>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <!-- Moyens de paiement -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h6 class="opacity-75 mb-3">Moyens de paiement acceptés</h6>
                    <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
                        <div class="payment-method">
                            <img src="https://lexxprint.com/wp-content/uploads/2022/03/wave.png" alt="Wave" height="40">
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-money-bill-wave fs-2 opacity-75"></i>
                            <small class="d-block opacity-75">Espèces</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="opacity-75 mb-0">&copy; {{ date('Y') }} Sages Home. Tous droits réservés. |
                        developed by <a href="https://www.ticafrique.ci/" target="_blank"
                            class="text-reset text-decoration-underline">Ticafrique</a></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="me-3 opacity-75">Politique de confidentialité</a>
                    <a href="#" class="opacity-75">Conditions d'utilisation</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Définir les dates minimales pour les champs de date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const checkInInputs = document.querySelectorAll('input[name="check_in"]');
            const checkOutInputs = document.querySelectorAll('input[name="check_out"]');

            checkInInputs.forEach(function(checkInInput, index) {
                checkInInput.min = today;
                checkInInput.addEventListener('change', function() {
                    const checkInDate = new Date(this.value);
                    const nextDay = new Date(checkInDate.getTime() + 24 * 60 * 60 * 1000);
                    if (checkOutInputs[index]) {
                        checkOutInputs[index].min = nextDay.toISOString().split('T')[0];
                    }
                });
            });

            // Gestion dynamique des communes
            const villesCommunes = @json(config('ville-commune'));

            // Pour le formulaire desktop
            const headerVille = document.getElementById('header_ville');
            const headerCommune = document.getElementById('header_commune');

            if (headerVille && headerCommune) {
                headerVille.addEventListener('change', function() {
                    updateCommunes(this.value, headerCommune);
                });
            }

            // Pour le formulaire mobile
            const mobileVille = document.getElementById('mobile_ville');
            const mobileCommune = document.getElementById('mobile_commune');

            if (mobileVille && mobileCommune) {
                mobileVille.addEventListener('change', function() {
                    updateCommunes(this.value, mobileCommune);
                });
            }

            function updateCommunes(selectedVille, communeSelect) {
                const communes = villesCommunes[selectedVille] || [];

                // Vider la liste des communes
                communeSelect.innerHTML = '<option value="">Toutes</option>';

                if (communes.length > 0) {
                    // Ajouter les communes disponibles
                    communes.forEach(function(commune) {
                        const option = document.createElement('option');
                        option.value = commune;
                        option.textContent = commune;
                        communeSelect.appendChild(option);
                    });
                    communeSelect.disabled = false;
                } else {
                    communeSelect.disabled = true;
                }
            }

            // Smooth scroll pour les liens d'ancrage
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>

    <!-- Boutons flottants -->
    <div class="floating-buttons">
        <!-- Bouton retour en haut -->
        <button type="button" class="floating-btn back-to-top" id="backToTop" title="Retour en haut">
            <i class="fas fa-chevron-up"></i>
        </button>
        
        <!-- Bouton WhatsApp -->
        <a href="https://wa.me/{{$parametre?->contact2}}?text=Bonjour, je suis intéressé(e) par vos résidences Sages Home." 
           target="_blank" 
           class="floating-btn whatsapp-btn" 
           title="Contactez-nous sur WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    <script>
        // Gestion du bouton retour en haut
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopBtn = document.getElementById('backToTop');
            
            // Afficher/masquer le bouton selon le scroll
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            });
            
            // Clic sur le bouton retour en haut
            backToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
