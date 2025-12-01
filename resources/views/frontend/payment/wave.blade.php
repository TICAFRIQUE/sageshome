@extends('layouts.app')

@section('title', 'Paiement Wave - ' . $payment->booking->residence->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="text-green mb-0">Paiement Wave</h3>
                </div>
                
                <div class="card-body text-center">
                    @if(request()->get('error') === 'wave_payment_failed')
                        <div class="alert alert-danger mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Le paiement Wave a échoué. Veuillez réessayer.
                        </div>
                    @endif

                    <div class="mb-4">
                        <i class="fas fa-mobile-alt text-primary" style="font-size: 4rem;"></i>
                    </div>

                    <h4 class="text-green mb-3">Montant à payer</h4>
                    <div class="display-4 text-gold mb-4">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</div>
                    
                    <p class="text-muted mb-4">
                        Réservation #{{ $payment->booking->booking_number }}<br>
                        {{ $payment->booking->residence->name }}
                    </p>

                    <!-- Redirection automatique vers Wave -->
                    <div class="wave-payment-info p-4 bg-light rounded mb-4">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <h6 class="mb-3">Redirection vers Wave en cours...</h6>
                        <p class="text-muted">
                            Si vous n'êtes pas automatiquement redirigé, cliquez sur le bouton ci-dessous.
                        </p>
                    </div>

                    <!-- Bouton de redirection manuelle -->
                    <a href="{{ route('booking.process-payment', $payment->booking) }}" 
                       class="btn btn-primary btn-lg me-3"
                       onclick="this.innerHTML='<i class=&quot;fas fa-spinner fa-spin me-2&quot;></i>Redirection...'; this.disabled=true;">
                        <i class="fas fa-external-link-alt me-2"></i>
                        Payer avec Wave
                    </a>
                    
                    <a href="{{ route('booking.payment', $payment->booking) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour
                    </a>

                    <div class="mt-4">
                        <small class="text-muted">
                            Vous rencontrez des difficultés ? 
                            <a href="tel:+221123456789">Contactez notre support</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto redirection après 3 secondes -->
<script>
setTimeout(function() {
    if (!window.location.href.includes('error=')) {
        // Redirection automatique vers la page de traitement du paiement
        window.location.href = "{{ route('booking.process-payment', $payment->booking) }}";
    }
}, 3000);
</script>
@endsection