@extends('layouts.app')

@section('title', 'Réservation - ' . $residence->name . ' | Sages Home')

@section('content')
    <div class="container booking-page">
        @include('frontend.booking.partials.steps', ['current' => 1])

        <h1>Vérifiez votre réservation</h1>
        <p class="page-sub">Contrôlez vos informations puis passez au paiement.</p>

        <div class="row g-4">
            <div class="col-lg-7 col-xl-8">
                <form method="POST" action="{{ route('booking.store') }}" id="bookingForm" novalidate>
                    @csrf
                    <input type="hidden" name="residence_id" value="{{ $residence->id }}">
                    <input type="hidden" name="check_in" value="{{ $checkIn }}">
                    <input type="hidden" name="check_out" value="{{ $checkOut }}">
                    <input type="hidden" name="guests" value="{{ $guests }}">

                    <section class="panel">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h2 class="mb-0">Voyageur principal</h2>
                            <a href="{{ route('dashboard.profile') }}" class="small">Modifier mon profil</a>
                        </div>
                        <dl class="info-grid">
                            <div><dt>Nom</dt><dd>{{ Auth::user()->username }}</dd></div>
                            <div><dt>E-mail</dt><dd>{{ Auth::user()->email }}</dd></div>
                            <div><dt>Téléphone</dt><dd>{{ Auth::user()->phone ?: '—' }}</dd></div>
                        </dl>
                    </section>

                    <section class="panel">
                        <h2><label for="special_requests" class="mb-0">Demandes particulières <span class="text-muted fw-normal small">(facultatif)</span></label></h2>
                        <textarea name="special_requests" id="special_requests" class="form-control" rows="3" maxlength="1000"
                            placeholder="Heure d'arrivée, lit bébé, besoin particulier…">{{ old('special_requests') }}</textarea>
                        @error('special_requests')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </section>

                    <section class="panel">
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                J'accepte les conditions générales et la politique de confidentialité.
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="cancellation" required>
                            <label class="form-check-label" for="cancellation">
                                J'ai pris connaissance de la politique d'annulation :
                                <span class="text-muted">annulation gratuite jusqu'à 24 h avant l'arrivée, ensuite la première nuit est facturée.</span>
                            </label>
                        </div>
                        <div class="text-danger small mt-2" id="termsError" hidden>Veuillez cocher les deux cases pour continuer.</div>
                    </section>

                    <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2">
                        <a href="{{ route('residences.show', $residence->slug) }}?check_in={{ $checkIn }}&check_out={{ $checkOut }}&guests={{ $guests }}"
                            class="btn btn-outline-primary" id="backBtn">
                            <i class="fas fa-arrow-left me-2"></i>Modifier les dates
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                            Continuer vers le paiement<i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-5 col-xl-4">
                @include('frontend.booking.partials.summary', [
                    'residence' => $residence,
                    'checkIn' => $checkIn,
                    'checkOut' => $checkOut,
                    'guests' => $guests,
                    'nights' => $priceInfo['nights'],
                    'pricePerNight' => $priceInfo['price_per_night'],
                    'subtotal' => $priceInfo['total_price'],
                    'tax' => $taxAmount,
                    'total' => $finalAmount,
                ])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('bookingForm');
            var terms = document.getElementById('terms');
            var cancellation = document.getElementById('cancellation');
            var error = document.getElementById('termsError');
            var btn = document.getElementById('submitBtn');

            [terms, cancellation].forEach(function (c) {
                c.addEventListener('change', function () { if (terms.checked && cancellation.checked) error.hidden = true; });
            });

            form.addEventListener('submit', function (e) {
                if (!terms.checked || !cancellation.checked) {
                    e.preventDefault();
                    error.hidden = false;
                    (terms.checked ? cancellation : terms).focus();
                    return;
                }
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement…';
            });

            // Retour arrière : réactiver le bouton
            window.addEventListener('pageshow', function () {
                btn.disabled = false;
                btn.innerHTML = 'Continuer vers le paiement<i class="fas fa-arrow-right ms-2"></i>';
            });
        });
    </script>
@endpush
