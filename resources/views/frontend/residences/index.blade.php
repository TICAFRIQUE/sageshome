@extends('layouts.app')

@section('title', 'Nos Résidences - Collection Exclusive | Sages Home')

@section('meta_description', 'Découvrez notre collection exclusive de résidences de luxe. Villas, appartements premium et maisons de charme à Abidjan avec services haut de gamme.')

@section('content')
<div class="container py-5">
    <!-- En-tête de page -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3 pt-4">
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
                <div class="col-md-2">
                    <label for="ville" class="form-label fw-medium">
                        <i class="fas fa-map-marker-alt me-1" style="color: var(--sage-gold-end);"></i>
                        Ville
                    </label>
                    <select class="form-select" id="ville" name="ville">
                        <option value="">Toutes les villes</option>
                        @foreach($availableVilles as $ville)
                            <option value="{{ $ville }}" {{ request('ville') == $ville ? 'selected' : '' }}>
                                {{ $ville }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="commune" class="form-label fw-medium">
                        <i class="fas fa-map me-1" style="color: var(--sage-gold-end);"></i>
                        Commune
                    </label>
                    <select class="form-select" id="commune" name="commune">
                        <option value="">Toutes les communes</option>
                        @foreach($availableCommunes as $commune)
                            <option value="{{ $commune }}" {{ request('commune') == $commune ? 'selected' : '' }}>
                                {{ $commune }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="type" class="form-label fw-medium">
                        <i class="fas fa-home me-1" style="color: var(--sage-gold-end);"></i>
                        Type
                    </label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tous les types</option>
                        @foreach($residenceTypes as $residenceType)
                            <option value="{{ $residenceType->id }}" {{ request('type') == $residenceType->id ? 'selected' : '' }}>
                                {{ $residenceType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="capacity" class="form-label fw-medium">
                        <i class="fas fa-users me-1" style="color: var(--sage-gold-end);"></i>
                        Capacité
                    </label>
                    <select class="form-select" id="capacity" name="capacity">
                        <option value="">Toutes</option>
                        <option value="1" {{ request('capacity') == '1' ? 'selected' : '' }}>1 personne</option>
                        <option value="2" {{ request('capacity') == '2' ? 'selected' : '' }}>2 personnes</option>
                        <option value="4" {{ request('capacity') == '4' ? 'selected' : '' }}>4 personnes</option>
                        <option value="6" {{ request('capacity') == '6' ? 'selected' : '' }}>6 personnes</option>
                        <option value="8" {{ request('capacity') == '8' ? 'selected' : '' }}>8+ personnes</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="min_price" class="form-label fw-medium">
                        <i class="fas fa-money-bill-wave me-1" style="color: var(--sage-gold-end);"></i>
                        Prix min
                    </label>
                    <input type="number" class="form-control" id="min_price" name="min_price" 
                           value="{{ request('min_price') }}" placeholder="0" min="0">
                </div>
                
                <div class="col-md-1">
                    <label for="max_price" class="form-label fw-medium">Prix max</label>
                    <input type="number" class="form-control" id="max_price" name="max_price" 
                           value="{{ request('max_price') }}" placeholder="∞" min="0">
                </div>
                
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('residences.index') }}" class="btn btn-outline-secondary w-100 mt-1" title="Réinitialiser">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats de recherche -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">
                @if($residences->count() > 0)
                    <strong>{{ $residences->count() }}</strong> résidences trouvées
                    @if(request()->hasAny(['ville', 'commune', 'type', 'capacity', 'min_price', 'max_price']))
                        <span class="text-primary">selon vos critères</span>
                    @endif
                @else
                    @if(request()->hasAny(['ville', 'commune', 'type', 'capacity', 'min_price', 'max_price']))
                        <span class="text-warning">Aucune résidence ne correspond à vos critères</span>
                    @else
                        <strong>Aucune résidence</strong> disponible actuellement
                    @endif
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
        @if($residences->count() > 0)
            @foreach($residences as $residence)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm residence-card">
                        <div class="position-relative">
                            @if($residence->primaryImage)
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
                            
                            @if($residence->is_featured)
                            <div class="position-absolute top-0 end-0 p-3">
                                <span class="badge" style="background: var(--sage-green-dark); color: white;">
                                    <i class="fas fa-star me-1"></i>Vedette
                                </span>
                            </div>
                            @endif
                            
                            <div class="position-absolute bottom-0 start-0 p-3">
                                @if($residence->is_available)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-warning text-dark">Complet</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $residence->name }}</h5>
                                <div class="text-end">
                                    <span class="h6 mb-0" style="color: var(--sage-green-dark);">{{ number_format($residence->price_per_night, 0, ',', ' ') }} FCFA</span>
                                    <br><small class="text-muted">par nuit</small>
                                </div>
                            </div>
                            
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1" style="color: var(--sage-gold-end);"></i>
                                @if($residence->commune)
                                    {{ $residence->commune }}, {{ $residence->ville }}
                                @else
                                    {{ $residence->ville }}
                                @endif
                            </p>
                            
                            <p class="card-text small text-muted mb-3">
                                {{ Str::limit($residence->description, 100) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex gap-3">
                                    @if($residence->capacity)
                                        <small class="text-muted">
                                            <i class="fas fa-users" style="color: var(--sage-gold-end);"></i>
                                            {{ $residence->capacity }} pers.
                                        </small>
                                    @endif
                                </div>
                                <div class="d-flex">
                                    @php $rating = rand(4, 5); @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star small" style="color: {{ $i <= $rating ? 'var(--sage-gold-end)' : '#E0E0E0' }};"></i>
                                    @endfor
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('residences.show', $residence->slug) }}" 
                                   class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="fas fa-eye me-1"></i>Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Message quand aucune résidence n'est trouvée -->
            <div class="col-12">
                <div class="card border-0 bg-light text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-search fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">
                            @if(request()->hasAny(['ville', 'commune', 'type', 'capacity', 'min_price', 'max_price']))
                                Aucune résidence ne correspond à vos critères
                            @else
                                Aucune résidence disponible
                            @endif
                        </h4>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['ville', 'commune', 'type', 'capacity', 'min_price', 'max_price']))
                                Essayez de modifier vos filtres de recherche pour élargir les résultats.
                            @else
                                Nos résidences seront bientôt disponibles. Revenez plus tard !
                            @endif
                        </p>
                        
                        @if(request()->hasAny(['ville', 'commune', 'type', 'capacity', 'min_price', 'max_price']))
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="{{ route('residences.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-undo me-1"></i>Réinitialiser les filtres
                            </a>
                            <a href="/#contact" class="btn btn-primary">
                                <i class="fas fa-envelope me-1"></i>Nous contacter
                            </a>
                        </div>
                        @else
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-home me-1"></i>Retour à l'accueil
                            </a>
                            <a href="{{ route('home') }}#contact" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-1"></i>Nous contacter
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
    <!-- Pagination -->
    @if($residences->hasPages())
        <div class="mt-5">
            {{ $residences->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Call-to-Action -->
    <div class="text-center mt-5 p-5 rounded" style="background: linear-gradient(135deg, var(--sage-green-dark), var(--sage-green-secondary));">
        <div class="text-white">
            <h3 class="fw-bold mb-3 text-white">Vous ne trouvez pas ce que vous cherchez ?</h3>
            <p class="lead mb-4">
                Contactez-nous pour une recherche personnalisée selon vos critères spécifiques.
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="tel:+225{{$parametre?->contact2}}" class="btn btn-primary btn-lg">
                    <i class="fas fa-phone me-2"></i>Appelez-nous
                </a>
                <a href="mailto:{{$parametre?->email1}}" class="btn btn-outline-light btn-lg">
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

    // Gestion des filtres ville-commune
    const villeSelect = document.getElementById('ville');
    const communeSelect = document.getElementById('commune');
    const allVillesCommunes = @json($allVillesCommunes ?? []);
    
    if (villeSelect && communeSelect) {
        villeSelect.addEventListener('change', function() {
            const selectedVille = this.value;
            
            // Réinitialiser les communes
            communeSelect.innerHTML = '<option value="">Toutes les communes</option>';
            
            if (selectedVille && allVillesCommunes[selectedVille]) {
                // Charger les communes de la ville sélectionnée
                const communes = allVillesCommunes[selectedVille];
                communes.forEach(commune => {
                    const option = document.createElement('option');
                    option.value = commune;
                    option.textContent = commune;
                    if ('{{ request("commune") }}' === commune) {
                        option.selected = true;
                    }
                    communeSelect.appendChild(option);
                });
            }
        });
        
        // Initialiser les communes si une ville est déjà sélectionnée
        if (villeSelect.value) {
            const event = new Event('change');
            villeSelect.dispatchEvent(event);
        }
    }

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