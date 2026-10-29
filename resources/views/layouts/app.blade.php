<!DOCTYPE html>
<html lang="fr" class="no-js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>document.documentElement.className = document.documentElement.className.replace('no-js', 'js');</script>

    {{-- SEO --}}
    <title>@yield('title', 'Sages Home - Résidences meublées haut de gamme à Abidjan')</title>
    <meta name="description" content="@yield('meta_description', 'Réservez une résidence meublée haut de gamme à Abidjan avec Sages Home : appartements et studios équipés, sécurisés, avec assistance 24h/24.')">
    <meta name="keywords" content="@yield('meta_keywords', 'résidence meublée Abidjan, location courte durée Côte d\'Ivoire, appartement meublé Cocody, studio meublé, Sages Home')">
    <meta name="author" content="Sages Home">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="@yield('canonical', request()->url())">

    {{-- Open Graph / Twitter --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', request()->url())">
    <meta property="og:title" content="@yield('og_title', 'Sages Home - Résidences meublées à Abidjan')">
    <meta property="og:description" content="@yield('og_description', 'Appartements et studios meublés haut de gamme, réservables en ligne.')">
    <meta property="og:image" content="@yield('og_image', asset('images/hero-1100.jpg'))">
    <meta property="og:site_name" content="Sages Home">
    <meta property="og:locale" content="fr_FR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'Sages Home - Résidences meublées')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Appartements et studios meublés haut de gamme à Abidjan.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/hero-1100.jpg'))">

    {{-- Mobile & thème --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Sages Home">
    <meta name="theme-color" content="#37542A">

    {{-- Favicons --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">

    {{-- Connexions anticipées vers les CDN --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preload" as="image" href="{{ asset('images/logo-mark.webp') }}" type="image/webp">
    @stack('preload')

    {{-- Polices (graisses limitées au strict nécessaire) --}}
    <link rel="stylesheet" media="print" onload="this.media='all'"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap"></noscript>

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-5.3.3/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    {{-- Bootstrap Icons : non bloquant (utilisé seulement sur quelques pages) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </noscript>
    <link rel="stylesheet" href="{{ asset('css/front.css') }}?v={{ filemtime(public_path('css/front.css')) }}">

    @stack('styles')
</head>

@php
    $waNumber = preg_replace('/\D+/', '', (string) ($parametre?->contact2 ?: $parametre?->contact1));
    if ($waNumber && strlen($waNumber) <= 10) {
        $waNumber = '225' . $waNumber;
    }
    $phone = $parametre?->contact1 ?: $parametre?->contact2;
    $socials = collect([
        'facebook-f' => $parametre?->lien_facebook,
        'instagram' => $parametre?->lien_instagram,
        'x-twitter' => $parametre?->lien_twitter,
        'linkedin-in' => $parametre?->lien_linkedin,
    ])->filter();
@endphp

<body class="@yield('body_class')">
    <a href="#main-content" class="skip-link">Aller au contenu</a>
    <div id="page-progress" aria-hidden="true"></div>

    {{-- ================= HEADER ================= --}}
    <header class="site-header">
        <div class="container d-flex align-items-center justify-content-between gap-3">
            <a class="brand" href="{{ route('home') }}" aria-label="Sages Home - Accueil">
                <picture>
                    <source srcset="{{ asset('images/logo-mark.webp') }}" type="image/webp">
                    <img src="{{ asset('images/logo-mark.png') }}" alt="" class="brand-mark" width="44" height="44">
                </picture>
                <span>
                    <span class="brand-name">SAGES HOME</span>
                    <span class="brand-tagline">Immo · Résidences · Voyages</span>
                </span>
            </a>

            {{-- Navigation desktop --}}
            <nav class="d-none d-lg-flex main-nav" aria-label="Navigation principale">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"
                    @if (request()->routeIs('home')) aria-current="page" @endif>Accueil</a>
                <a class="nav-link {{ request()->routeIs('residences.*') ? 'active' : '' }}"
                    href="{{ route('residences.index') }}"
                    @if (request()->routeIs('residences.*')) aria-current="page" @endif>Nos résidences</a>
                <a class="nav-link" href="{{ route('home') }}#contact">Contact</a>
            </nav>

            <div class="d-flex align-items-center gap-2">
                {{-- Recherche --}}
                <button class="header-search-trigger d-none d-md-inline-flex" type="button" data-bs-toggle="collapse"
                    data-bs-target="#searchPanel" aria-expanded="false" aria-controls="searchPanel">
                    <span>Où et quand ?</span>
                    <span class="trigger-icon"><i class="fas fa-search"></i></span>
                </button>
                <button class="header-icon-btn d-md-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#searchPanel" aria-expanded="false" aria-controls="searchPanel"
                    aria-label="Rechercher une résidence">
                    <i class="fas fa-search"></i>
                </button>

                {{-- Compte --}}
                <div class="dropdown d-none d-lg-block">
                    <button class="header-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" aria-label="Mon compte">
                        @auth
                            <span class="account-avatar">{{ Str::upper(Str::substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                        @else
                            <i class="fas fa-user"></i>
                        @endauth
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end account-dropdown">
                        @auth
                            <li class="dropdown-header">
                                <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name ?? 'Mon compte' }}
                            </li>
                            @role('developpeur')
                                <li><a class="dropdown-item" href="{{ route('dashboard.index') }}"><i class="fas fa-gauge me-2"></i>Tableau de bord</a></li>
                            @endrole
                            <li><a class="dropdown-item" href="{{ route('dashboard.bookings') }}"><i class="fas fa-calendar-alt me-2"></i>Mes réservations</a></li>
                            <li><a class="dropdown-item" href="{{ route('dashboard.profile') }}"><i class="fas fa-user-pen me-2"></i>Mon profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-right-from-bracket me-2"></i>Se déconnecter</button>
                                </form>
                            </li>
                        @else
                            <li class="dropdown-header">Bienvenue chez Sages Home</li>
                            <li><a class="dropdown-item" href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"><i class="fas fa-right-to-bracket me-2"></i>Se connecter</a></li>
                            <li><a class="dropdown-item" href="{{ route('register') }}?redirect={{ urlencode(request()->fullUrl()) }}"><i class="fas fa-user-plus me-2"></i>Créer un compte</a></li>
                        @endauth
                    </ul>
                </div>

                @if ($phone)
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="btn btn-green d-none d-xl-inline-flex align-items-center rounded-pill px-3">
                        <i class="fas fa-phone me-2"></i>{{ $phone }}
                    </a>
                @endif

                {{-- Menu mobile --}}
                <button class="header-icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-label="Ouvrir le menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- Panneau de recherche --}}
    <div class="collapse search-panel" id="searchPanel">
        <div class="container search-panel-inner">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="h5 mb-0">Trouvez votre résidence</p>
                <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#searchPanel"
                    aria-label="Fermer la recherche"></button>
            </div>
            @include('frontend.partials.search-form', ['prefix' => 'panel'])
        </div>
    </div>

    {{-- Menu mobile (offcanvas) --}}
    <div class="offcanvas offcanvas-end mobile-menu" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header">
            <a class="brand" href="{{ route('home') }}" id="mobileMenuLabel">
                <img src="{{ asset('images/logo-mark.webp') }}" alt="" class="brand-mark" width="40" height="40">
                <span class="brand-name">SAGES HOME</span>
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer le menu"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <nav aria-label="Navigation mobile">
                <a class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fas fa-house"></i>Accueil</a>
                <a class="mobile-nav-link {{ request()->routeIs('residences.*') ? 'active' : '' }}" href="{{ route('residences.index') }}"><i class="fas fa-building"></i>Nos résidences</a>
                <a class="mobile-nav-link" href="{{ route('home') }}#contact"><i class="fas fa-envelope"></i>Contact</a>
            </nav>

            <p class="menu-section-title">Mon compte</p>
            @auth
                <a class="mobile-nav-link" href="{{ route('dashboard.bookings') }}"><i class="fas fa-calendar-alt"></i>Mes réservations</a>
                <a class="mobile-nav-link" href="{{ route('dashboard.profile') }}"><i class="fas fa-user-pen"></i>Mon profil</a>
                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                    @csrf
                    <button type="submit" class="mobile-nav-link w-100 border-0 bg-transparent text-danger"><i class="fas fa-right-from-bracket text-danger"></i>Se déconnecter</button>
                </form>
            @else
                <a class="mobile-nav-link" href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"><i class="fas fa-right-to-bracket"></i>Se connecter</a>
                <a class="mobile-nav-link" href="{{ route('register') }}?redirect={{ urlencode(request()->fullUrl()) }}"><i class="fas fa-user-plus"></i>Créer un compte</a>
            @endauth

            <div class="mt-auto pt-4 d-grid gap-2">
                <a href="{{ route('residences.index') }}" class="btn btn-primary btn-lg">Réserver une résidence</a>
                @if ($phone)
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="btn btn-outline-primary"><i class="fas fa-phone me-2"></i>{{ $phone }}</a>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= CONTENU ================= --}}
    <main id="main-content" tabindex="-1">
        @if (session('success') || session('error'))
            <div class="container pt-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show flash-alert mb-0" role="alert">
                        <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show flash-alert mb-0" role="alert">
                        <i class="fas fa-circle-exclamation me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('images/logo-mark.webp') }}" alt="" width="40" height="40" loading="lazy">
                        <span class="footer-brand-name">SAGES HOME</span>
                    </div>
                    <p class="mb-4">Des résidences meublées haut de gamme, sélectionnées avec soin, pour des séjours
                        confortables et sans souci en Côte d'Ivoire.</p>
                    @if ($socials->isNotEmpty())
                        <div class="d-flex gap-2">
                            @foreach ($socials as $icon => $link)
                                <a href="{{ $link }}" class="social-btn" target="_blank" rel="noopener"
                                    aria-label="{{ ucfirst(Str::before($icon, '-')) }}"><i class="fab fa-{{ $icon }}"></i></a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-6 col-lg-2">
                    <h2>Navigation</h2>
                    <ul class="list-unstyled footer-links mb-0">
                        <li><a href="{{ route('home') }}">Accueil</a></li>
                        <li><a href="{{ route('residences.index') }}">Nos résidences</a></li>
                        <li><a href="{{ route('home') }}#pourquoi">Pourquoi nous choisir</a></li>
                        <li><a href="{{ route('home') }}#contact">Contact</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-2">
                    <h2>Mon espace</h2>
                    <ul class="list-unstyled footer-links mb-0">
                        @auth
                            <li><a href="{{ route('dashboard.bookings') }}">Mes réservations</a></li>
                            <li><a href="{{ route('dashboard.profile') }}">Mon profil</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Se connecter</a></li>
                            <li><a href="{{ route('register') }}">Créer un compte</a></li>
                        @endauth
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h2>Nous joindre</h2>
                    <ul class="list-unstyled footer-contact mb-0">
                        @if ($parametre?->localisation)
                            <li><i class="fas fa-location-dot"></i><span>{{ $parametre->localisation }}</span></li>
                        @endif
                        @foreach (array_filter([$parametre?->contact1, $parametre?->contact2, $parametre?->contact3]) as $tel)
                            <li><i class="fas fa-phone"></i><a href="tel:{{ preg_replace('/[^\d+]/', '', $tel) }}">{{ $tel }}</a></li>
                        @endforeach
                        @foreach (array_filter([$parametre?->email1, $parametre?->email2]) as $mail)
                            <li><i class="fas fa-envelope"></i><a href="mailto:{{ $mail }}">{{ $mail }}</a></li>
                        @endforeach
                        @if ($parametre?->google_maps)
                            <li><i class="fas fa-map"></i><a href="{{ $parametre->google_maps }}" target="_blank" rel="noopener">Voir sur Google Maps</a></li>
                        @endif
                        <li><i class="fas fa-clock"></i><span>Assistance 24h/24, 7j/7</span></li>
                    </ul>
                </div>
            </div>

            <hr class="footer-divider my-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-center">
                    <span class="small me-1">Paiements acceptés :</span>
                    <span class="payment-pill">
                        <img src="https://lexxprint.com/wp-content/uploads/2022/03/wave.png" alt="" width="20" height="20" loading="lazy">Wave
                    </span>
                    <span class="payment-pill"><i class="fas fa-money-bill-wave"></i>Espèces</span>
                </div>
                <p class="small mb-0 text-center text-md-end">
                    &copy; {{ date('Y') }} Sages Home. Tous droits réservés · Réalisé par
                    <a href="https://www.ticafrique.ci/" target="_blank" rel="noopener" class="text-decoration-underline">Ticafrique</a>
                </p>
            </div>
        </div>
    </footer>

    {{-- Boutons flottants --}}
    <div class="floating-buttons">
        <button type="button" class="floating-btn back-to-top" id="backToTop" aria-label="Retour en haut de page">
            <i class="fas fa-chevron-up"></i>
        </button>
        @if ($waNumber)
            <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Bonjour, je suis intéressé(e) par vos résidences Sages Home.') }}"
                target="_blank" rel="noopener" class="floating-btn whatsapp-btn" aria-label="Nous écrire sur WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
        @endif
    </div>

    {{-- Scripts --}}
    <script>window.SAGES_VILLES_COMMUNES = @json(config('ville-commune'));</script>
    <script src="{{ asset('vendor/bootstrap-5.3.3/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/front.js') }}?v={{ filemtime(public_path('js/front.js')) }}"></script>

    @stack('scripts')
</body>

</html>
