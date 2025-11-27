@extends('backend.layouts.master')

@section('title', 'Modifier la Résidence')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Modifier {{ $residence->name }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.residences.index') }}">Résidences</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.residences.show', $residence) }}">{{ $residence->name }}</a></li>
                    <li class="breadcrumb-item active">Modifier</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<form action="{{ route('admin.residences.update', $residence) }}" method="POST" enctype="multipart/form-data" id="residenceForm">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Informations générales -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Informations générales</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label class="form-label" for="name">Nom de la résidence *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $residence->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label" for="residence_type_id">Type de résidence *</label>
                                <select class="form-select @error('residence_type_id') is-invalid @enderror" 
                                        id="residence_type_id" name="residence_type_id" required>
                                    <option value="">Choisir un type</option>
                                    @foreach($residenceTypes as $type)
                                        <option value="{{ $type->id }}" 
                                                {{ old('residence_type_id', $residence->residence_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('residence_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="capacity">Capacité (personnes) *</label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                       id="capacity" name="capacity" 
                                       value="{{ old('capacity', $residence->capacity) }}" 
                                       min="1" max="20" required>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="price_per_night">Prix par nuit (FCFA) *</label>
                                <input type="number" class="form-control @error('price_per_night') is-invalid @enderror" 
                                       id="price_per_night" name="price_per_night" 
                                       value="{{ old('price_per_night', $residence->price_per_night) }}" 
                                       min="0" required>
                                @error('price_per_night')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Description courte *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  maxlength="255" required>{{ old('description', $residence->description) }}</textarea>
                        <small class="form-text text-muted">Maximum 255 caractères</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="full_description">Description complète</label>
                        <textarea class="form-control @error('full_description') is-invalid @enderror" 
                                  id="full_description" name="full_description" 
                                  rows="6">{{ old('full_description', $residence->full_description) }}</textarea>
                        @error('full_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">Adresse *</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="2" 
                                  required>{{ old('address', $residence->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="latitude">Latitude</label>
                                <input type="number" class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" name="latitude" 
                                       value="{{ old('latitude', $residence->latitude) }}" 
                                       step="any" min="-90" max="90">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="longitude">Longitude</label>
                                <input type="number" class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" name="longitude" 
                                       value="{{ old('longitude', $residence->longitude) }}" 
                                       step="any" min="-180" max="180">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Équipements -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Équipements</h4>
                </div>
                <div class="card-body">
                    @php
                        $amenityCategories = [
                            'Connectivité' => [
                                'wifi' => 'Wi-Fi - Connexion disponible dans toute la propriété',
                                'canal_plus' => 'Abonnement Canal+ - Accès à toutes les chaînes'
                            ],
                            'Confort' => [
                                'climatiseur' => 'Climatiseur - Climatisation dans toutes les pièces',
                                'chauffe_eau' => 'Chauffe-eau - Système de chauffage de l\'eau'
                            ],
                            'Cuisine équipée' => [
                                'cuisiniere' => 'Cuisinière',
                                'four' => 'Four',
                                'micro_onde' => 'Micro-ondes',
                                'bouilloire' => 'Bouilloire électrique',
                                'refrigerateur' => 'Réfrigérateur',
                                'mixeur' => 'Mixeur',
                                'ustensiles' => 'Ustensiles de cuisine - Poêles, casseroles, etc.',
                                'couverts' => 'Couverts complets pour les repas'
                            ],
                            'Services' => [
                                'menage' => 'Service de ménage - Effectué tous les 3 jours',
                                'securite' => 'Sécurité 24H/24 et 7J/7 - Service de sécurité en continu'
                            ],
                            'Loisirs' => [
                                'piscine' => 'Piscine - Accessible pour les résidents'
                            ],
                            'Informations pratiques' => [
                                'arrivee_autonome' => 'Arrivée autonome - Accès avec boîte à clé sécurisée',
                                'annulation_gratuite' => 'Annulation gratuite - 2 semaines avant l\'arrivée',
                                'animaux_interdits' => 'Animaux de compagnie non autorisés'
                            ]
                        ];
                        $selectedAmenities = old('amenities', $residence->amenities ?: []);
                    @endphp
                    
                    @foreach($amenityCategories as $category => $amenities)
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">{{ $category }}</h6>
                        <div class="row">
                            @foreach($amenities as $key => $label)
                            <div class="col-lg-6 col-md-12">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" 
                                           name="amenities[]" value="{{ $key }}" 
                                           id="amenity_{{ $key }}"
                                           {{ in_array($key, $selectedAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="amenity_{{ $key }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @error('amenities')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Images actuelles -->
            @if($residence->images->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Images actuelles</h4>
                </div>
                <div class="card-body">
                    <div class="row" id="current-images">
                        @foreach($residence->images as $image)
                        <div class="col-md-3 mb-3" id="image-{{ $image->id }}">
                            <div class="card position-relative">
                                <img src="{{ Storage::url($image->image_path) }}" 
                                     class="card-img-top" 
                                     style="height: 120px; object-fit: cover;"
                                     alt="{{ $image->alt_text }}">
                                <div class="card-body p-2">
                                    @if($image->is_primary)
                                        <span class="badge bg-primary">Principal</span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary make-primary" 
                                                data-image-id="{{ $image->id }}">
                                            Définir principal
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1 delete-image" 
                                            data-image-id="{{ $image->id }}">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Nouvelles images -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Ajouter de nouvelles images</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="images">Ajouter de nouvelles images (JPG, JPEG, PNG, max 1MB)</label>
                        <input type="file" class="form-control @error('new_images.*') is-invalid @enderror" 
                               id="images" name="new_images[]" multiple accept="image/*">
                        <small class="form-text text-muted">Sélectionnez autant d'images que vous voulez (max 1MB chacune). Vous pourrez définir l'image principale et supprimer des images.</small>
                        @error('new_images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Aperçu des nouvelles images -->
                    <div id="image-preview" class="row"></div>
                    
                    <!-- Bouton pour ajouter plus d'images -->
                    <div class="mb-3 mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-more-images">
                            <i class="ri-add-line me-1"></i> Ajouter d'autres images
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('admin.residences.show', $residence) }}" class="btn btn-light">
                            <i class="ri-arrow-left-line me-1"></i> Retour
                        </a>
                        <a href="{{ route('residences.show', $residence->slug) }}" 
                           class="btn btn-outline-info" target="_blank">
                            <i class="ri-external-link-line me-1"></i> Voir sur le site
                        </a>
                    </div>
                </div>
            </div>

            <!-- Paramètres -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Paramètres</h4>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" 
                               id="is_available" name="is_available" value="1"
                               {{ old('is_available', $residence->is_available) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_available">
                            Disponible à la réservation
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" 
                               id="is_featured" name="is_featured" value="1"
                               {{ old('is_featured', $residence->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_featured">
                            Mettre en avant
                        </label>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Statistiques</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-primary mb-0">{{ $residence->bookings->count() }}</h5>
                            <small class="text-muted">Réservations</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-success mb-0">
                                {{ number_format($residence->bookings->where('status', 'confirmed')->sum('total_amount'), 0, ',', ' ') }}
                            </h5>
                            <small class="text-muted">FCFA générés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Champs cachés pour suppression d'images -->
<form id="delete-image-form" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedNewFiles = [];
    let primaryNewImageIndex = -1;
    
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');
    const addMoreBtn = document.getElementById('add-more-images');

    function validateFileSize(file) {
        const maxSize = 1024 * 1024; // 1MB
        return file.size <= maxSize;
    }

    function renderNewImagePreview() {
        imagePreview.innerHTML = '';
        
        selectedNewFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3';
                col.innerHTML = `
                    <div class="card position-relative">
                        <img src="${e.target.result}" 
                             class="card-img-top" 
                             style="height: 120px; object-fit: cover;"
                             alt="Aperçu">
                        <div class="card-body p-2">
                            <small class="text-muted d-block">${file.name}</small>
                            <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                            <div class="mt-2">
                                ${index === primaryNewImageIndex ? 
                                    '<span class="badge bg-primary me-1">Principal</span>' : 
                                    `<button type="button" class="btn btn-sm btn-outline-primary me-1 set-primary-new" data-index="${index}">Définir principal</button>`
                                }
                                <button type="button" class="btn btn-sm btn-outline-danger remove-new-image" data-index="${index}">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                imagePreview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
        
        updateFileInput();
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedNewFiles.forEach(file => dt.items.add(file));
        imageInput.files = dt.files;
    }

    imageInput.addEventListener('change', function(e) {
        const newFiles = Array.from(e.target.files);
        
        newFiles.forEach(file => {
            if (file.type.startsWith('image/')) {
                if (!validateFileSize(file)) {
                    alert(`Le fichier ${file.name} dépasse la taille limite de 1MB`);
                    return;
                }
                
                // Vérifier si le fichier n'existe pas déjà (par nom et taille)
                const existingFile = selectedNewFiles.find(f => 
                    f.name === file.name && f.size === file.size
                );
                
                if (!existingFile) {
                    selectedNewFiles.push(file);
                } else {
                    // Afficher un message plus visible à l'utilisateur
                    const toast = document.createElement('div');
                    toast.className = 'alert alert-warning alert-dismissible fade show position-fixed';
                    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; width: auto;';
                    toast.innerHTML = `
                        <strong>Fichier ignoré :</strong> ${file.name} est déjà présent.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(toast);
                    
                    // Supprimer automatiquement après 3 secondes
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 3000);
                }
            }
        });
        
        renderNewImagePreview();
        
        // Réinitialiser l'input pour permettre de sélectionner les mêmes fichiers plus tard
        e.target.value = '';
    });

    if (addMoreBtn) {
        addMoreBtn.addEventListener('click', function() {
            const tempInput = document.createElement('input');
            tempInput.type = 'file';
            tempInput.multiple = true;
            tempInput.accept = 'image/*';
            
            tempInput.addEventListener('change', function() {
                const newFiles = Array.from(this.files);
                
                newFiles.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        if (!validateFileSize(file)) {
                            alert(`Le fichier ${file.name} dépasse la taille limite de 1MB`);
                            return;
                        }
                        
                        // Vérifier si le fichier n'existe pas déjà (par nom et taille)
                        const existingFile = selectedNewFiles.find(f => 
                            f.name === file.name && f.size === file.size
                        );
                        
                        if (!existingFile) {
                            selectedNewFiles.push(file);
                        } else {
                            // Afficher un message plus visible à l'utilisateur
                            const toast = document.createElement('div');
                            toast.className = 'alert alert-warning alert-dismissible fade show position-fixed';
                            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; width: auto;';
                            toast.innerHTML = `
                                <strong>Fichier ignoré :</strong> ${file.name} est déjà présent.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.body.appendChild(toast);
                            
                            // Supprimer automatiquement après 3 secondes
                            setTimeout(() => {
                                if (toast.parentNode) {
                                    toast.parentNode.removeChild(toast);
                                }
                            }, 3000);
                        }
                    }
                });
                
                renderNewImagePreview();
            });
            
            tempInput.click();
        });
    }

    // Délégation d'événements pour les nouvelles images
    imagePreview.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-new-image') || e.target.closest('.remove-new-image')) {
            const button = e.target.closest('.remove-new-image');
            const index = parseInt(button.dataset.index);
            
            selectedNewFiles.splice(index, 1);
            
            if (index === primaryNewImageIndex) {
                primaryNewImageIndex = -1;
            } else if (index < primaryNewImageIndex) {
                primaryNewImageIndex--;
            }
            
            renderNewImagePreview();
        }
        
        if (e.target.classList.contains('set-primary-new') || e.target.closest('.set-primary-new')) {
            const button = e.target.closest('.set-primary-new');
            primaryNewImageIndex = parseInt(button.dataset.index);
            renderNewImagePreview();
        }
    });

    // Suppression d'image
    document.querySelectorAll('.delete-image').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            if (confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
                fetch(`/admin/sages-home/residences/${imageId}/image`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`image-${imageId}`).remove();
                    } else {
                        alert('Erreur lors de la suppression de l\'image');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la suppression de l\'image');
                });
            }
        });
    });

    // Définir image principale
    document.querySelectorAll('.make-primary').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            
            fetch(`/admin/sages-home/residences/${imageId}/make-primary`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Rechargement pour mettre à jour l'affichage
                } else {
                    alert('Erreur lors de la mise à jour');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la mise à jour');
            });
        });
    });
});
</script>
@endsection