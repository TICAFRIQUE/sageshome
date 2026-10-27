@extends('layouts.app')

@section('title', 'Mes réservations | Sages Home')

@php
    $tabs = ['all' => 'Toutes', 'pending' => 'En attente', 'confirmed' => 'Confirmées', 'completed' => 'Terminées', 'cancelled' => 'Annulées'];
    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
    $current = request('status', 'all');
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
@endphp

@section('content')
    <div class="container booking-page">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
            <div>
                <h1>Mes réservations</h1>
                <p class="page-sub mb-0">Retrouvez vos séjours, vos paiements et vos reçus.</p>
            </div>
            <a href="{{ route('residences.index') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nouvelle réservation</a>
        </div>

        <nav class="type-chips mb-3" aria-label="Filtrer par statut" style="display:flex;gap:.5rem;overflow-x:auto">
            @foreach ($tabs as $key => $label)
                @if ($key === 'all' || $statusCounts[$key] > 0 || $current === $key)
                    <a href="{{ route('dashboard.bookings', $key === 'all' ? [] : ['status' => $key]) }}"
                        class="my-tab {{ $current === $key ? 'active' : '' }}">{{ $label }} <span>{{ $statusCounts[$key] }}</span></a>
                @endif
            @endforeach
        </nav>

        @forelse ($bookings as $booking)
            @php
                $in = \Carbon\Carbon::parse($booking->check_in_date ?? $booking->check_in);
                $out = \Carbon\Carbon::parse($booking->check_out_date ?? $booking->check_out);
                $total = $booking->final_amount ?: $booking->total_amount;
                $paid = $booking->payment_status === 'paid';
                $canCancel = in_array($booking->status, ['pending', 'confirmed']) && $in->isFuture();
                $upcoming = in_array($booking->status, ['pending', 'confirmed']) && $in->isFuture();
                $daysLeft = (int) today()->diffInDays($in, false);
            @endphp
            <article class="my-booking">
                <a href="{{ route('dashboard.booking.show', $booking) }}" class="my-booking-img" tabindex="-1" aria-hidden="true">
                    @if ($booking->residence?->primaryImage)
                        <img src="{{ $booking->residence->primaryImage->full_url }}" alt="" loading="lazy" width="160" height="120">
                    @else
                        <span class="rc-placeholder"><img src="{{ asset('images/logo-mark.webp') }}" alt="" width="40" height="40"></span>
                    @endif
                </a>
                <div class="my-booking-main">
                    <div class="d-flex flex-wrap justify-content-between gap-2">
                        <div>
                            <a href="{{ route('dashboard.booking.show', $booking) }}" class="my-booking-title">{{ $booking->residence?->name ?? 'Résidence' }}</a>
                            <div class="small text-muted">Réf. {{ $booking->booking_number }}</div>
                        </div>
                        <div>
                            <span class="status-pill s-{{ $booking->status }}">{{ $statusLabels[$booking->status] ?? $booking->status }}</span>
                        </div>
                    </div>

                    <dl class="my-booking-facts">
                        <div><dt>Séjour</dt><dd>{{ $in->translatedFormat('j M') }} → {{ $out->translatedFormat('j M Y') }}</dd></div>
                        <div><dt>Durée</dt><dd>{{ $booking->nights }} nuit{{ $booking->nights > 1 ? 's' : '' }} · {{ $booking->guests }} voyageur{{ $booking->guests > 1 ? 's' : '' }}</dd></div>
                        <div><dt>Total</dt><dd>{{ $fmt($total) }} FCFA</dd></div>
                        <div><dt>Paiement</dt><dd class="{{ $paid ? 'text-success' : 'text-warning' }}">{{ $paid ? 'Payé' : ($booking->payment_status === 'partial' ? 'Partiel' : 'À régler') }}</dd></div>
                    </dl>

                    @if ($upcoming && $daysLeft >= 0 && $daysLeft <= 14)
                        <div class="small mb-2"><i class="fas fa-hourglass-half text-gold me-1"></i>{{ $daysLeft === 0 ? "Arrivée aujourd'hui" : ($daysLeft === 1 ? 'Arrivée demain' : "Arrivée dans $daysLeft jours") }}</div>
                    @endif

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.booking.show', $booking) }}" class="btn btn-sm btn-primary">Détails</a>
                        <a href="{{ route('dashboard.booking.receipt', $booking) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-pdf me-1"></i>Reçu
                        </a>
                        @if ($booking->status === 'pending' && !$paid)
                            <a href="{{ route('booking.payment', $booking) }}" class="btn btn-sm btn-outline-primary">Payer</a>
                        @endif
                        @if ($canCancel)
                            <form action="{{ route('dashboard.booking.cancel', $booking) }}" method="POST" class="ms-auto"
                                onsubmit="return confirm('Annuler cette réservation ? Annulation gratuite jusqu\'à 24 h avant l\'arrivée.')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-link text-danger">Annuler</button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-calendar-xmark"></i></div>
                <h2 class="h5">{{ $current === 'all' ? "Vous n'avez pas encore de réservation" : 'Aucune réservation dans cette catégorie' }}</h2>
                <p class="text-muted mb-3">Découvrez nos résidences et réservez en quelques clics.</p>
                <a href="{{ route('residences.index') }}" class="btn btn-primary">Voir les résidences</a>
            </div>
        @endforelse

        @if ($bookings->hasPages())
            <div class="mt-4 d-flex justify-content-center">{{ $bookings->links() }}</div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .my-tab { flex: 0 0 auto; display: inline-flex; gap: .45rem; align-items: center; padding: .45rem .9rem; border-radius: 999px; border: 1px solid var(--sage-border); background: #fff; color: var(--sage-text); font-size: .88rem; font-weight: 500; text-decoration: none; }
        .my-tab span { min-width: 20px; height: 20px; padding: 0 5px; border-radius: 999px; background: var(--sage-bg-light); font-size: .72rem; display: inline-flex; align-items: center; justify-content: center; }
        .my-tab.active { background: var(--sage-green); color: #fff; border-color: var(--sage-green); }
        .my-tab.active span { background: var(--sage-gold-start); color: var(--sage-green-dark); }
        .my-booking { display: flex; gap: 1rem; padding: .9rem; margin-bottom: .85rem; background: #fff; border: 1px solid var(--sage-border); border-radius: var(--radius); transition: box-shadow .2s var(--ease); }
        .my-booking:hover { box-shadow: var(--shadow); }
        .my-booking-img { flex: 0 0 150px; height: 112px; border-radius: 10px; overflow: hidden; background: var(--sage-bg-light); }
        .my-booking-img img { width: 100%; height: 100%; object-fit: cover; }
        .my-booking-img .rc-placeholder img { width: 40px !important; height: 40px !important; }
        .my-booking-main { flex: 1; min-width: 0; }
        .my-booking-title { font-weight: 600; color: var(--sage-green-dark); text-decoration: none; font-size: 1rem; }
        .my-booking-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: .4rem 1rem; margin: .6rem 0; }
        .my-booking-facts dt { font-size: .72rem; font-weight: 500; color: var(--sage-text-secondary); }
        .my-booking-facts dd { margin: 0; font-size: .88rem; font-weight: 500; }
        .status-pill { display: inline-block; padding: .25rem .65rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
        .s-pending { background: #FFF1D6; color: #8A5A00; } .s-confirmed { background: #DFF1E4; color: #1E6B34; }
        .s-completed { background: #E2EDF7; color: #1F5F99; } .s-cancelled { background: #FBE3E3; color: #9E1E1E; }
        @media (max-width: 575.98px) { .my-booking { flex-direction: column; } .my-booking-img { flex-basis: auto; width: 100%; height: 150px; } }
    </style>
@endpush
