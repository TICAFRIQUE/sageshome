@extends('layouts.app')

@section('title', 'Nos Résidences - Collection Exclusive | Sages Home')

@section('meta_description', 'Découvrez notre collection exclusive de résidences de luxe. Villas, appartements premium et maisons de charme à Abidjan avec services haut de gamme.')

@section('content')
<div class="container py-5">
    <!-- En-tête de page -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">
            Nos <span style="color: var(--sage-gold-end);">Résidences</span>
        </h1>
        <p class="lead text-muted mx-auto" style="max-width: 600px;">
            Découvrez notre collection soigneusement sélectionnée de résidences exceptionnelles, 
            chacune offrant une expérience unique de confort et de raffinement.
        </p>
    </div>

    <!-- Filtres de recherche -->
    <div class="card mb-5 border-0 shadow-sm">
        <div class="card-body p-4" style="background: linear-gradient(135deg, #F8F8F8, #FFFFFF);">
            <form class="row g-3 align-items-end" method="GET">
                <div class="col-md-3">
                    <label for="location" class="form-label fw-medium">
                        <i class="fas fa-map-marker-alt me-1" style="color: var(--sage-gold-end);"></i>
                        Localisation
                    </label>
                    <select class="form-select" id="location" name="location">
                        <option value="">Toutes les zones</option>
                        <option value="cocody" {{ request('location') == 'cocody' ? 'selected' : '' }}>Cocody</option>
                        <option value="plateau" {{ request('location') == 'plateau' ? 'selected' : '' }}>Plateau</option>
                        <option value="zone4" {{ request('location') == 'zone4' ? 'selected' : '' }}>Zone 4</option>
                        <option value="marcory" {{ request('location') == 'marcory' ? 'selected' : '' }}>Marcory</option>
                        <option value="treichville" {{ request('location') == 'treichville' ? 'selected' : '' }}>Treichville</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="bedrooms" class="form-label fw-medium">
                        <i class="fas fa-bed me-1" style="color: var(--sage-gold-end);"></i>
                        Chambres
                    </label>
                    <select class="form-select" id="bedrooms" name="bedrooms">
                        <option value="">Toutes</option>
                        <option value="1" {{ request('bedrooms') == '1' ? 'selected' : '' }}>1 chambre</option>
                        <option value="2" {{ request('bedrooms') == '2' ? 'selected' : '' }}>2 chambres</option>
                        <option value="3" {{ request('bedrooms') == '3' ? 'selected' : '' }}>3 chambres</option>
                        <option value="4" {{ request('bedrooms') == '4' ? 'selected' : '' }}>4+ chambres</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="capacity" class="form-label fw-medium">
                        <i class="fas fa-users me-1" style="color: var(--sage-gold-end);"></i>
                        Capacité
                    </label>
                    <select class="form-select" id="capacity" name="capacity">
                        <option value="">Toutes</option>
                        <option value="2" {{ request('capacity') == '2' ? 'selected' : '' }}>2 personnes</option>
                        <option value="4" {{ request('capacity') == '4' ? 'selected' : '' }}>4 personnes</option>
                        <option value="6" {{ request('capacity') == '6' ? 'selected' : '' }}>6 personnes</option>
                        <option value="8" {{ request('capacity') == '8' ? 'selected' : '' }}>8+ personnes</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="price_range" class="form-label fw-medium">
                        <i class="fas fa-money-bill-wave me-1" style="color: var(--sage-gold-end);"></i>
                        Budget (FCFA/nuit)
                    </label>
                    <select class="form-select" id="price_range" name="price_range">
                        <option value="">Tous budgets</option>
                        <option value="0-50000" {{ request('price_range') == '0-50000' ? 'selected' : '' }}>Moins de 50 000</option>
                        <option value="50000-100000" {{ request('price_range') == '50000-100000' ? 'selected' : '' }}>50 000 - 100 000</option>
                        <option value="100000-150000" {{ request('price_range') == '100000-150000' ? 'selected' : '' }}>100 000 - 150 000</option>
                        <option value="150000-plus" {{ request('price_range') == '150000-plus' ? 'selected' : '' }}>Plus de 150 000</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>
                        Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats de recherche -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">
                @if(isset($residences) && is_countable($residences))
                    <strong>{{ count($residences) }}</strong> résidences trouvées
                @else
                    <strong>9</strong> résidences trouvées
                @endif
            </p>
        </div>
        
        <div class="d-flex gap-2 align-items-center">
            <label for="sort" class="text-muted small me-2">Trier par :</label>
            <select class="form-select form-select-sm" id="sort" name="sort" style="width: auto;">
                <option value="recommended">Recommandées</option>
                <option value="price_asc">Prix croissant</option>
                <option value="price_desc">Prix décroissant</option>
                <option value="rating">Mieux notées</option>
                <option value="newest">Plus récentes</option>
            </select>
        </div>
    </div>

    <!-- Grille des résidences -->
    <div class="row g-4">
        @if(isset($residences) && count($residences) > 0)
            @foreach($residences as $residence)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm residence-card">
                        <div class="position-relative">
                            @if(isset($residence->primaryImage) && $residence->primaryImage)
                                <img src="{{ Storage::url($residence->primaryImage->image_path) }}" 
                                     class="card-img-top" alt="{{ $residence->name }}"
                                     style="height: 220px; object-fit: cover;">
                            @else
                                <div class="bg-gradient" style="height: 220px; background: linear-gradient(135deg, var(--sage-gold-start), var(--sage-gold-end));">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-home text-white fa-4x"></i>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($residence->is_featured) && $residence->is_featured)
                            <div class="position-absolute top-0 end-0 p-3">
                                <span class="badge" style="background: var(--sage-green-dark); color: white;">
                                    <i class="fas fa-star me-1"></i>Vedette
                                </span>
                            </div>
                            @endif
                            
                            <div class="position-absolute bottom-0 start-0 p-3">
                                @if(isset($residence->is_available) && $residence->is_available)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-warning text-dark">Complet</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $residence->name ?? 'Résidence Premium' }}</h5>
                                <div class="text-end">
                                    <span class="h6 mb-0" style="color: var(--sage-green-dark);">{{ number_format($residence->price_per_night ?? 75000, 0, ',', ' ') }} FCFA</span>
                                    <br><small class="text-muted">par nuit</small>
                                </div>
                            </div>
                            
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1" style="color: var(--sage-gold-end);"></i>
                                {{ $residence->location ?? $residence->address ?? 'Abidjan' }}
                            </p>
                            
                            <p class="card-text small text-muted mb-3">
                                {{ Str::limit($residence->description ?? 'Résidence d\'exception avec tous les conforts modernes.', 100) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex gap-3">
                                    @if(isset($residence->bedrooms) && $residence->bedrooms)
                                        <small class="text-muted">
                                            <i class="fas fa-bed" style="color: var(--sage-gold-end);"></i>
                                            {{ $residence->bedrooms }} ch.
                                        </small>
                                    @endif
                                    @if(isset($residence->capacity) && $residence->capacity)
                                        <small class="text-muted">
                                            <i class="fas fa-users" style="color: var(--sage-gold-end);"></i>
                                            {{ $residence->capacity }} pers.
                                        </small>
                                    @endif
                                </div>
                                <div class="d-flex">
                                    @php $rating = $residence->rating ?? rand(3, 5); @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star small" style="color: {{ $i <= $rating ? 'var(--sage-gold-end)' : '#E0E0E0' }};"></i>
                                    @endfor
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('residences.show', $residence->slug ?? 'residence-' . ($residence->id ?? rand(1,10))) }}" 
                                   class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="fas fa-eye me-1"></i>Voir détails
                                </a>
                                {{-- @if(isset($residence->is_available) && $residence->is_available)
                                    <a href="{{ route('booking.create', $residence->id ?? 1) }}" 
                                       class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-calendar-check me-1"></i>Réserver
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-sm flex-grow-1" disabled>
                                        <i class="fas fa-times me-1"></i>Complet
                                    </button>
                                @endif --}}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Résidences par défaut si pas de données -->
            @php
                $defaultResidences = [
                    ['name' => 'Villa d\'Exception', 'slug' => 'villa-exception', 'price' => 125000, 'location' => 'Cocody', 'description' => 'Villa luxueuse avec piscine privée et vue panoramique sur la lagune. Parfaite pour des séjours d\'exception.', 'bedrooms' => 4, 'capacity' => 8, 'featured' => true, 'available' => true, 'gradient' => 'var(--sage-gold-start), var(--sage-gold-end)', 'icon' => 'fas fa-home'],
                    ['name' => 'Appartement Premium', 'slug' => 'appartement-premium', 'price' => 85000, 'location' => 'Plateau', 'description' => 'Appartement moderne au cœur du quartier des affaires avec tous les équipements haut de gamme.', 'bedrooms' => 2, 'capacity' => 4, 'featured' => false, 'available' => true, 'gradient' => 'var(--sage-green-secondary), var(--sage-green-dark)', 'icon' => 'fas fa-building'],
                    ['name' => 'Maison de Charme', 'slug' => 'maison-charme', 'price' => 65000, 'location' => 'Zone 4', 'description' => 'Maison traditionnelle rénovée avec jardin tropical et terrasse privée pour un séjour authentique.', 'bedrooms' => 3, 'capacity' => 6, 'featured' => false, 'available' => true, 'gradient' => '#2F4A33, #4A6B42', 'icon' => 'fas fa-tree'],
                    ['name' => 'Studio Design', 'slug' => 'studio-design', 'price' => 45000, 'location' => 'Marcory', 'description' => 'Studio moderne et élégant avec tout le nécessaire pour un court séjour confortable.', 'bedrooms' => 1, 'capacity' => 2, 'featured' => false, 'available' => true, 'gradient' => '#888888, #1A1A1A', 'icon' => 'fas fa-bed'],
                    ['name' => 'Villa Familiale', 'slug' => 'villa-familiale', 'price' => 95000, 'location' => 'Cocody', 'description' => 'Spacieuse villa parfaite pour les familles avec jardin et espace de jeux pour enfants.', 'bedrooms' => 4, 'capacity' => 10, 'featured' => false, 'available' => false, 'gradient' => 'var(--sage-gold-start), var(--sage-green-secondary)', 'icon' => 'fas fa-home'],
                    ['name' => 'Duplex Moderne', 'slug' => 'duplex-moderne', 'price' => 110000, 'location' => 'Plateau', 'description' => 'Duplex contemporain avec terrasse panoramique et équipements haut de gamme.', 'bedrooms' => 3, 'capacity' => 6, 'featured' => false, 'available' => true, 'gradient' => 'var(--sage-green-dark), var(--sage-gold-end)', 'icon' => 'fas fa-building'],
                    ['name' => 'Résidence Sécurisée', 'slug' => 'residence-securisee', 'price' => 75000, 'location' => 'Zone 4', 'description' => 'Résidence dans complexe sécurisé avec piscine commune et service de conciergerie.', 'bedrooms' => 2, 'capacity' => 4, 'featured' => false, 'available' => true, 'gradient' => '#4A6B42, #2F4A33', 'icon' => 'fas fa-shield-alt'],
                    ['name' => 'Penthouse Luxe', 'slug' => 'penthouse-luxe', 'price' => 200000, 'location' => 'Plateau', 'description' => 'Penthouse d\'exception avec terrasse privée et vue imprenable sur la ville.', 'bedrooms' => 4, 'capacity' => 8, 'featured' => true, 'available' => true, 'gradient' => 'var(--sage-gold-start), var(--sage-gold-end)', 'icon' => 'fas fa-crown'],
                    ['name' => 'Loft Artistique', 'slug' => 'loft-artistique', 'price' => 80000, 'location' => 'Treichville', 'description' => 'Loft unique avec décoration artistique et espace de travail créatif.', 'bedrooms' => 2, 'capacity' => 4, 'featured' => false, 'available' => true, 'gradient' => '#888888, var(--sage-green-secondary)', 'icon' => 'fas fa-palette']
                ];
            @endphp

            @foreach($defaultResidences as $index => $residence)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm residence-card">
                        <div class="position-relative">
                            <div class="bg-gradient" style="height: 220px; background: linear-gradient(135deg, {{ $residence['gradient'] }});">
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <i class="{{ $residence['icon'] }} text-white fa-4x"></i>
                                </div>
                            </div>
                            
                            @if($residence['featured'])
                            <div class="position-absolute top-0 end-0 p-3">
                                <span class="badge" style="background: var(--sage-green-dark); color: white;">
                                    <i class="fas fa-star me-1"></i>Vedette
                                </span>
                            </div>
                            @endif
                            
                            <div class="position-absolute bottom-0 start-0 p-3">
                                @if($residence['available'])
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-warning text-dark">Complet</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $residence['name'] }}</h5>
                                <div class="text-end">
                                    <span class="h6 mb-0" style="color: var(--sage-green-dark);">{{ number_format($residence['price'], 0, ',', ' ') }} FCFA</span>
                                    <br><small class="text-muted">par nuit</small>
                                </div>
                            </div>
                            
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1" style="color: var(--sage-gold-end);"></i>
                                {{ $residence['location'] }}, Abidjan
                            </p>
                            
                            <p class="card-text small text-muted mb-3">
                                {{ $residence['description'] }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex gap-3">
                                    <small class="text-muted">
                                        <i class="fas fa-bed" style="color: var(--sage-gold-end);"></i>
                                        {{ $residence['bedrooms'] }} ch.
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-users" style="color: var(--sage-gold-end);"></i>
                                        {{ $residence['capacity'] }} pers.
                                    </small>
                                </div>
                                <div class="d-flex">
                                    @php $rating = rand(3, 5); @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star small" style="color: {{ $i <= $rating ? 'var(--sage-gold-end)' : '#E0E0E0' }};"></i>
                                    @endfor
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('residences.show', $residence['slug']) }}" 
                                   class="btn btn-outline-secondary btn-sm flex-grow-1">
                                    <i class="fas fa-eye me-1"></i>Voir détails
                                </a>
                                @if($residence['available'])
                                    <a href="{{ route('booking.create', $index + 1) }}" 
                                       class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-calendar-check me-1"></i>Réserver
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-sm flex-grow-1" disabled>
                                        <i class="fas fa-times me-1"></i>Complet
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Pagination -->
    @if(isset($residences) && method_exists($residences, 'links'))
        <div class="mt-5">
            {{ $residences->appends(request()->query())->links() }}
        </div>
    @else
        <nav class="mt-5" aria-label="Navigation des résidences">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <span class="page-link">Précédent</span>
                </li>
                <li class="page-item active">
                    <span class="page-link">1</span>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">3</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">Suivant</a>
                </li>
            </ul>
        </nav>
    @endif

    <!-- Call-to-Action -->
    <div class="text-center mt-5 p-5 rounded" style="background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary));">
        <div class="text-white">
            <h3 class="fw-bold mb-3">Vous ne trouvez pas ce que vous cherchez ?</h3>
            <p class="lead mb-4">
                Contactez-nous pour une recherche personnalisée selon vos critères spécifiques.
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="tel:+22527201234" class="btn btn-primary btn-lg">
                    <i class="fas fa-phone me-2"></i>Appelez-nous
                </a>
                <a href="mailto:contact@sageshome.com" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-envelope me-2"></i>Contactez-nous
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.residence-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.residence-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.residence-card .card-img-top {
    transition: transform 0.3s ease;
}

