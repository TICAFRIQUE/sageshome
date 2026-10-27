@extends('layouts.app')

@section('title', 'Réservation ' . $booking->booking_number . ' | Sages Home')

@php
    $confirmed = in_array($booking->status, ['confirmed', 'completed']);
    $paid = $booking->payment_status === 'paid';
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $phone = $parametre?->contact1 ?: $parametre?->contact2;
    $in = \Carbon\Carbon::parse($booking->check_in ?? $booking->check_in_date);
    $out = \Carbon\Carbon::parse($booking->check_out ?? $booking->check_out_date);
@endphp

@section('content')
    <div class="container booking-page">
        @include('frontend.booking.partials.steps', ['current' => 3])

        <div class="status-banner {{ $confirmed ? 'is-success' : 'is-pending' }}" role="status">
            <i class="fas {{ $confirmed ? 'fa-circle-check' : 'fa-clock' }}"></i>
            <div>
                <strong>{{ $confirmed ? 'Réservation confirmée' : 'Réservation enregistrée' }}</strong>
                Référence <b>{{ $booking->booking_number }}</b>.
                @if ($confirmed)
                    Un e-mail de confirmation a été envoyé à {{ Auth::user()->email }}.
                @elseif (!$paid)
                    Elle sera confirmée à réception du paiement. Un récapitulatif a été envoyé à {{ Auth::user()->email }}.
                @endif
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7 col-xl-8">
                <section class="panel">
                    <h2>Votre séjour</h2>
                    <dl class="info-grid">
                        <div><dt>Résidence</dt><dd>{{ $booking->residence->name }}</dd></div>
                        <div><dt>Adresse</dt><dd>{{ $booking->residence->address ?: $booking->residence->location_display }}</dd></div>
                        <div><dt>Arrivée</dt><dd>{{ ucfirst($in->translatedFormat('l j F Y')) }} <span class="text-muted">· dès 15 h</span></dd></div>
                        <div><dt>Départ</dt><dd>{{ ucfirst($out->translatedFormat('l j F Y')) }} <span class="text-muted">· avant 11 h</span></dd></div>
                        <div><dt>Voyageurs</dt><dd>{{ $booking->guests }}</dd></div>
                        <div><dt>Durée</dt><dd>{{ $booking->nights }} {{ $booking->nights > 1 ? 'nuits' : 'nuit' }}</dd></div>
                    </dl>
                    @if ($booking->residence->hasGoogleMaps())
                        <a href="{{ $booking->residence->google_maps_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mt-3 no-print">
                            <i class="fas fa-map-location-dot me-1"></i>Itinéraire
                        </a>
                    @endif
                </section>

                <section class="panel">
                    <h2>Paiement</h2>
                    <p class="mb-2">
                        <span class="fw-semibold">{{ $booking->payment_status_display }}</span>
                        <span class="text-muted">
                            —
                            @if ($paid)
                                votre paiement a bien été reçu.
                            @elseif ($booking->payment_status === 'partial')
                                le solde reste à régler.
                            @else
                                à régler {{ optional($booking->payments->last())->payment_method === 'cash' ? 'à votre arrivée' : 'pour finaliser la réservation' }}.
                            @endif
                        </span>
                    </p>
                    @if ($booking->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 small align-middle">
                                <thead class="text-muted">
                                    <tr><th class="fw-medium">Mode</th><th class="fw-medium">Date</th><th class="fw-medium">Statut</th><th class="fw-medium text-end">Montant</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($booking->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->method_display }}</td>
                                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge rounded-pill {{ $payment->status === 'completed' ? 'text-bg-success' : 'text-bg-warning' }}">{{ $payment->status_display }}</span></td>
                                            <td class="text-end">{{ $fmt($payment->amount) }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <section class="panel">
                    <h2>À savoir avant d'arriver</h2>
                    <ul class="check-list">
                        <li><i class="fas fa-id-card"></i>Munissez-vous d'une pièce d'identité valide.</li>
                        <li><i class="fas fa-clock"></i>Arrivée à partir de 15 h, départ avant 11 h.</li>
                        <li><i class="fas fa-calendar-xmark"></i>Annulation gratuite jusqu'à 24 h avant l'arrivée.</li>
                        @if ($phone)
                            <li><i class="fas fa-phone"></i>Besoin d'aide ? Appelez-nous au <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}">{{ $phone }}</a>@if ($parametre?->email1) ou écrivez à <a href="mailto:{{ $parametre->email1 }}">{{ $parametre->email1 }}</a>@endif.</li>
                        @endif
                    </ul>
                </section>

                <div class="d-flex flex-wrap gap-2 no-print">
                    <a href="{{ route('dashboard.bookings') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>Mes réservations</a>
                    <a href="{{ route('dashboard.booking.receipt', $booking) }}" target="_blank" rel="noopener" class="btn btn-outline-primary"><i class="fas fa-file-pdf me-2"></i>Reçu (PDF / imprimer)</a>
                    <a href="{{ route('home') }}" class="btn btn-link">Retour à l'accueil</a>
                </div>
            </div>

            <div class="col-lg-5 col-xl-4">
                @include('frontend.booking.partials.summary', [
                    'residence' => $booking->residence,
                    'checkIn' => $in,
                    'checkOut' => $out,
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
