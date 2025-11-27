@extends('backend.layouts.master')

@section('title', 'Ajouter une Résidence')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Ajouter une Résidence</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.residences.index') }}">Résidences</a></li>
                    <li class="breadcrumb-item active">Ajouter</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
<form action="{{ route('admin.residences.store') }}" method="POST" enctype="multipart/form-data" id="residenceForm">
    @csrf
    
    <div class="row">
        <!-- Colonne principale (Gauche) -->
        <div class="col-lg-8">
            <!-- Informations de base -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-information-line me-2"></i>Informations de base
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom de la résidence <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="residence_type_id" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('residence_type_id') is-invalid @enderror" id="residence_type_id" name="residence_type_id" required>
                                    <option value="">Choisir un type</option>
                                    @foreach($residenceTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('residence_type_id') == $type->id ? 'selected' : '' }}>
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Capacité (personnes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                       id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" max="20" required>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price_per_night" class="form-label">Prix par nuit (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price_per_night') is-invalid @enderror" 
                                       id="price_per_night" name="price_per_night" value="{{ old('price_per_night') }}" min="0" required>
                                @error('price_per_night')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description courte <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" maxlength="500" required>{{ old('description') }}</textarea>
                        <div class="form-text">Maximum 500 caractères</div>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="full_description" class="form-label">Description complète</label>
                        <textarea class="form-control @error('full_description') is-invalid @enderror" 
                                  id="full_description" name="full_description" rows="6">{{ old('full_description') }}</textarea>
                        <div class="form-text">Description détaillée pour les clients</div>
                        @error('full_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Localisation -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-map-pin-line me-2"></i>Localisation
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse complète <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                               id="address" name="address" value="{{ old('address') }}" required>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" name="latitude" value="{{ old('latitude') }}">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" name="longitude" value="{{ old('longitude') }}">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne latérale (Droite) -->
        <div class="col-lg-4">
            <!-- Paramètres de publication -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-settings-3-line me-2"></i>Publication
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_available" value="1" id="is_available" checked>
                            <label class="form-check-label" for="is_available">Disponible à la réservation</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                            <label class="form-check-label" for="is_featured">Résidence mise en avant</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line me-1"></i>Enregistrer la résidence
                        </button>
                        <a href="{{ route('admin.residences.index') }}" class="btn btn-secondary">
                            <i class="ri-close-line me-1"></i>Annuler
                        </a>
                    </div>
                </div>
            </div>

            <!-- Aide -->
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">💡 Conseils</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2">• Utilisez un nom descriptif et attrayant</li>
                        <li class="mb-2">• La description courte apparaît dans les listes</li>
                        <li class="mb-2">• Ajoutez des coordonnées GPS pour la carte</li>
                        <li class="mb-0">• Sélectionnez au moins 3 équipements</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section équipements (pleine largeur) -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Équipements et Services</h4>
                </div>
                <div class="card-body">
                    @php
                        $amenityCategories = [
                            'Connectivité' => [
                                'wifi' => 'Wi-Fi',
                                'canal_plus' => 'Abonnement Canal+'
                            ],
                            'Confort' => [
                                'climatiseur' => 'Climatiseur',
                                'chauffe_eau' => 'Chauffe-eau'
                            ],
                            'Cuisine équipée' => [
                                'cuisiniere' => 'Cuisinière',
                                'four' => 'Four',
                                'micro_onde' => 'Micro-ondes',
                                'bouilloire' => 'Bouilloire électrique',
                                'refrigerateur' => 'Réfrigérateur',
                                'mixeur' => 'Mixeur',
                                'ustensiles' => 'Ustensiles de cuisine',
                                'couverts' => 'Couverts complets'
                            ],
                            'Services' => [
                                'menage' => 'Service de ménage',
                                'securite' => 'Sécurité 24H/24 et 7J/7'
                            ],
                            'Loisirs' => [
                                'piscine' => 'Piscine'
                            ],
                            'Informations pratiques' => [
                                'arrivee_autonome' => 'Arrivée autonome',
                                'annulation_gratuite' => 'Annulation gratuite',
                                'animaux_interdits' => 'Animaux de compagnie non autorisés'
                            ]
                        ];
                        $selectedAmenities = old('amenities', []);
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
    </div>

    <!-- Section images (pleine largeur) -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-image-line me-2"></i>Images de la Résidence
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="images" class="form-label">Images <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" 
                               id="images" name="images[]" multiple accept="image/*" required>
                        <div class="form-text">Sélectionnez autant d'images que vous voulez (JPEG, PNG, JPG, GIF - max 1MB chacune). Vous pourrez définir l'image principale et supprimer des images après sélection.</div>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div id="image-preview" class="row mt-3"></div>
                    
                    <!-- Bouton pour ajouter plus d'images -->
                    <div class="mb-3 mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-more-images">
                            <i class="ri-add-line me-1"></i>Ajouter d'autres images
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedFiles = [];
    let primaryImageIndex = 0;
    
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');
    const addMoreBtn = document.getElementById('add-more-images');

    function validateFileSize(file) {
        const maxSize = 1024 * 1024; // 1MB
        return file.size <= maxSize;
    }

    function renderImagePreview() {
        imagePreview.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3';
                col.innerHTML = `
                    <div class="card position-relative">
                        <img src="${e.target.result}" 
                             class="card-img-top" 
                             style="height: 150px; object-fit: cover;"
                             alt="Aperçu">
                        <div class="card-body p-2">
                            <small class="text-muted d-block">${file.name}</small>
                            <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                            <div class="mt-2">
                                ${index === primaryImageIndex ? 
                                    '<span class="badge bg-primary me-1">Principal</span>' : 
                                    `<button type="button" class="btn btn-sm btn-outline-primary me-1 set-primary" data-index="${index}">Définir principal</button>`
                                }
                                <button type="button" class="btn btn-sm btn-outline-danger remove-image" data-index="${index}">
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
        selectedFiles.forEach(file => dt.items.add(file));
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
                const existingFile = selectedFiles.find(f => 
                    f.name === file.name && f.size === file.size
                );
                
                if (!existingFile) {
                    selectedFiles.push(file);
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
        
        renderImagePreview();
        
        // Réinitialiser l'input pour permettre de sélectionner les mêmes fichiers plus tard
        e.target.value = '';
    });

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
                    const existingFile = selectedFiles.find(f => 
                        f.name === file.name && f.size === file.size
                    );
                    
                    if (!existingFile) {
                        selectedFiles.push(file);
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
            
            renderImagePreview();
        });
        
        tempInput.click();
    });

    // Délégation d'événements pour les boutons dynamiques
    imagePreview.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-image') || e.target.closest('.remove-image')) {
            const button = e.target.closest('.remove-image');
            const index = parseInt(button.dataset.index);
            
            selectedFiles.splice(index, 1);
            
            // Ajuster l'index de l'image principale si nécessaire
            if (index === primaryImageIndex) {
                primaryImageIndex = 0;
            } else if (index < primaryImageIndex) {
                primaryImageIndex--;
            }
            
            renderImagePreview();
        }
        
        if (e.target.classList.contains('set-primary') || e.target.closest('.set-primary')) {
            const button = e.target.closest('.set-primary');
            primaryImageIndex = parseInt(button.dataset.index);
            renderImagePreview();
        }
    });
    
    // Ajout d'un champ caché pour indiquer l'image principale
    document.getElementById('residenceForm').addEventListener('submit', function(e) {
        if (selectedFiles.length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une image');
            return;
        }
        
        // Créer un champ caché pour l'index de l'image principale
        const primaryInput = document.createElement('input');
        primaryInput.type = 'hidden';
        primaryInput.name = 'primary_image_index';
        primaryInput.value = primaryImageIndex;
        this.appendChild(primaryInput);
    });
});
</script>
@endsection