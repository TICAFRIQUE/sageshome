@extends('layouts.app')

@section('title', 'Paiement PayPal - ' . $payment->booking->residence->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="text-green mb-0">Paiement PayPal</h3>
                </div>
                
                <div class="card-body text-center">
                    <div class="mb-4">
                        <img src="/images/paypal-logo.png" alt="PayPal" style="height: 60px;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="bi bi-paypal text-primary" style="font-size: 4rem; display: none;"></i>
                    </div>

                    <h4 class="text-green mb-3">Montant à payer</h4>
                    <div class="display-4 text-gold mb-4">{{ number_format($payment->amount, 0) }} FCFA</div>
                    
                    <p class="text-muted mb-4">
                        Réservation #{{ $payment->booking->booking_number }}<br>
                        {{ $payment->booking->residence->name }}
                    </p>

                    <!-- Simulation d'interface PayPal -->
                    <div class="paypal-simulation p-4 bg-light rounded mb-4">
                        <div class="d-flex justify-content-center mb-3">
                            <button class="btn btn-warning btn-lg me-2" style="background-color: #FFC439; border: none;">
                                <strong>PayPal</strong>
                            </button>
                            <button class="btn btn-dark btn-lg">
                                <i class="bi bi-credit-card me-1"></i><strong>Carte</strong>
                            </button>
                        </div>
                        
                        <div class="paypal-form">
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Email ou mobile" disabled>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" placeholder="Mot de passe" disabled>
                            </div>
                            
                            <p class="text-muted small">
                                Cette interface PayPal est simulée. En production, vous seriez redirigé vers le vrai site PayPal.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('payment.confirm', $payment) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-lg me-3" style="background-color: #FFC439; border: none;">
                            <i class="bi bi-check-circle me-2"></i>Simuler le paiement PayPal
                        </button>
                        
                        <a href="{{ route('booking.payment', $payment->booking) }}" class="btn btn-outline-secondary">
                            Retour
                        </a>
                    </form>

                    <div class="mt-4">
                        <small class="text-muted">
                            Vos données sont sécurisées par le chiffrement SSL PayPal
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection