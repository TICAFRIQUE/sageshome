@extends('layouts.app')

@section('title', 'Réservation - ' . $residence->name)

@section('content')
<div class="container py-5">
    <!-- Progress Indicator -->
    <div class="row mb-5 my-5" >
        <div class="col-12">
            <div class="progress-indicator">
                <!-- Progress Bar -->
                {{-- <div class="progress-bar-container">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" data-progress="33"></div>
                    </div>
                </div> --}}
                
                <!-- Progress Steps -->
                <div class="progress-steps d-flex justify-content-between">
                    <div class="step completed active">
                        <div class="step-circle">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-label">Étape 1</div>
                            <div class="step-title">Détails de réservation</div>
                        </div>
                    </div>
                    <div class="step upcoming">
                        <div class="step-circle">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-label">Étape 2</div>
                            <div class="step-title">Paiement sécurisé</div>
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
        <!-- Left Column - Booking Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-green mb-0">Détails de la réservation</h3>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('booking.store') }}" id="bookingForm">
                        @csrf
                        <input type="hidden" name="residence_id" value="{{ $residence->id }}">
                        <input type="hidden" name="check_in" value="{{ $checkIn }}">
                        <input type="hidden" name="check_out" value="{{ $checkOut }}">
                        <input type="hidden" name="guests" value="{{ $guests }}">

                        <!-- User Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-green mb-3">Informations du voyageur principal</h5>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="user_name" class="form-label">Nom complet</label>
                                <input type="text" class="form-control" id="user_name" 
                                       value="{{ Auth::user()->username }}" readonly>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="user_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="user_email" 
                                       value="{{ Auth::user()->email }}" readonly>
                            </div>
                            
                            <div class="col-md-6 mt-3">
                                <label for="user_phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="user_phone" 
                                       value="{{ Auth::user()->phone }}" readonly>
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-green mb-3">Demandes spéciales (optionnel)</h5>
                                <textarea name="special_requests" class="form-control" rows="4" 
                                          placeholder="Avez-vous des demandes particulières pour votre séjour ? (heure d'arrivée tardive, équipements spéciaux, etc.)">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        J'accepte les <a href="#" class="text-primary">conditions générales</a> 
                                        et la <a href="#" class="text-primary">politique de confidentialité</a>
                                    </label>
                                </div>
                                
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="cancellation" required>
                                    <label class="form-check-label" for="cancellation">
                                        J'ai lu et compris la <a href="#" class="text-primary">politique d'annulation</a>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            {{-- <a href="{{ route('residences.show', $residence->slug) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Retour
                            </a> --}}

                            <a href="#" id="backBtn" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Retour
                            </a>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                Passer au paiement <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column - Booking Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 100px;">
                <div class="card-header">
                    <h4 class="card-title text-green mb-0">Récapitulatif</h4>
                </div>
                
                <div class="card-body">
                    <!-- Residence Info -->
                    <div class="d-flex mb-3">
                        @if($residence->primaryImage)
                            <img src="{{ $residence->primaryImage->full_url }}" 
                                 class="me-3" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                                 alt="{{ $residence->name }}">
                        @endif
                        
                        <div>
                            <h6 class="mb-1">{{ $residence->name }}</h6>
                            <small class="text-muted">{{ $residence->type_display }}</small>
                            <div class="text-muted small">
                                <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($residence->address, 30) }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Booking Details -->
                    <div class="booking-details">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Arrivée:</strong></div>
                            <div class="col-6 text-end">{{ \Carbon\Carbon::parse($checkIn)->format('d/m/Y') }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6"><strong>Départ:</strong></div>
                            <div class="col-6 text-end">{{ \Carbon\Carbon::parse($checkOut)->format('d/m/Y') }}</div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6"><strong>Voyageurs:</strong></div>
                            <div class="col-6 text-end">{{ $guests }}</div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6"><strong>Nuits:</strong></div>
                            <div class="col-6 text-end">{{ $priceInfo['nights'] }}</div>
                        </div>

                        <hr>

                        <!-- Price Breakdown -->
                        <div class="price-breakdown">
                            <div class="row mb-2">
                                <div class="col-8">{{ number_format($priceInfo['price_per_night'], 0 , ',', ' ') }} FCFA x {{ $priceInfo['nights'] }} nuits<br><small class="text-muted">({{ number_format(fcfa_to_eur($priceInfo['price_per_night']), 2, ',', ' ') }} € x {{ $priceInfo['nights'] }} nuits)</small></div>
                                <div class="col-4 text-end">{{ number_format($priceInfo['total_price'], 0 , ',', ' ') }} FCFA<br><small class="text-muted">({{ number_format(fcfa_to_eur($priceInfo['total_price']), 2, ',', ' ') }} €)</small></div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-8">Taxes et frais (10%)</div>
                                <div class="col-4 text-end">{{ number_format($taxAmount, 0 , ',', ' ') }} FCFA<br><small class="text-muted">({{ number_format(fcfa_to_eur($taxAmount), 2, ',', ' ') }} €)</small></div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-8"><strong>Total</strong></div>
                                <div class="col-4 text-end">
                                    <strong class="text-gold fs-5">{{ number_format($finalAmount, 0 , ',', ' ') }} FCFA</strong><br>
                                    <span class="text-muted">({{ number_format(fcfa_to_eur($finalAmount), 2, ',', ' ') }} €)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cancellation Policy -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-green mb-2">Politique d'annulation</h6>
                        <small class="text-muted">
                            Annulation gratuite jusqu'à 24 heures avant l'arrivée. 
                            Après cette période, la première nuit sera facturée.
                        </small>
                    </div>
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
    padding: 1rem 1rem;
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

/* Progress Bar */
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
    width: 33%;
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

/* Progress Steps */
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
    width: 40px;
    height: 40px;
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

/* Step States */
.step.completed .step-circle {
    background: linear-gradient(135deg, var(--green-dark), #4CAF50);
    color: white;
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
}

.step.active .step-circle {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    color: white;
    box-shadow: 0 6px 20px rgba(204, 167, 56, 0.4);
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

/* Responsive */
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

@media (max-width: 576px) {
    .step-circle {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }
    
    .step-title {
        font-size: 0.8rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Back button functionality
    const backBtn = document.getElementById('backBtn');
    backBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.history.back();
    });

    // Form validation and submission handling
    const form = document.getElementById('bookingForm');
    const termsCheckbox = document.getElementById('terms');
    const cancellationCheckbox = document.getElementById('cancellation');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Validate form before submission
    form.addEventListener('submit', function(e) {
        if (!termsCheckbox.checked || !cancellationCheckbox.checked) {
            e.preventDefault();
            alert('Veuillez accepter les conditions générales et la politique d\'annulation.');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Traitement...';
    });
});
</script>
@endpush