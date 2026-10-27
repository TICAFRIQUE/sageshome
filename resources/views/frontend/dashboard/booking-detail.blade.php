@extends('layouts.app')

@section('title', 'Réservation ' . $booking->booking_number . ' | Sages Home')

@php
    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
    $paymentLabels = ['pending' => 'En attente', 'processing' => 'En cours', 'completed' => 'Payé', 'failed' => 'Échec'];
    $in = \Carbon\Carbon::parse($booking->check_in_date ?? $booking->check_in);
    $out = \Carbon\Carbon::parse($booking->check_out_date ?? $booking->check_out);
    $total = $booking->final_amount ?: $booking->total_amount;
    $paid = $booking->payment_status === 'paid';
    $canCancel = in_array($booking->status, ['pending', 'confirmed']) && $in->isFuture();
    $banner = match ($booking->status) {
        'confirmed', 'completed' => ['is-success', 'fa-circle-check', 'Réservation ' . strtolower($statusLabels[$booking->status])],
        'cancelled' => ['is-pending', 'fa-circle-xmark', 'Réservation annulée'],
        default => ['is-pending', 'fa-clock', 'Réservation en attente de confirmation'],
    };
@endphp

@section('content')
    <div class="container booking-page">
        <nav aria-label="Fil d'Ariane">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-house"></i><span class="visually-hidden">Accueil</span></a></li>
                <li class="breadcrumb-item"><a href="{{ route('dashboard.bookings') }}">Mes réservations</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $booking->booking_number }}</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h1>{{ $booking->residence?->name }}</h1>
                <p class="page-sub mb-0">Réservation {{ $booking->booking_number }} · faite le {{ $booking->created_at->translatedFormat('j F Y') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2 no-print">
                <a href="{{ route('dashboard.booking.receipt', $booking) }}" target="_blank" rel="noopener" class="btn btn-primary">
                    <i class="fas fa-file-pdf me-2"></i>Télécharger / imprimer le reçu
                </a>
            </div>
        </div>

        <div class="status-banner {{ $banner[0] }}" role="status">
            <i class="fas {{ $banner[1] }}"></i>
            <div><strong>{{ $banner[2] }}</strong>
                @if ($booking->status === 'pending' && !$paid)
                    Réglez votre séjour pour le confirmer.
                @elseif ($canCancel)
                    Annulation gratuite jusqu'à 24 h avant l'arrivée.
                @endif
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7 col-xl-8">
                <section class="panel">
                    <h2>Votre séjour</h2>
                    <dl class="info-grid">
                        <div><dt>Arrivée</dt><dd>{{ ucfirst($in->translatedFormat('l j F Y')) }} <span class="text-muted">· dès 15 h</span></dd></div>
                        <div><dt>Départ</dt><dd>{{ ucfirst($out->translatedFormat('l j F Y')) }} <span class="text-muted">· avant 11 h</span></dd></div>
                        <div><dt>Voyageurs</dt><dd>{{ $booking->guests }}</dd></div>
                        <div><dt>Durée</dt><dd>{{ $booking->nights }} {{ $booking->nights > 1 ? 'nuits' : 'nuit' }}</dd></div>
                        <div><dt>Adresse</dt><dd>{{ $booking->residence?->address ?: $booking->residence?->location_display }}</dd></div>
                    </dl>
                    <div class="d-flex flex-wrap gap-2 mt-3 no-print">
                        @if ($booking->residence?->hasGoogleMaps())
                            <a href="{{ $booking->residence->google_maps_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="fas fa-map-location-dot me-1"></i>Itinéraire</a>
                        @endif
                        @if ($booking->residence)
                            <a href="{{ route('residences.show', $booking->residence->slug) }}" class="btn btn-sm btn-outline-primary">Voir la résidence</a>
                        @endif
                    </div>
                </section>

                @if ($booking->special_requests)
                    <section class="panel"><h2>Vos demandes</h2><p class="mb-0 text-muted">{{ $booking->special_requests }}</p></section>
                @endif

                <section class="panel">
                    <h2>Paiements</h2>
                    @if ($booking->payments->count())
                        <div class="table-responsive">
                            <table class="table table-sm small align-middle mb-0">
                                <thead class="text-muted"><tr><th class="fw-medium">Date</th><th class="fw-medium">Mode</th><th class="fw-medium">Statut</th><th class="fw-medium text-end">Montant</th></tr></thead>
                                <tbody>
                                    @foreach ($booking->payments as $p)
                                        <tr>
                                            <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $p->method_display }}</td>
                                            <td><span class="badge rounded-pill {{ $p->status === 'completed' ? 'text-bg-success' : ($p->status === 'failed' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ $paymentLabels[$p->status] ?? $p->status }}</span></td>
                                            <td class="text-end">{{ number_format($p->amount, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-2">Aucun paiement enregistré.</p>
                    @endif
                    @if ($booking->status === 'pending' && !$paid)
                        <a href="{{ route('booking.payment', $booking) }}" class="btn btn-primary mt-3 no-print">Payer maintenant</a>
                    @endif
                </section>

                <div class="d-flex flex-wrap gap-2 no-print">
                    <a href="{{ route('dashboard.bookings') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Mes réservations</a>
                    @if ($canCancel)
                        <form action="{{ route('dashboard.booking.cancel', $booking) }}" method="POST"
                            onsubmit="return confirm('Annuler cette réservation ?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-link text-danger">Annuler la réservation</button>
                        </form>
                    @endif
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
                    'total' => $total,
                    'reference' => $booking->booking_number,
                ])
            </div>
        </div>
    </div>
@endsection
