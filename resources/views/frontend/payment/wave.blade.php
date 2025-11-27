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
                    <div class="mb-4">
                        <img src="/images/wave-logo.png" alt="Wave" style="height: 60px;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="bi bi-phone-fill text-primary" style="font-size: 4rem; display: none;"></i>
                    </div>

                    <h4 class="text-green mb-3">Montant à payer</h4>
                    <div class="display-4 text-gold mb-4">{{ number_format($payment->amount, 0) }} FCFA</div>
                    
                    <p class="text-muted mb-4">
                        Réservation #{{ $payment->booking->booking_number }}<br>
                        {{ $payment->booking->residence->name }}
                    </p>

                    <!-- Simulation d'interface Wave -->
                    <div class="wave-payment-simulation p-4 bg-light rounded mb-4">
                        <h6 class="mb-3">Instructions de paiement Wave:</h6>
                        <ol class="text-start">
                            <li>Composez *145# sur votre téléphone</li>
                            <li>Sélectionnez "Payer une facture"</li>
                            <li>Entrez le code marchand: <strong>SAGES001</strong></li>
                            <li>Entrez le montant: <strong>{{ number_format($payment->amount, 0) }} FCFA</strong></li>
                            <li>Confirmez avec votre code PIN</li>
                        </ol>
                        
                        <div class="alert alert-info mt-3">
                            <strong>Référence:</strong> {{ $payment->payment_reference }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('payment.confirm', $payment) }}">
                        @csrf
                        <button type="submit" class="btn btn-green btn-lg me-3">
                            <i class="bi bi-check-circle me-2"></i>J'ai effectué le paiement
                        </button>
                        
                        <a href="{{ route('booking.payment', $payment->booking) }}" class="btn btn-outline-secondary">
                            Retour
                        </a>
                    </form>

                    <div class="mt-4">
                        <small class="text-muted">
                            Vous avez des difficultés ? <a href="tel:+221123456789">Appelez-nous</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-check payment status -->
<script>
let checkCount = 0;
const maxChecks = 30; // Check for 5 minutes (every 10 seconds)

function checkPaymentStatus() {
    if (checkCount >= maxChecks) {
        return; // Stop checking after 5 minutes
    }
    
    fetch('/api/payment/{{ $payment->id }}/status', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'completed') {
            window.location.href = "{{ route('booking.confirmation', $payment->booking) }}";
        }
    })
    .catch(error => console.error('Error checking payment status:', error));
    
    checkCount++;
}

// Check payment status every 10 seconds
setInterval(checkPaymentStatus, 10000);
</script>
@endsection