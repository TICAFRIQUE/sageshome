@extends('backend.pdf.layout')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
    $paymentLabels = ['pending' => 'En attente', 'processing' => 'En cours', 'completed' => 'Payé', 'failed' => 'Échec'];
    $methodLabels = ['cash' => 'Espèces', 'wave' => 'Wave', 'paypal' => 'PayPal', 'bank_transfer' => 'Virement bancaire', 'credit_card' => 'Carte bancaire'];
    $in = \Carbon\Carbon::parse($booking->check_in_date ?? $booking->check_in);
    $out = \Carbon\Carbon::parse($booking->check_out_date ?? $booking->check_out);
    $nights = $booking->nights ?: $in->diffInDays($out);
    $total = $booking->final_amount ?: $booking->total_amount;
    $paid = $booking->payments->where('status', 'completed')->sum('amount');
    $remaining = max($total - $paid, 0);
@endphp

@section('title', 'Reçu de réservation')
@section('subtitle', 'N° ' . $booking->booking_number . ' · ' . $booking->created_at?->format('d/m/Y'))

@section('content')
    <table style="width:100%; border-collapse:collapse; margin-bottom: 4mm">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right: 3mm">
                <div class="box">
                    <div class="kpi-label">Client</div>
                    <div class="bold" style="font-size: 11pt; margin-top: 1mm">{{ trim($booking->first_name . ' ' . $booking->last_name) }}</div>
                    @if ($booking->phone)<div>{{ $booking->phone }}</div>@endif
                    <div class="muted">{{ $booking->email }}</div>
                </div>
            </td>
            <td style="width:50%; vertical-align:top; padding-left: 3mm">
                <div class="box">
                    <div class="kpi-label">Résidence</div>
                    <div class="bold" style="font-size: 11pt; margin-top: 1mm">{{ $booking->residence?->name }}</div>
                    <div>{{ $booking->residence?->address ?: $booking->residence?->location_display }}</div>
                </div>
            </td>
        </tr>
    </table>

    <h2>Séjour</h2>
    <table class="data">
        <thead><tr><th>Arrivée</th><th>Départ</th><th class="right">Nuits</th><th class="right">Voyageurs</th><th>Statut</th></tr></thead>
        <tbody>
            <tr>
                <td>{{ ucfirst($in->translatedFormat('l j F Y')) }}<div class="small muted">dès 15 h</div></td>
                <td>{{ ucfirst($out->translatedFormat('l j F Y')) }}<div class="small muted">avant 11 h</div></td>
                <td class="right">{{ $nights }}</td>
                <td class="right">{{ $booking->guests ?? $booking->guests_count }}</td>
                <td><span class="badge b-{{ $booking->status }}">{{ $statusLabels[$booking->status] ?? $booking->status }}</span></td>
            </tr>
        </tbody>
    </table>

    <h2>Détail du montant</h2>
    <table class="data">
        <thead><tr><th>Désignation</th><th class="right">Prix par nuit (FCFA)</th><th class="right">Quantité</th><th class="right">Total (FCFA)</th></tr></thead>
        <tbody>
            <tr>
                <td>Hébergement — {{ $booking->residence?->name }}</td>
                <td class="right">{{ $fmt($booking->price_per_night) }}</td>
                <td class="right">{{ $nights }} nuit{{ $nights > 1 ? 's' : '' }}</td>
                <td class="right">{{ $fmt($booking->total_price ?: $total) }}</td>
            </tr>
            @if ((float) $booking->tax_amount > 0)
                <tr><td>Taxes et frais</td><td></td><td></td><td class="right">{{ $fmt($booking->tax_amount) }}</td></tr>
            @endif
        </tbody>
        <tfoot>
            <tr><td colspan="3">Total</td><td class="right">{{ $fmt($total) }} FCFA</td></tr>
            <tr><td colspan="3" style="font-weight:normal; background:#fff; border-top:0">Montant réglé</td><td class="right success" style="background:#fff; border-top:0">{{ $fmt($paid) }} FCFA</td></tr>
            <tr><td colspan="3" style="background:#fff; border-top:0">Reste à payer</td><td class="right {{ $remaining > 0 ? 'warning' : 'success' }}" style="background:#fff; border-top:0">{{ $fmt($remaining) }} FCFA</td></tr>
        </tfoot>
    </table>
    <div class="muted small">Équivalent : ≈ {{ number_format(fcfa_to_eur($total), 2, ',', ' ') }} €</div>

    @if ($booking->payments->count())
        <h2>Paiements</h2>
        <table class="data">
            <thead><tr><th>Date</th><th>Moyen</th><th>Statut</th><th class="right">Montant (FCFA)</th></tr></thead>
            <tbody>
                @foreach ($booking->payments as $p)
                    <tr>
                        <td class="nowrap">{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ $methodLabels[$p->payment_method] ?? $p->payment_method }}</td>
                        <td><span class="badge {{ $p->status === 'completed' ? 'b-paid' : ($p->status === 'failed' ? 'b-failed' : 'b-pending') }}">{{ $paymentLabels[$p->status] ?? $p->status }}</span></td>
                        <td class="right">{{ $fmt($p->amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($remaining > 0 && $booking->status !== 'cancelled')
        <p class="small warning" style="margin-top: 3mm">Ce document n'est pas une facture acquittée : le solde de {{ $fmt($remaining) }} FCFA reste à régler.</p>
    @endif

    <table class="signature">
        <tr>
            <td>
                <div class="bold">À présenter à l'arrivée</div>
                <div class="small muted">Pièce d'identité valide. Annulation gratuite jusqu'à 24 h avant l'arrivée.</div>
            </td>
            <td class="right"><div>Cachet Sages Home</div><div class="line" style="margin-left:auto"></div></td>
        </tr>
    </table>
@endsection
