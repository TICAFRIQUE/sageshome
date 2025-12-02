@extends('layouts.app')

@section('title', 'Confirmation - Réservation #' . $booking->booking_number)

@section('content')
<div class="container py-5">
    <!-- Progress Indicator -->
    <div class="row mb-5" style="padding-top: 50px;">
        <div class="col-12">
            <div class="progress-indicator">
                <!-- Progress Bar -->
                {{-- <div class="progress-bar-container">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill completed" data-progress="100"></div>
                    </div>
                </div> --}}
                
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
                    <div class="step completed">
                        <div class="step-circle">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="step-content">
                            <div class="step-label">Étape 2</div>
                            <div class="step-title">Paiement effectué</div>
                        </div>
                    </div>
                    <div class="step completed active">
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

    <!-- Success Message -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <div class="success-animation mb-4">
                <div class="success-checkmark">
                    <div class="check-icon">
                        <span class="icon-line line-tip"></span>
                        <span class="icon-line line-long"></span>
                        <div class="icon-circle"></div>
                        <div class="icon-fix"></div>
                    </div>
                </div>
            </div>
            <h1 class="text-success mb-3">
                <i class="fas fa-check-circle me-2"></i>
                Réservation confirmée !
            </h1>
            <p class="lead text-muted">
                <i class="fas fa-ticket-alt me-2 text-primary"></i>
                Votre réservation <strong class="text-success">#{{ $booking->booking_number }}</strong> a été confirmée avec succès.
            </p>
            <div class="success-details">
                <p class="text-muted">
                    <i class="fas fa-envelope me-2 text-info"></i>
                    Un email de confirmation a été envoyé à {{ Auth::user()->email }}
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Booking Details -->
        <div class="col-lg-8">
            <!-- Booking Summary -->
            <div class="card mb-4">
                <div class="card-header bg-success-light">
                    <h4 class="card-title text-success mb-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        Détails de votre réservation
                    </h4>
                </div>
                
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            @if($booking->residence->primaryImage)
                                <img src="{{ $booking->residence->primaryImage->full_url }}" 
                                     class="img-fluid rounded" alt="{{ $booking->residence->name }}"
                                     style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="width: 100%; height: 200px;">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-8">
                            <h5 class="text-green mb-3">{{ $booking->residence->name }}</h5>
                            
                            <div class="booking-info">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <strong><i class="fas fa-calendar-check me-2 text-success"></i>Arrivée:</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        {{ $booking->check_in->format('l j F Y') }}
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <strong><i class="fas fa-calendar-times me-2 text-warning"></i>Départ:</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        {{ $booking->check_out->format('l j F Y') }}
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <strong><i class="fas fa-users me-2 text-primary"></i>Voyageurs:</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        {{ $booking->guests }}
                                    </div>
                                </div>
                                
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <strong><i class="fas fa-moon me-2 text-info"></i>Nuits:</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        {{ $booking->nights }}
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong><i class="fas fa-map-marker-alt me-2 text-danger"></i>Adresse:</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        {{ $booking->residence->address }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="card mb-4">
                <div class="card-header bg-info-light">
                    <h4 class="card-title text-info mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Statut du paiement
                    </h4>
                </div>
                
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            @if($booking->payment_status === 'paid')
                                <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                            @elseif($booking->payment_status === 'partial')
                                <i class="fas fa-clock text-warning" style="font-size: 2rem;"></i>
                            @else
                                <i class="fas fa-exclamation-circle text-danger" style="font-size: 2rem;"></i>
                            @endif
                        </div>
                        
                        <div>
                            <h6 class="mb-1">{{ $booking->payment_status_display }}</h6>
                            <p class="text-muted mb-0">
                                @if($booking->payment_status === 'paid')
                                    Votre paiement a été traité avec succès.
                                @elseif($booking->payment_status === 'pending')
                                    Le paiement sera effectué à votre arrivée.
                                @else
                                    Contactez-nous si vous avez des questions sur votre paiement.
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- Payment Details -->
                    @if($booking->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Méthode</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($booking->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->method_display }}</td>
                                            <td>{{ number_format($payment->amount, 0 , ',', ' ') }} FCFA</td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                                    {{ $payment->status_display }}
                                                </span>
                                            </td>
                                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Special Requests -->
            @if($booking->special_requests)
                <div class="card">
                    <div class="card-header bg-warning-light">
                        <h4 class="card-title text-warning mb-0">
                            <i class="fas fa-comment-dots me-2"></i>
                            Demandes spéciales
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        <p class="mb-0">{{ $booking->special_requests }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Actions & Info -->
        <div class="col-lg-4">
            <!-- Price Summary -->
            <div class="card mb-4">
                <div class="card-header bg-primary-light">
                    <h5 class="card-title text-primary mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        Récapitulatif des prix
                    </h5>
                </div>
                
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ number_format($booking->price_per_night, 0 , ',', ' ') }} FCFA x {{ $booking->nights }} nuits</span>
                        <span>{{ number_format($booking->total_price, 0 , ',', ' ') }} FCFA</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Taxes et frais</span>
                        <span>{{ number_format($booking->tax_amount, 0 , ',', ' ') }} FCFA</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong class="text-gold fs-5">{{ number_format($booking->final_amount, 0 , ',', ' ') }} FCFA</strong>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <a href="{{ route('booking.my-bookings') }}" class="btn btn-success w-100 mb-2">
                        <i class="fas fa-list me-2"></i>Voir mes réservations
                    </a>
                    
                    <a href="{{ route('home') }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-home me-2"></i>Retour à l'accueil
                    </a>
                    
                    <button onclick="window.print()" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-print me-2"></i>Imprimer
                    </button>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card">
                <div class="card-header bg-secondary-light">
                    <h5 class="card-title text-secondary mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Besoin d'aide ?
                    </h5>
                </div>
                
                <div class="card-body">
                    <p class="mb-3">
                        <i class="fas fa-headset me-2 text-primary"></i>
                        Notre équipe est là pour vous accompagner.
                    </p>
                    
                    <div class="contact-info">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone text-success me-2"></i>
                            <a href="tel:+221123456789" class="text-decoration-none">+225 07 15 82 05 96</a>
                        </div>
                        
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope text-info me-2"></i>
                            <a href="mailto:support@sageshome.com" class="text-decoration-none">support@sageshome.com</a>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock text-warning me-2"></i>
                            <span>Support 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Information -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-info border-info">
                <h6>
                    <i class="fas fa-info-circle me-2"></i>
                    Informations importantes
                </h6>
                <ul class="mb-0">
                    <li><i class="fas fa-id-card me-2 text-primary"></i>Veuillez vous présenter à l'accueil avec une pièce d'identité valide</li>
                    <li><i class="fas fa-clock me-2 text-success"></i>L'enregistrement se fait à partir de 15h00</li>
                    <li><i class="fas fa-door-open me-2 text-warning"></i>La libération des chambres doit se faire avant 11h00</li>
                    <li><i class="fas fa-envelope-check me-2 text-info"></i>Un email de confirmation a été envoyé à {{ Auth::user()->email }}</li>
                </ul>
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
    background: linear-gradient(90deg, #28a745, #20c997);
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
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    border-radius: 10px;
    transition: width 1s ease;
    position: relative;
    width: 100%;
}

.progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 50%;
    right: -8px;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    background: #20c997;
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

.step.completed .step-circle {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
}

.step.active .step-circle {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    animation: pulse-success 2s infinite;
}

.step-content {
    max-width: 140px;
    margin: 0 auto;
}

.step-label {
    font-size: 0.75rem;
    color: #28a745;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.step-title {
    font-size: 0.95rem;
    color: #28a745;
    font-weight: 600;
    line-height: 1.2;
}

@keyframes pulse-success {
    0% {
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.6);
        transform: scale(1.05);
    }
    100% {
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        transform: scale(1);
    }
}

/* Success Animation */
.success-animation {
    margin: 2rem 0;
}

.success-checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #28a745;
    stroke-miterlimit: 10;
    margin: 10px auto;
    box-shadow: inset 0px 0px 0px #28a745;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    position: relative;
}

.success-checkmark .check-icon {
    width: 56px;
    height: 56px;
    position: absolute;
    left: 12px;
    top: 12px;
    border-radius: 50%;
    border: 2px solid #28a745;
}

.success-checkmark .icon-line {
    height: 2px;
    background-color: #28a745;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.success-checkmark .icon-line.line-tip {
    top: 25px;
    left: 14px;
    width: 25px;
    transform: rotate(45deg);
    animation: icon-line-tip 0.75s;
}

.success-checkmark .icon-line.line-long {
    top: 32px;
    right: 8px;
    width: 47px;
    transform: rotate(-45deg);
    animation: icon-line-long 0.75s;
}

@keyframes icon-line-tip {
    0% { width: 0; left: 1px; top: 19px; }
    54% { width: 0; left: 1px; top: 19px; }
    70% { width: 50px; left: -8px; top: 37px; }
    84% { width: 17px; left: 21px; top: 48px; }
    100% { width: 25px; left: 14px; top: 25px; }
}

@keyframes icon-line-long {
    0% { width: 0; right: 46px; top: 54px; }
    65% { width: 0; right: 46px; top: 54px; }
    84% { width: 55px; right: 0px; top: 35px; }
    100% { width: 47px; right: 8px; top: 32px; }
}

/* Card Headers */
.bg-success-light {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1)) !important;
}

.bg-info-light {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(108, 117, 125, 0.1)) !important;
}

.bg-warning-light {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 143, 0, 0.1)) !important;
}

.bg-primary-light {
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(108, 117, 125, 0.1)) !important;
}

.bg-secondary-light {
    background: linear-gradient(135deg, rgba(108, 117, 125, 0.1), rgba(73, 80, 87, 0.1)) !important;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border: none;
    padding: 1.5rem;
}

.success-details {
    margin-top: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(32, 201, 151, 0.05));
    border-radius: 10px;
    border-left: 4px solid #28a745;
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
    
    .success-checkmark {
        width: 60px;
        height: 60px;
    }
}

@media print {
    .navbar, .footer, .btn, .alert {
        display: none !important;
    }
    
    .container {
        max-width: 100% !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
@endpush