@extends('layouts.app')

@section('title', 'Paiement - Réservation #' . $booking->booking_number)

@section('content')
<div class="container py-5">
    <!-- Progress Indicator -->
    <div class="row mb-5" style="padding-top: 50px;">
        <div class="col-12">
            <div class="progress-indicator">
                <!-- Progress Bar -->
                <div class="progress-bar-container">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" data-progress="66"></div>
                    </div>
                </div>
                
                <!-- Progress Steps -->
                <div class="progress-steps d-flex justify-content-between">
                    <div class="step completed">
                        <div class="step-circle">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-label">Étape 1</div>
                            <div class="step-title">Détails complétés</div>
                        </div>
                    </div>
                    <div class="step completed active">
                        <div class="step-circle">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-label">Étape 2</div>
                            <div class="step-title">Paiement en cours</div>
                        </div>
                    </div>
                    <div class="step upcoming">
                        <div class="step-circle">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-label">Étape 3</div>
                            <div class="step-title">Confirmation</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Payment Methods -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-green mb-0">
                        <i class="fas fa-credit-card me-2 text-gold"></i>
                        Choisissez votre mode de paiement
                    </h3>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('booking.process-payment', $booking) }}" id="paymentForm">
                        @csrf
                        
                        <div class="payment-methods">
                            <!-- Wave Payment -->
                            <div class="payment-method mb-3">
                                <input type="radio" class="form-check-input" name="payment_method" 
                                       value="wave" id="wave" required>
                                <label for="wave" class="form-check-label payment-label">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-icon me-3">
                                            <i class="fas fa-mobile-alt text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <i class="fas fa-star me-1 text-warning"></i>
                                                Wave
                                            </h6>
                                            <small class="text-muted">Paiement mobile sécurisé</small>
                                        </div>
                                        <div class="payment-badge">
                                            <span class="badge bg-primary">Rapide</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- PayPal Payment -->
                            <div class="payment-method mb-3">
                                <input type="radio" class="form-check-input" name="payment_method" 
                                       value="paypal" id="paypal" required>
                                <label for="paypal" class="form-check-label payment-label">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-icon me-3">
                                            <i class="fab fa-paypal text-info" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <i class="fas fa-shield-alt me-1 text-success"></i>
                                                PayPal
                                            </h6>
                                            <small class="text-muted">Paiement en ligne sécurisé</small>
                                        </div>
                                        <div class="payment-badge">
                                            <span class="badge bg-info">Sécurisé</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Cash Payment -->
                            <div class="payment-method mb-3">
                                <input type="radio" class="form-check-input" name="payment_method" 
                                       value="cash" id="cash" required>
                                <label for="cash" class="form-check-label payment-label">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-icon me-3">
                                            <i class="fas fa-money-bill-wave text-success" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <i class="fas fa-clock me-1 text-warning"></i>
                                                Espèces
                                            </h6>
                                            <small class="text-muted">Paiement à l'arrivée</small>
                                        </div>
                                        <div class="payment-badge">
                                            <span class="badge bg-success">Flexible</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div id="paymentInfo" class="alert alert-info mt-4" style="display: none;">
                            <div id="waveInfo" class="payment-info-content" style="display: none;">
                                <h6>
                                    <i class="fas fa-info-circle me-2 text-primary"></i>
                                    Paiement Wave
                                </h6>
                                <p class="mb-2">
                                    <i class="fas fa-mobile-alt me-2 text-muted"></i>
                                    Vous serez redirigé vers l'application Wave pour finaliser votre paiement.
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1 text-success"></i>
                                    Le paiement est sécurisé et traité par Wave.
                                </small>
                            </div>
                            
                            <div id="paypalInfo" class="payment-info-content" style="display: none;">
                                <h6>
                                    <i class="fas fa-info-circle me-2 text-info"></i>
                                    Paiement PayPal
                                </h6>
                                <p class="mb-2">
                                    <i class="fas fa-external-link-alt me-2 text-muted"></i>
                                    Vous serez redirigé vers PayPal pour finaliser votre paiement.
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-credit-card me-1 text-info"></i>
                                    Vous pouvez payer avec votre compte PayPal ou une carte bancaire.
                                </small>
                            </div>
                            
                            <div id="cashInfo" class="payment-info-content" style="display: none;">
                                <h6>
                                    <i class="fas fa-info-circle me-2 text-success"></i>
                                    Paiement en espèces
                                </h6>
                                <p class="mb-2">
                                    <i class="fas fa-check-circle me-2 text-muted"></i>
                                    Votre réservation sera confirmée et vous paierez à votre arrivée.
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-money-bill-wave me-1 text-success"></i>
                                    Assurez-vous d'avoir le montant exact lors de votre arrivée.
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 pt-3" style="border-top: 2px solid #f1f3f4;">
                            <a href="{{ route('residences.show', $booking->residence->slug) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Retour aux détails
                            </a>
                            
                            <button type="submit" class="btn btn-payment btn-lg px-4" id="paymentBtn" disabled>
                                <span id="paymentBtnText">Choisissez un mode de paiement</span>
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Info -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h6 class="text-green mb-3">
                        <i class="fas fa-shield-check me-2 text-gold"></i>
                        Vos données sont protégées
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <i class="fas fa-lock text-success" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2"><small><strong>SSL Sécurisé</strong></small></p>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-check-double text-success" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2"><small><strong>Paiement Vérifié</strong></small></p>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-headset text-success" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2"><small><strong>Support 24/7</strong></small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Booking Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 100px;">
                <div class="card-header">
                    <h4 class="card-title text-green mb-0">
                        <i class="fas fa-receipt me-2 text-gold"></i>
                        Récapitulatif de la commande
                    </h4>
                </div>
                
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="badge bg-light text-dark">Réservation #{{ $booking->booking_number }}</span>
                    </div>

                    <!-- Residence Info -->
                    <div class="d-flex mb-3">
                        @if($booking->residence->primaryImage)
                            <img src="{{ $booking->residence->primaryImage->full_url }}" 
                                 class="me-3" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                                 alt="{{ $booking->residence->name }}">
                        @endif
                        
                        <div>
                            <h6 class="mb-1">{{ $booking->residence->name }}</h6>
                            <small class="text-muted">{{ $booking->residence->type_display }}</small>
                            <div class="text-muted small">
                                <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($booking->residence->address, 30) }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Booking Details -->
                    <div class="booking-details">
                        <div class="row mb-2">
                            <div class="col-6">
                                <i class="fas fa-calendar-check me-2 text-success"></i>
                                <strong>Arrivée:</strong>
                            </div>
                            <div class="col-6 text-end">{{ $booking->check_in->format('d/m/Y') }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6">
                                <i class="fas fa-calendar-minus me-2 text-warning"></i>
                                <strong>Départ:</strong>
                            </div>
                            <div class="col-6 text-end">{{ $booking->check_out->format('d/m/Y') }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6">
                                <i class="fas fa-users me-2 text-primary"></i>
                                <strong>Voyageurs:</strong>
                            </div>
                            <div class="col-6 text-end">{{ $booking->guests }}</div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <i class="fas fa-moon me-2 text-info"></i>
                                <strong>Nuits:</strong>
                            </div>
                            <div class="col-6 text-end">{{ $booking->nights }}</div>
                        </div>

                        <hr>

                        <!-- Price Breakdown -->
                        <div class="price-breakdown">
                            <div class="row mb-2">
                                <div class="col-8">
                                    <i class="fas fa-bed me-2 text-muted"></i>
                                    {{ number_format($booking->price_per_night, 0) }} FCFA x {{ $booking->nights }} nuits
                                </div>
                                <div class="col-4 text-end">{{ number_format($booking->total_price, 0) }} FCFA</div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-8">
                                    <i class="fas fa-percent me-2 text-info"></i>
                                    Taxes et frais
                                </div>
                                <div class="col-4 text-end">{{ number_format($booking->tax_amount, 0) }} FCFA</div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-8">
                                    <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                    <strong>Total à payer</strong>
                                </div>
                                <div class="col-4 text-end">
                                    <strong class="text-gold fs-4">{{ number_format($booking->final_amount, 0) }} FCFA</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($booking->special_requests)
                        <hr>
                        <div>
                            <h6 class="text-green mb-2">
                                <i class="fas fa-comment-dots me-2 text-gold"></i>
                                Demandes spéciales
                            </h6>
                            <div class="d-flex align-items-start">
                                <i class="fas fa-quote-left me-2 text-muted mt-1"></i>
                                <small class="text-muted">{{ $booking->special_requests }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress-indicator {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
    position: relative;
    overflow: hidden;
}

.progress-indicator::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--green-dark), var(--gold-end));
    border-radius: 20px 20px 0 0;
}

