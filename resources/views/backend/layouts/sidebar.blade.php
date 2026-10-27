@php
    // Compteur de réservations à traiter (badge du menu)
    $pendingBookingsCount = \App\Models\Booking::where('status', 'pending')->count();
    $canSettings = Auth::user()->role == 'superadmin' || Auth::user()->role == 'developpeur' || Auth::user()->can('voir-parametre');
    $settingsActive = Route::is('role.*', 'parametre.*', 'module.*', 'permission.*', 'admin-register.*');
    $bookingsActive = Route::is('admin.bookings.*');
@endphp

<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('dashboard.index') }}" class="sh-brand">
            <img src="{{ asset('images/logo-mark.webp') }}" alt="Sages Home" width="34" height="34">
            <span class="sh-brand-text">
                <span class="sh-brand-name">SAGES HOME</span>
                <span class="sh-brand-sub">Administration</span>
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover" aria-label="Réduire le menu">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>

            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>Pilotage</span></li>

                @can('voir-tableau de bord')
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Route::is('dashboard.*', 'admin.sages-home.dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard.index') }}">
                            <i class="ri-dashboard-2-line"></i> <span>Tableau de bord</span>
                        </a>
                    </li>
                @endcan

                <li class="nav-item">
                    <a class="nav-link menu-link {{ Route::is('admin.bookings.report') ? 'active' : '' }}"
                        href="{{ route('admin.bookings.report') }}">
                        <i class="ri-file-chart-line"></i> <span>Compte d'exploitation</span>
                    </a>
                </li>

                <li class="menu-title"><span>Réservations</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ Route::is('admin.bookings.index', 'admin.bookings.show') ? 'active' : '' }}"
                        href="{{ route('admin.bookings.index') }}">
                        <i class="ri-calendar-check-line"></i> <span>Réservations</span>
                        @if ($pendingBookingsCount > 0)
                            <span class="menu-badge" title="{{ $pendingBookingsCount }} en attente">{{ $pendingBookingsCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Route::is('admin.bookings.calendar') ? 'active' : '' }}"
                        href="{{ route('admin.bookings.calendar') }}">
                        <i class="ri-calendar-2-line"></i> <span>Calendrier</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Route::is('admin.bookings.create') ? 'active' : '' }}"
                        href="{{ route('admin.bookings.create') }}">
                        <i class="ri-add-circle-line"></i> <span>Nouvelle réservation</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Route::is('admin.clients.*') ? 'active' : '' }}"
                        href="{{ route('admin.clients.index') }}">
                        <i class="ri-group-line"></i> <span>Clients</span>
                    </a>
                </li>

                <li class="menu-title"><span>Catalogue</span></li>

                <li class="nav-item">
                    <a href="{{ route('admin.residences.index') }}"
                        class="nav-link menu-link {{ Route::is('admin.residences.*') ? 'active' : '' }}">
                        <i class="ri-building-line"></i> <span>Résidences</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.residence-types.index') }}"
                        class="nav-link menu-link {{ Route::is('admin.residence-types.*') ? 'active' : '' }}">
                        <i class="ri-home-4-line"></i> <span>Types de résidences</span>
                    </a>
                </li>

                @if ($canSettings)
                    <li class="menu-title"><span>Administration</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ $settingsActive ? 'active' : '' }}" href="#sidebarAuth"
                            data-bs-toggle="collapse" role="button" aria-controls="sidebarAuth"
                            aria-expanded="{{ $settingsActive ? 'true' : 'false' }}">
                            <i class="ri-settings-3-line"></i> <span>Paramètres</span>
                        </a>
                        <div class="collapse menu-dropdown {{ $settingsActive ? 'show' : '' }}" id="sidebarAuth">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('parametre.index') }}"
                                        class="nav-link {{ Route::is('parametre.*') ? 'active' : '' }}">Informations du site</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin-register.index') }}"
                                        class="nav-link {{ Route::is('admin-register.*') ? 'active' : '' }}">Utilisateurs</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('module.index') }}"
                                        class="nav-link {{ Route::is('module.*') ? 'active' : '' }}">Modules</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('role.index') }}"
                                        class="nav-link {{ Route::is('role.*') ? 'active' : '' }}">Rôles</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('permission.index') }}"
                                        class="nav-link {{ Route::is('permission.*') ? 'active' : '' }}">Permissions</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <li class="nav-item mt-3">
                    <a class="nav-link menu-link" href="{{ route('home') }}" target="_blank" rel="noopener">
                        <i class="ri-external-link-line"></i> <span>Voir le site</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
