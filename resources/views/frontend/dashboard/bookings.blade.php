@extends('layouts.app')

@section('title', 'Mes Réservations - Sages Home')
@section('page-title', 'Mes Réservations')
@section('page-subtitle', 'Gérez toutes vos réservations en un coup d\'œil')

@section('page-actions')
    <div class="d-flex flex-wrap gap-3 align-items-center py-5">
        <!-- Statistiques rapides -->
        <div class="d-flex gap-3">
            <div class="quick-stat">
                <div class="quick-stat-value text-success">{{ $statusCounts['confirmed'] }}</div>
                <div class="quick-stat-label">Confirmées</div>
            </div>
            <div class="quick-stat">
                <div class="quick-stat-value text-warning">{{ $statusCounts['pending'] }}</div>
                <div class="quick-stat-label">En attente</div>
            </div>
            <div class="quick-stat">
                <div class="quick-stat-value text-info">{{ $statusCounts['completed'] }}</div>
                <div class="quick-stat-label">Terminées</div>
            </div>
        </div>

        <div class="ms-auto">
            <a href="{{ route('residences.index') }}" class="btn btn-sage-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle réservation
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div style="padding:70px;">
        <!-- Filtres améliorés -->
        <div class="dashboard-card mb-4 ">
            {{-- <div class="card-header bg-light border-0">
                <h6 class="card-title mb-0">
                    <i class="fas fa-filter me-2 text-primary"></i>
                    Filtrer et trier vos réservations
                </h6>
            </div> --}}
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard.bookings') }}" class="row g-3" id="filterForm">
                    <div class="col-md-3">
                        <label for="status" class="form-label">
                            <i class="fas fa-tag me-1 text-muted"></i>
                            Statut
                        </label>
                        <select name="status" id="status" class="form-select form-select-modern">
                            <option value="all"
                                {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>
                                <i class="fas fa-list"></i> Tous ({{ $statusCounts['all'] }})
                            </option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                🕒 En attente ({{ $statusCounts['pending'] }})
                            </option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>
                                ✅ Confirmées ({{ $statusCounts['confirmed'] }})
                            </option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                ✨ Terminées ({{ $statusCounts['completed'] }})
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                ❌ Annulées ({{ $statusCounts['cancelled'] }})
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="sort" class="form-label">
                            <i class="fas fa-sort me-1 text-muted"></i>
                            Trier par
                        </label>
                        <select name="sort" id="sort" class="form-select form-select-modern">
                            <option value="created_at"
                                {{ request('sort') === 'created_at' || !request('sort') ? 'selected' : '' }}>
                                📅 Date de création
                            </option>
                            <option value="check_in" {{ request('sort') === 'check_in' ? 'selected' : '' }}>
                                🏨 Date d'arrivée
                            </option>
                            <option value="check_out" {{ request('sort') === 'check_out' ? 'selected' : '' }}>
                                🚪 Date de départ
                            </option>
                            <option value="total_price" {{ request('sort') === 'total_price' ? 'selected' : '' }}>
                                💰 Prix total
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="direction" class="form-label">
                            <i class="fas fa-sort-amount-down me-1 text-muted"></i>
                            Ordre
                        </label>
                        <select name="direction" id="direction" class="form-select form-select-modern">
                            <option value="desc"
                                {{ request('direction') === 'desc' || !request('direction') ? 'selected' : '' }}>
                                📉 Décroissant
                            </option>
                            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>
                                📈 Croissant
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" id="filterBtn">
                            <i class="fas fa-search me-1"></i>
                            <span class="d-none d-md-inline">Filtrer</span>
                        </button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('dashboard.bookings') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-undo me-1"></i>
                            <span class="d-none d-md-inline">Reset</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if ($bookings->count() > 0)
            <!-- Liste des réservations optimisée -->
            <div class="row g-4 px-5">
                @foreach ($bookings as $booking)
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="booking-card dashboard-card h-100 position-relative">
                            <!-- Badge de statut en overlay -->
                            <div class="position-absolute top-0 start-0 m-3" style="z-index: 10;">
                                @switch($booking->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>En attente
                                        </span>
                                    @break

                                    @case('confirmed')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Confirmée
                                        </span>
                                    @break

                                    @case('cancelled')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Annulée
                                        </span>
                                    @break

                                    @case('completed')
                                        <span class="badge bg-info">
                                            <i class="fas fa-star me-1"></i>Terminée
                                        </span>
                                    @break

                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                @endswitch
                            </div>

                            <!-- Image simplifiée -->
                            @if ($booking->residence->primaryImage)
                                <div class="booking-image-container">
                                    <img src="{{ Storage::url($booking->residence->primaryImage->image_path) }}"
                                        class="booking-image" loading="lazy"
                                        alt="{{ $booking->residence->name }}">
                                </div>
                            @else
                                <div class="booking-image bg-light d-flex align-items-center justify-content-center"
                                    style="height: 180px;">
                                    <i class="fas fa-home text-muted" style="font-size: 2rem;"></i>
                                </div>
                            @endif

                            <div class="card-body p-3">
                                <h6 class="card-title text-dark mb-2">{{ $booking->residence->name }}</h6>
                                <p class="text-muted mb-3 small">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $booking->residence->residenceType->name }}
                                </p>

                                <!-- Informations essentielles -->
                                <div class="booking-details mb-3">
                                    <div class="detail-row">
                                        <div class="detail-icon">
                                            <i class="fas fa-calendar text-primary"></i>
                                        </div>
                                        <div class="detail-content">
                                            <span class="detail-label">Arrivée :</span>
                                            <span
                                                class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>

                                    <div class="detail-row">
                                        <div class="detail-icon">
                                            <i class="fas fa-calendar text-warning"></i>
                                        </div>
                                        <div class="detail-content">
                                            <span class="detail-label">Départ :</span>
                                            <span
                                                class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>

                                    <div class="detail-row">
                                        <div class="detail-icon">
                                            <i class="fas fa-users text-info"></i>
                                        </div>
                                        <div class="detail-content">
                                            <span class="detail-label">Voyageurs :</span>
                                            <span class="detail-value">{{ $booking->guests }}</span>
                                        </div>
                                    </div>

                                    <!--date de reservation-->
                                    <div class="detail-row">
                                        <div class="detail-icon">
                                            <i class="fas fa-calendar text-info"></i>
                                        </div>
                                        <div class="detail-content">
                                            <span class="detail-label">Date de reservation :</span>
                                            <span
                                                class="detail-value">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y à H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Prix simplifié -->
                                <div class="price-section mb-3">
                                    <div class="price-container">
                                        <span class="price-label">Total</span>
                                        <span class="price-value">{{ number_format($booking->total_price, 0, ',', ' ') }} <span class="currency">FCFA</span></span>
                                    </div>
                                </div>

                                <!-- Actions simplifiées -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('dashboard.booking.show', $booking) }}"
                                        class="btn btn-primary btn-modern btn-sm">
                                        <i class="fas fa-eye me-1"></i>Détails
                                    </a>

                                    @if ($booking->status === 'pending' || $booking->status === 'confirmed')
                                        @if (\Carbon\Carbon::parse($booking->check_in)->isFuture())
                                            <form action="{{ route('dashboard.booking.cancel', $booking) }}"
                                                method="POST"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')"
                                                class="cancel-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="btn btn-outline-danger btn-modern btn-sm w-100">
                                                    <i class="fas fa-times me-1"></i>Annuler
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination améliorée -->
            <div class="pagination-container d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="pagination-info text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Affichage de {{ $bookings->firstItem() ?? 0 }} à {{ $bookings->lastItem() ?? 0 }}
                    sur {{ $bookings->total() }} résultat{{ $bookings->total() > 1 ? 's' : '' }}
                </div>

                <div class="pagination-links">
                    {{ $bookings->links() }}
                </div>
            </div>
        @else
            <div class="empty-state-card dashboard-card">
                <div class="card-body text-center py-5">
                    <div class="empty-state-animation mb-4">
                        @if (request('status') && request('status') !== 'all')
                            <i class="fas fa-search text-primary" style="font-size: 4rem;"></i>
                        @else
                            <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                        @endif
                    </div>

                    <h4 class="text-dark mb-3">
                        @if (request('status') && request('status') !== 'all')
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            Aucune réservation
                            {{ request('status') === 'pending' ? 'en attente' : (request('status') === 'confirmed' ? 'confirmée' : (request('status') === 'completed' ? 'terminée' : 'annulée')) }}
                            trouvée
                        @else
                            <i class="fas fa-info-circle text-info me-2"></i>
                            Commencez votre aventure !
                        @endif
                    </h4>

                    <p class="text-muted mb-4 lead">
                        @if (request('status') && request('status') !== 'all')
                            <i class="fas fa-filter me-1"></i>
                            Modifiez vos filtres ou découvrez nos résidences pour faire une nouvelle réservation.
                        @else
                            <i class="fas fa-heart me-1"></i>
                            Vous n'avez pas encore effectué de réservation.<br>
                            Découvrez nos magnifiques résidences et réservez dès maintenant !
                        @endif
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="{{ route('residences.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Découvrir nos résidences
                        </a>

                        @if (request('status') && request('status') !== 'all')
                            <a href="{{ route('dashboard.bookings') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-list me-2"></i>Voir toutes les réservations
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('styles')
    <style>
        /* Statistiques rapides */
        .quick-stat {
            text-align: center;
            padding: 0.75rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 12px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            min-width: 80px;
        }

        .quick-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .quick-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.25rem;
            display: block;
        }

        .quick-stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Formulaire de filtres moderne */
        .form-select-modern {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }

        .form-select-modern:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
            transform: translateY(-1px);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Cartes de réservation simplifiées */
        .booking-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.2s ease;
        }

        .booking-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .booking-image-container {
            overflow: hidden;
            border-radius: 8px 8px 0 0;
        }

        .booking-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        /* Suppression des overlays complexes */

        /* Détails de réservation simplifiés */
        .booking-details {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 0.75rem;
        }

        .detail-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            padding: 0.25rem 0;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-icon {
            width: 24px;
            text-align: center;
            margin-right: 0.5rem;
            font-size: 0.9rem;
        }

        .detail-content {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-label {
            font-weight: 500;
            color: #495057;
            font-size: 0.85rem;
        }

        .detail-value {
            color: #212529;
            font-weight: 500;
            font-size: 0.85rem;
        }

        /* Section prix simplifiée */
        .price-section {
            background: #0d6efd;
            border-radius: 6px;
            padding: 0.75rem;
            color: white;
            text-align: center;
        }

        .price-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .price-label {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .price-value {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .currency {
            font-size: 0.85rem;
        }

        /* Boutons simplifiés */
        .btn-modern {
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            border: none;
        }

        /* État vide amélioré */
        .empty-state-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 2px dashed #dee2e6;
            border-radius: 20px;
        }

        .empty-state-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* Pagination moderne */
        .pagination-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .pagination-info {
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Badges simplifiés */
        .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.6rem;
            border-radius: 4px;
            font-weight: 500;
        }

        /* Responsive design simplifié */
        @media (max-width: 768px) {
            .booking-card {
                margin-bottom: 1rem;
            }
            
            .quick-stat {
                margin-bottom: 0.5rem;
            }
        }

}
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form on filter change avec loading state
            const form = document.getElementById('filterForm');
            const selects = form.querySelectorAll('select');
            const filterBtn = document.getElementById('filterBtn');
            const originalBtnText = filterBtn.innerHTML;

            // Fonction pour afficher l'état de chargement
            function showLoading() {
                filterBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Chargement...';
                filterBtn.disabled = true;
            }

            // Auto-submit sur changement de filtre
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    showLoading();
                    form.submit();
                });
            });

            // Animation des cartes au scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const cardObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observer toutes les cartes
            document.querySelectorAll('.booking-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
                cardObserver.observe(card);
            });

            // Tooltip pour les badges de statut
            const statusBadges = document.querySelectorAll('.badge');
            statusBadges.forEach(badge => {
                badge.title = getStatusTooltip(badge.textContent.trim());
            });

            // Fonction pour obtenir le tooltip du statut
            function getStatusTooltip(status) {
                const tooltips = {
                    'En attente': 'Cette réservation est en attente de confirmation',
                    'Confirmée': 'Cette réservation a été confirmée',
                    'Annulée': 'Cette réservation a été annulée',
                    'Terminée': 'Ce séjour est terminé'
                };
                return tooltips[status] || status;
            }

            // Amélioration de la confirmation d'annulation
            document.querySelectorAll('.cancel-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const bookingName = this.closest('.booking-card').querySelector('.card-title')
                        .textContent;

                    Swal.fire({
                        title: 'Confirmer l\'annulation',
                        text: `Êtes-vous sûr de vouloir annuler votre réservation "${bookingName}" ?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-times me-1"></i> Oui, annuler',
                        cancelButtonText: '<i class="fas fa-arrow-left me-1"></i> Retour',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Afficher un loading sur le bouton
                            const submitBtn = this.querySelector('button[type="submit"]');
                            const originalText = submitBtn.innerHTML;
                            submitBtn.innerHTML =
                                '<i class="fas fa-spinner fa-spin me-1"></i> Annulation...';
                            submitBtn.disabled = true;

                            // Soumettre le formulaire
                            this.submit();
                        }
                    });
                });
            });

            // Recherche rapide (si on veut l'ajouter plus tard)
            const searchInput = document.getElementById('quickSearch');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const query = this.value.toLowerCase();
                        filterCards(query);
                    }, 300);
                });
            }

            function filterCards(query) {
                document.querySelectorAll('.booking-card').forEach(card => {
                    const title = card.querySelector('.card-title').textContent.toLowerCase();
                    const show = title.includes(query) || query === '';
                    card.closest('.col-lg-6').style.display = show ? 'block' : 'none';
                });
            }

            // Performance: Lazy loading pour les images
            if ('loading' in HTMLImageElement.prototype) {
                // Le navigateur supporte le lazy loading natif
                console.log('Lazy loading natif activé');
            } else {
                // Fallback pour les navigateurs plus anciens
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }

            // Statistiques rapides avec animation de compteur
            function animateCounters() {
                document.querySelectorAll('.quick-stat-value').forEach(counter => {
                    const target = parseInt(counter.textContent);
                    const increment = target / 30;
                    let current = 0;

                    const timer = setInterval(() => {
                        current += increment;
                        counter.textContent = Math.floor(current);

                        if (current >= target) {
                            counter.textContent = target;
                            clearInterval(timer);
                        }
                    }, 50);
                });
            }

            // Démarrer l'animation des compteurs après un délai
            setTimeout(animateCounters, 500);
        });

        // Fonction globale pour afficher les notifications de succès
        function showSuccessMessage(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Succès!',
                    text: message,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            }
        }
    </script>
@endpush