.progress-bar-container {
    margin-bottom: 2rem;
}

.progress-bar-bg {
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--green-dark) 0%, var(--gold-end) 100%);
    border-radius: 10px;
    transition: width 1s ease;
    position: relative;
    width: 66%;
}

.progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 50%;
    right: -8px;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    background: var(--gold-end);
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.progress-steps {
    position: relative;
}

.step {
    text-align: center;
    flex: 1;
    position: relative;
    z-index: 2;
}

.step-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    margin: 0 auto 1rem;
    position: relative;
    transition: all 0.4s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.step.completed .step-circle {
    background: linear-gradient(135deg, var(--green-dark), #4CAF50);
    color: white;
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
}

.step.active .step-circle {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    color: white;
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
    animation: pulse-active 2s infinite;
}

.step.upcoming .step-circle {
    background: #f8f9fa;
    color: #6c757d;
    border: 3px solid #dee2e6;
}

.step-content {
    max-width: 140px;
    margin: 0 auto;
}

.step-label {
    font-size: 0.75rem;
    color: var(--gold-end);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.step.upcoming .step-label {
    color: #6c757d;
}

.step-title {
    font-size: 0.95rem;
    color: var(--green-dark);
    font-weight: 600;
    line-height: 1.2;
}

.step.upcoming .step-title {
    color: #6c757d;
    font-weight: 500;
}

@keyframes pulse-active {
    0% {
        box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.6);
        transform: scale(1.05);
    }
    100% {
        box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        transform: scale(1);
    }
}

.payment-method {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.2rem;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fafafa;
}

.payment-method:hover {
    border-color: var(--gold-end);
    background: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.payment-method input[type="radio"]:checked + .payment-label {
    color: var(--green-dark);
}

.payment-method:has(input[type="radio"]:checked) {
    border-color: var(--gold-end);
    background: linear-gradient(135deg, rgba(242, 209, 138, 0.1), rgba(255, 255, 255, 0.9));
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.2);
}

.payment-label {
    cursor: pointer;
    margin: 0;
    width: 100%;
}

.payment-icon {
    width: 60px;
    text-align: center;
}

.payment-badge {
    margin-left: auto;
}

.text-gold {
    color: var(--gold-end) !important;
}

.btn-green {
    background: linear-gradient(135deg, var(--green-dark), #4CAF50);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.btn-green:hover {
    background: linear-gradient(135deg, #4CAF50, var(--green-dark));
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
}

.btn-payment {
    background: #6c757d;
    border: none;
    color: white;
    transition: all 0.3s ease;
    opacity: 0.7;
}

.btn-payment:disabled {
    background: #6c757d;
    color: white;
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-payment.btn-wave {
    background: linear-gradient(135deg, #007bff, #0056b3);
    opacity: 1;
}

.btn-payment.btn-wave:hover {
    background: linear-gradient(135deg, #0056b3, #007bff);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.3);
}

.btn-payment.btn-paypal {
    background: linear-gradient(135deg, #0070ba, #003087);
    opacity: 1;
}

.btn-payment.btn-paypal:hover {
    background: linear-gradient(135deg, #003087, #0070ba);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 112, 186, 0.3);
}

.btn-payment.btn-cash {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    opacity: 1;
}

.btn-payment.btn-cash:hover {
    background: linear-gradient(135deg, #1e7e34, #28a745);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    transform: translateY(-2px);
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.card-header {
    background: linear-gradient(135deg, var(--green-dark), #4CAF50);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    border: none;
}

@media (max-width: 768px) {
    .progress-indicator {
        padding: 2rem 1rem;
    }
    
    .step-circle {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .step-content {
        max-width: 100px;
    }
    
    .step-label {
        font-size: 0.7rem;
    }
    
    .step-title {
        font-size: 0.85rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentBtn = document.getElementById('paymentBtn');
    const paymentBtnText = document.getElementById('paymentBtnText');
    const paymentInfo = document.getElementById('paymentInfo');
    const paymentForm = document.getElementById('paymentForm');

    // Handle payment method selection
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            updatePaymentInfo(this.value);
            updatePaymentButton(this.value);
        });
    });

    function updatePaymentInfo(method) {
        // Hide all info contents
        document.querySelectorAll('.payment-info-content').forEach(content => {
            content.style.display = 'none';
        });

        // Show selected method info
        const infoElement = document.getElementById(method + 'Info');
        if (infoElement) {
            infoElement.style.display = 'block';
            paymentInfo.style.display = 'block';
        }
    }

    function updatePaymentButton(method) {
        paymentBtn.disabled = false;
        
        // Remove all payment method classes
        paymentBtn.classList.remove('btn-wave', 'btn-paypal', 'btn-cash');
        
        const buttonTexts = {
            'wave': 'Payer avec Wave',
            'paypal': 'Payer avec PayPal',
            'cash': 'Confirmer la réservation'
        };
        
        // Add specific payment method class for styling
        const buttonClasses = {
            'wave': 'btn-wave',
            'paypal': 'btn-paypal', 
            'cash': 'btn-cash'
        };
        
        if (buttonClasses[method]) {
            paymentBtn.classList.add(buttonClasses[method]);
        }
        
        paymentBtnText.textContent = buttonTexts[method] || 'Procéder au paiement';
    }

    // Handle form submission
    paymentForm.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        
        if (!selectedMethod) {
            e.preventDefault();
            alert('Veuillez sélectionner un mode de paiement.');
            return;
        }

        // Show loading state
        paymentBtn.disabled = true;
        
        const loadingTexts = {
            'wave': 'Redirection vers Wave...',
            'paypal': 'Redirection vers PayPal...',
            'cash': 'Confirmation en cours...'
        };
        
        paymentBtnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + 
                                 (loadingTexts[selectedMethod.value] || 'Traitement...');
    });
});
</script>
@endpush