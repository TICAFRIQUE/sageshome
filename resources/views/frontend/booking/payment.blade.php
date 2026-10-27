@extends('layouts.app')

@section('title', 'Paiement - Réservation ' . $booking->booking_number . ' | Sages Home')

@php
    $lastPayment = $booking->payments->last();
    $methods = [
        'wave' => ['Wave', 'Paiement mobile, confirmation immédiate', 'fas fa-mobile-alt',
            'Vous allez être redirigé vers Wave pour valider le paiement. Cette page se mettra à jour automatiquement.'],
        'paypal' => ['PayPal', 'Compte PayPal ou carte bancaire', 'fab fa-paypal',
            'Vous allez être redirigé vers PayPal. Vous pouvez payer avec votre compte ou une carte bancaire.'],
        'cash' => ['Espèces', "Paiement sur place à l'arrivée", 'fas fa-money-bill-wave',
            "Votre réservation est enregistrée et vous réglez à votre arrivée. Prévoyez le montant exact."],
    ];
@endphp

@section('content')
    <div class="container booking-page">
        @include('frontend.booking.partials.steps', ['current' => 2])

        <h1>Paiement</h1>
        <p class="page-sub">Réservation <strong>{{ $booking->booking_number }}</strong> · choisissez votre mode de paiement.</p>

        <div class="row g-4">
            <div class="col-lg-7 col-xl-8">
                <div id="pollingZone"></div>

                <form method="POST" action="{{ route('booking.process-payment', $booking) }}" id="paymentForm" class="panel">
                    @csrf
                    <h2>Mode de paiement</h2>

                    <div class="pay-options" role="radiogroup" aria-label="Mode de paiement">
                        @foreach ($methods as $value => [$name, $desc, $icon])
                            <label class="pay-option">
                                <input type="radio" name="payment_method" value="{{ $value }}" id="{{ $value }}" required>
                                <span class="pay-box">
                                    <span class="pay-icon"><i class="{{ $icon }}"></i></span>
                                    <span>
                                        <span class="pay-name d-block">{{ $name }}</span>
                                        <span class="pay-desc d-block">{{ $desc }}</span>
                                    </span>
                                    <span class="pay-radio" aria-hidden="true"></span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div id="paymentInfo" class="pay-note" hidden>
                        @foreach ($methods as $value => $m)
                            <p id="{{ $value }}Info" class="payment-info-content mb-0" hidden>
                                <i class="fas fa-info-circle me-1 text-muted"></i>{{ $m[3] }}
                            </p>
                        @endforeach
                    </div>

                    <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-3 pt-3 border-top">
                        <a href="{{ route('residences.show', $booking->residence->slug) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la résidence
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="paymentBtn" disabled>
                            <span id="paymentBtnText">Choisissez un mode de paiement</span>
                        </button>
                    </div>
                </form>

                <ul class="check-list panel">
                    <li><i class="fas fa-lock"></i>Paiement sécurisé : vos données bancaires ne transitent pas par notre site.</li>
                    <li><i class="fas fa-envelope"></i>Un récapitulatif vous est envoyé par e-mail.</li>
                    <li><i class="fas fa-headset"></i>Une question ? Notre équipe répond 24h/24.</li>
                </ul>
            </div>

            <div class="col-lg-5 col-xl-4">
                @include('frontend.booking.partials.summary', [
                    'residence' => $booking->residence,
                    'checkIn' => $booking->check_in ?? $booking->check_in_date,
                    'checkOut' => $booking->check_out ?? $booking->check_out_date,
                    'guests' => $booking->guests,
                    'nights' => $booking->nights,
                    'pricePerNight' => $booking->price_per_night,
                    'subtotal' => $booking->total_price,
                    'tax' => $booking->tax_amount,
                    'total' => $booking->final_amount,
                    'reference' => $booking->booking_number,
                ])
                @if ($booking->special_requests)
                    <div class="panel mt-3 small">
                        <div class="fw-semibold mb-1">Vos demandes</div>
                        <div class="text-muted">{{ $booking->special_requests }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            var paymentBtn = document.getElementById('paymentBtn');
            var paymentBtnText = document.getElementById('paymentBtnText');
            var paymentInfo = document.getElementById('paymentInfo');
            var paymentForm = document.getElementById('paymentForm');

            var labels = { wave: 'Payer avec Wave', paypal: 'Payer avec PayPal', cash: 'Confirmer la réservation' };
            var loading = { wave: 'Redirection vers Wave…', paypal: 'Redirection vers PayPal…', cash: 'Confirmation en cours…' };

            paymentMethods.forEach(function (input) {
                input.addEventListener('change', function () {
                    document.querySelectorAll('.payment-info-content').forEach(function (p) { p.hidden = true; });
                    var info = document.getElementById(this.value + 'Info');
                    if (info) { info.hidden = false; paymentInfo.hidden = false; }
                    paymentBtn.disabled = false;
                    paymentBtnText.textContent = labels[this.value] || 'Continuer';
                });
            });

            paymentForm.addEventListener('submit', function (e) {
                var selected = document.querySelector('input[name="payment_method"]:checked');
                if (!selected) {
                    e.preventDefault();
                    return;
                }
                paymentBtn.disabled = true;
                paymentBtnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + (loading[selected.value] || 'Traitement…');
                if (selected.value === 'wave') {
                    setTimeout(startPaymentPolling, 5000);
                }
            });

            window.addEventListener('pageshow', function (e) {
                if (!e.persisted) return;
                var selected = document.querySelector('input[name="payment_method"]:checked');
                paymentBtn.disabled = !selected;
                paymentBtnText.textContent = selected ? labels[selected.value] : 'Choisissez un mode de paiement';
            });

            // ---------- Vérification automatique d'un paiement Wave ----------
            var pollingInterval = null;
            var pollingAttempts = 0;
            var maxPollingAttempts = 60; // 60 × 3 s = 3 minutes

            @if ($lastPayment && $lastPayment->payment_method === 'wave' && $lastPayment->status === 'pending')
                startPaymentPolling();
            @endif

            function startPaymentPolling() {
                var zone = document.getElementById('pollingZone');
                if (zone && !document.getElementById('pollingAlert')) {
                    zone.innerHTML =
                        '<div class="status-banner is-pending" id="pollingAlert" role="status">' +
                        '<i class="fas fa-sync fa-spin"></i>' +
                        '<div><strong>Vérification du paiement…</strong>Si vous avez payé sur votre téléphone, cette page se met à jour toute seule.</div>' +
                        '</div>';
                }
                if (!pollingInterval) pollingInterval = setInterval(checkPaymentStatus, 3000);
            }

            function checkPaymentStatus() {
                pollingAttempts++;
                if (pollingAttempts >= maxPollingAttempts) {
                    stopPaymentPolling();
                    var alert = document.getElementById('pollingAlert');
                    if (alert) {
                        alert.innerHTML = '<i class="fas fa-clock"></i><div><strong>Vérification interrompue</strong>Si vous avez payé, actualisez la page ou contactez-nous.</div>';
                    }
                    return;
                }
                @if ($lastPayment)
                    fetch('/api/payment/{{ $lastPayment->id }}/status', { headers: { 'Accept': 'application/json' } })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data.status === 'completed' && data.booking_status === 'confirmed') {
                                stopPaymentPolling();
                                window.location.href = @json(route('booking.confirmation', $booking));
                            }
                        })
                        .catch(function () {});
                @endif
            }

            function stopPaymentPolling() {
                if (pollingInterval) { clearInterval(pollingInterval); pollingInterval = null; }
            }
            window.addEventListener('beforeunload', stopPaymentPolling);
        });
    </script>
@endpush
