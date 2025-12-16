@extends('backend.layouts.master')

@section('title', 'Créer une Réservation')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .step {
            flex: 1;
            text-align: center;
            padding: 1rem;
            position: relative;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 2rem;
            right: -50%;
            width: 100%;
            height: 2px;
            background: #e9ecef;
            z-index: -1;
        }

        .step.active::after {
            background: #0d6efd;
        }

        .step.completed::after {
            background: #198754;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .step.active .step-number {
            background: #0d6efd;
            color: white;
        }

        .step.completed .step-number {
            background: #198754;
            color: white;
        }

        .availability-status {
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            display: none;
        }

        .availability-status.available {
            background: #d1e7dd;
            border: 1px solid #badbcc;
            color: #0f5132;
        }

        .availability-status.unavailable {
            background: #f8d7da;
            border: 1px solid #f5c2c7;
            color: #842029;
        }

        .price-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            position: sticky;
            top: 20px;
        }
    </style>
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Créer une Réservation</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Réservations</a></li>
                        <li class="breadcrumb-item active">Nouvelle Réservation</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form action="{{ route('admin.bookings.store') }}" method="POST" id="bookingForm">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <!-- Sélection de la résidence et dates -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-building-line me-2"></i>Résidence et Période
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Date d'arrivée</label>
                                <input type="date" class="form-control" name="check_in" id="check_in"
                                    min="{{ date('Y-m-d') }}" value="{{ old('check_in') }}" required>
                                @error('check_in')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Date de départ</label>
                                <input type="date" class="form-control" name="check_out" id="check_out"
                                    min="{{ date('Y-m-d') }}" value="{{ old('check_out') }}" required>
                                @error('check_out')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div id="alertContainer"></div>
                            </div>

                            <div class="col-md-12 mb-3" id="residenceSelectContainer" style="display: none;">
                                <label class="form-label required">Résidences disponibles</label>
                                <select class="form-select select2" name="residence_id" id="residence_id" required>
                                    <option value="">Sélectionnez une résidence</option>
                                </select>
                                <small class="text-muted">
                                    <i class="ri-information-line"></i> Seules les résidences disponibles pour ces dates sont affichées
                                </small>
                                @error('residence_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations du client -->
                <div class="card" id="clientInfoCard" style="display: none;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-user-line me-2"></i>Informations du Client
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">


                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Nom</label>
                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}"
                                    required>
                                @error('last_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Prénom</label>
                                <input type="text" class="form-control" name="first_name"
                                    value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                    required>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Téléphone</label>
                                <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}"
                                    required>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pays</label>
                                <input type="text" class="form-control" name="country"
                                    value="{{ old('country', 'Côte d\'Ivoire') }}">
                                @error('country')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Nombre de personnes</label>
                                <input type="number" class="form-control" name="guests" min="1"
                                    value="{{ old('guests', 1) }}" required>
                                @error('guests')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Demandes spéciales</label>
                                <textarea class="form-control" name="special_requests" rows="3">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de paiement -->
                <div class="card" id="paymentInfoCard" style="display: none;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-money-dollar-circle-line me-2"></i>Informations de Paiement
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Méthode de paiement</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="">Sélectionnez une méthode</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Espèces
                                    </option>
                                    <option value="wave" {{ old('payment_method') == 'wave' ? 'selected' : '' }}>Wave
                                    </option>
                                    <option value="bank_transfer"
                                        {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement bancaire
                                    </option>
                                    <option value="credit_card"
                                        {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Carte bancaire
                                    </option>
                                    <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>
                                        PayPal</option>
                                </select>
                                @error('payment_method')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Statut du paiement</label>
                                <select class="form-select" name="payment_status" required>
                                    <option value="completed"
                                        {{ old('payment_status', 'completed') == 'completed' ? 'selected' : '' }}>Payé
                                    </option>
                                    <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>En
                                        attente</option>
                                </select>
                                @error('payment_status')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- <div class="col-12 mb-3">
                                <label class="form-label">Notes administratives</label>
                                <textarea class="form-control" name="notes" rows="3"
                                    placeholder="Notes internes (non visibles par le client)">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div> --}}
                        </div>

                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Note :</strong> La réservation sera créée par <strong>{{ Auth::user()->name }}</strong>
                            ({{ Auth::user()->email }})
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé -->
            <div class="col-lg-4">
                <div class="price-summary">
                    <h5 class="mb-3"><i class="ri-file-list-3-line me-2"></i>Résumé de la réservation</h5>

                    <div id="summaryContent" style="display: none;">
                        <div class="mb-3">
                            <strong id="residenceName">-</strong>
                            <div class="text-muted small" id="residenceDates">-</div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span id="nightsLabel">-</span>
                            <span id="nightsAmount">-</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <strong class="h5 mb-0">Total</strong>
                            <strong class="h5 mb-0 text-primary" id="totalAmount">-</strong>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="submitBtn" disabled>
                            <i class="ri-save-line me-2"></i>Créer la réservation
                        </button>
                    </div>

                    <div id="summaryPlaceholder">
                        <p class="text-muted text-center">
                            <i class="ri-information-line fs-48 d-block mb-2"></i>
                            Sélectionnez une résidence et vérifiez la disponibilité pour voir le résumé
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialiser Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            let availableResidencesData = [];

            // Fonction pour afficher les alertes
            function showAlert(message, type = 'danger') {
                const iconMap = {
                    'danger': 'error-warning',
                    'warning': 'alert',
                    'info': 'information',
                    'success': 'checkbox-circle'
                };
                
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                        <i class="ri-${iconMap[type]}-line me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#alertContainer').html(alertHtml);
            }

            // Fonction pour effacer les alertes
            function clearAlerts() {
                $('#alertContainer').empty();
            }

            // Charger les résidences disponibles quand les dates changent
            $('#check_in, #check_out').on('change', function() {
                const checkIn = $('#check_in').val();
                const checkOut = $('#check_out').val();

                clearAlerts();
                $('#residenceSelectContainer').hide();
                $('#residence_id').html('<option value="">Sélectionnez une résidence</option>');
                $('#clientInfoCard, #paymentInfoCard').slideUp();
                $('#summaryContent').hide();
                $('#summaryPlaceholder').show();
                $('#submitBtn').prop('disabled', true);

                if (!checkIn || !checkOut) {
                    return;
                }

                if (checkOut <= checkIn) {
                    showAlert('La date de départ doit être après la date d\'arrivée', 'warning');
                    return;
                }

                // Afficher un loader
                showAlert('<span class="spinner-border spinner-border-sm me-2"></span>Recherche des résidences disponibles...', 'info');

                $.ajax({
                    url: '{{ route('admin.bookings.available-residences') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        check_in: checkIn,
                        check_out: checkOut
                    },
                    success: function(response) {
                        availableResidencesData = response.residences;
                        
                        if (response.residences.length === 0) {
                            showAlert('Aucune résidence disponible pour ces dates. Veuillez choisir d\'autres dates.', 'warning');
                            return;
                        }

                        // Remplir le select avec les résidences disponibles
                        let options = '<option value="">Sélectionnez une résidence</option>';
                        response.residences.forEach(function(residence) {
                            options += `
                                <option value="${residence.id}" 
                                        data-price="${residence.price_per_night}"
                                        data-total="${residence.total}">
                                    ${residence.name} - ${formatPrice(residence.price_per_night)} FCFA/nuit 
                                    (Total: ${formatPrice(residence.total)} FCFA)
                                </option>
                            `;
                        });

                        $('#residence_id').html(options);
                        $('#residenceSelectContainer').slideDown();
                        
                        showAlert(`${response.residences.length} résidence(s) disponible(s) pour ces dates`, 'success');
                    },
                    error: function(xhr) {
                        let errorMessage = 'Erreur lors de la recherche des résidences disponibles';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join('<br>');
                        }
                        
                        showAlert(errorMessage, 'danger');
                    }
                });
            });

            // Quand une résidence est sélectionnée, afficher les sections et le résumé
            $('#residence_id').on('change', function() {
                const residenceId = $(this).val();
                
                if (!residenceId) {
                    $('#clientInfoCard, #paymentInfoCard').slideUp();
                    $('#summaryContent').hide();
                    $('#summaryPlaceholder').show();
                    $('#submitBtn').prop('disabled', true);
                    return;
                }

                const selectedOption = $(this).find('option:selected');
                const residenceData = availableResidencesData.find(r => r.id == residenceId);
                
                if (residenceData) {
                    // Afficher les sections
                    $('#clientInfoCard, #paymentInfoCard').slideDown();
                    
                    // Mettre à jour le résumé
                    updateSummaryFromData(residenceData);
                    
                    $('#submitBtn').prop('disabled', false);
                }
            });

            function updateSummaryFromData(data) {
                const checkIn = $('#check_in').val();
                const checkOut = $('#check_out').val();
                const residenceName = $('#residence_id option:selected').text().split(' - ')[0];
                
                // Calculer les nuitées
                const nights = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
                
                $('#residenceName').text(residenceName);
                $('#residenceDates').text(formatDate(checkIn) + ' → ' + formatDate(checkOut));
                $('#nightsLabel').text(nights + ' nuit' + (nights > 1 ? 's' : '') + ' × ' + formatPrice(data.price_per_night) + ' FCFA');
                $('#nightsAmount').text(formatPrice(data.total) + ' FCFA');
                $('#totalAmount').text(formatPrice(data.total) + ' FCFA');
                
                $('#summaryPlaceholder').hide();
                $('#summaryContent').show();
            }

            function formatPrice(price) {
                return new Intl.NumberFormat('fr-FR').format(price);
            }

            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            // Validation du formulaire
            $('#bookingForm').submit(function(e) {
                const btn = $('#submitBtn');
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Création en cours...');
            });
        });
    </script>
@endsection