.residence-card:hover .card-img-top {
    transform: scale(1.02);
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.form-select:focus,
.form-control:focus {
    border-color: var(--sage-gold-end);
    box-shadow: 0 0 0 0.2rem rgba(242, 209, 138, 0.25);
}

.pagination .page-link {
    color: var(--sage-green-dark);
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: var(--sage-gold-end);
    border-color: var(--sage-gold-end);
    color: var(--sage-green-dark);
}

.pagination .page-link:hover {
    color: var(--sage-green-dark);
    background-color: var(--sage-gold-start);
    border-color: var(--sage-gold-start);
}

.card-img-top {
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes au scroll
    const cards = document.querySelectorAll('.residence-card');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // Gestion du formulaire de recherche
    const searchForm = document.querySelector('form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            // Laisser le formulaire se soumettre normalement
            console.log('Recherche soumise');
        });
    }

    // Tri des résidences
    const sortSelect = document.getElementById('sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            console.log('Tri par :', this.value);
            // Dans un vrai projet, recharger les résultats triés
            // Pour l'instant, on peut ajouter le paramètre à l'URL
            const url = new URL(window.location);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
        
        // Définir la valeur sélectionnée selon l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const sortValue = urlParams.get('sort');
        if (sortValue) {
            sortSelect.value = sortValue;
        }
    }
});
</script>
@endpush