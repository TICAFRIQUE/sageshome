@extends('backend.pdf.layout')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $dec = fn ($n) => number_format((float) $n, 1, ',', ' ');
    $start = \Carbon\Carbon::parse($filters['start_date']);
    $end = \Carbon\Carbon::parse($filters['end_date']);
    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
    $methodLabels = ['cash' => 'Espèces', 'wave' => 'Wave', 'paypal' => 'PayPal', 'bank_transfer' => 'Virement bancaire', 'credit_card' => 'Carte bancaire'];
    $totalPayments = max($paymentMethodStats->sum('total'), 1);
    $collectRate = $stats['booked_revenue'] > 0 ? min(100, round($stats['total_revenue'] / $stats['booked_revenue'] * 100)) : 0;
@endphp

@section('title', "Compte d'exploitation")
@section('subtitle')
    Du {{ $start->format('d/m/Y') }} au {{ $end->format('d/m/Y') }}
    @if ($residenceName) · {{ $residenceName }} @endif
    @if ($filters['status']) · {{ $statusLabels[$filters['status']] ?? $filters['status'] }} @endif
@endsection

@section('content')
    <h2>Synthèse de la période</h2>
    <table class="kpis">
        <tr>
            <td class="featured">
                <div class="kpi-label">Encaissé</div>
                <div class="kpi-value">{{ $fmt($stats['total_revenue']) }} <small>FCFA</small></div>
                <div class="kpi-meta">{{ $collectRate }} % du CA réservé</div>
            </td>
            <td>
                <div class="kpi-label">Chiffre d'affaires réservé</div>
                <div class="kpi-value">{{ $fmt($stats['booked_revenue']) }} <small>FCFA</small></div>
                <div class="kpi-meta">Reste à encaisser : {{ $fmt($stats['remaining']) }} FCFA</div>
            </td>
            <td>
                <div class="kpi-label">Taux d'occupation</div>
                <div class="kpi-value">{{ $dec($stats['occupancy']) }} <small>%</small></div>
                <div class="kpi-meta">{{ $stats['total_nights'] }} nuitées / {{ $stats['residence_count'] * $stats['days'] }} disponibles</div>
            </td>
            <td>
                <div class="kpi-label">Prix moyen par nuit</div>
                <div class="kpi-value">{{ $fmt($stats['adr']) }} <small>FCFA</small></div>
                <div class="kpi-meta">Séjour moyen : {{ $dec($stats['avg_stay']) }} nuit(s)</div>
            </td>
        </tr>
    </table>

    <table class="data" style="margin-top: 2mm">
        <thead>
            <tr>
                <th>Réservations</th>
                <th class="right">Confirmées / terminées</th>
                <th class="right">En attente</th>
                <th class="right">Annulées</th>
                <th class="right">Taux d'annulation</th>
                <th class="right">Période</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="bold">{{ $stats['total_bookings'] }}</td>
                <td class="right">{{ $stats['confirmed_bookings'] }}</td>
                <td class="right">{{ $stats['pending_bookings'] }}</td>
                <td class="right">{{ $stats['cancelled_bookings'] }}</td>
                <td class="right">{{ $dec($stats['cancellation_rate']) }} %</td>
                <td class="right">{{ $stats['days'] }} jours</td>
            </tr>
        </tbody>
    </table>

    <h2>Performance par résidence</h2>
    @if ($residenceStats->count())
        <table class="data">
            <thead>
                <tr>
                    <th>Résidence</th>
                    <th class="right">Réserv.</th>
                    <th class="right">Annul.</th>
                    <th class="right">Nuitées</th>
                    <th style="width: 34mm">Occupation</th>
                    <th class="right">CA réservé (FCFA)</th>
                    <th class="right">Encaissé (FCFA)</th>
                    <th class="right">Reste (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($residenceStats as $s)
                    <tr>
                        <td class="bold">{{ $s['residence']?->name ?? 'Résidence supprimée' }}</td>
                        <td class="right">{{ $s['total_bookings'] }}</td>
                        <td class="right">{{ $s['cancelled_bookings'] }}</td>
                        <td class="right">{{ $s['total_nights'] }}</td>
                        <td>
                            <table style="width:100%; border-collapse:collapse"><tr>
                                <td style="padding:0; border:0; background:none"><div class="bar"><span style="width: {{ $s['occupancy'] }}%"></span></div></td>
                                <td style="padding:0 0 0 1.5mm; border:0; background:none; width: 11mm" class="right small">{{ $dec($s['occupancy']) }} %</td>
                            </tr></table>
                        </td>
                        <td class="right nowrap">{{ $fmt($s['booked_revenue']) }}</td>
                        <td class="right nowrap bold">{{ $fmt($s['total_revenue']) }}</td>
                        <td class="right nowrap {{ $s['booked_revenue'] - $s['total_revenue'] > 0 ? 'warning' : 'muted' }}">{{ $fmt(max($s['booked_revenue'] - $s['total_revenue'], 0)) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="right">{{ $stats['total_bookings'] }}</td>
                    <td class="right">{{ $stats['cancelled_bookings'] }}</td>
                    <td class="right">{{ $stats['total_nights'] }}</td>
                    <td>{{ $dec($stats['occupancy']) }} %</td>
                    <td class="right nowrap">{{ $fmt($stats['booked_revenue']) }}</td>
                    <td class="right nowrap">{{ $fmt($stats['total_revenue']) }}</td>
                    <td class="right nowrap">{{ $fmt($stats['remaining']) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="muted">Aucune réservation sur la période.</p>
    @endif

    <h2>Encaissements par moyen de paiement</h2>
    @if ($paymentMethodStats->count())
        <table class="data" style="width: 60%">
            <thead>
                <tr>
                    <th>Moyen de paiement</th>
                    <th class="right">Transactions</th>
                    <th class="right">Montant (FCFA)</th>
                    <th class="right">Part</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paymentMethodStats as $method => $s)
                    <tr>
                        <td>{{ $methodLabels[$method] ?? ucfirst($method) }}</td>
                        <td class="right">{{ $s['count'] }}</td>
                        <td class="right nowrap">{{ $fmt($s['total']) }}</td>
                        <td class="right">{{ round($s['total'] / $totalPayments * 100) }} %</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="right">{{ $paymentMethodStats->sum('count') }}</td>
                    <td class="right nowrap">{{ $fmt($paymentMethodStats->sum('total')) }}</td>
                    <td class="right">100 %</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="muted">Aucun encaissement sur la période.</p>
    @endif

    <h2 style="page-break-before: auto">Détail des réservations ({{ $bookings->count() }})</h2>
    @if ($bookings->count())
        <table class="data">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>Résidence</th>
                    <th>Arrivée</th>
                    <th>Départ</th>
                    <th class="right">Nuits</th>
                    <th class="right">Montant</th>
                    <th class="right">Encaissé</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $b)
                    @php $paid = $b->payments->where('status', 'completed')->sum('amount'); @endphp
                    <tr>
                        <td class="nowrap bold">{{ $b->booking_number }}</td>
                        <td>{{ trim($b->first_name . ' ' . $b->last_name) }}</td>
                        <td class="nowrap">{{ $b->phone }}</td>
                        <td>{{ $b->residence?->name }}</td>
                        <td class="nowrap">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d/m/Y') }}</td>
                        <td class="nowrap">{{ \Carbon\Carbon::parse($b->check_out_date)->format('d/m/Y') }}</td>
                        <td class="right">{{ $b->nights ?: \Carbon\Carbon::parse($b->check_in_date)->diffInDays($b->check_out_date) }}</td>
                        <td class="right nowrap">{{ $fmt($b->total_amount) }}</td>
                        <td class="right nowrap {{ $paid >= $b->total_amount ? 'success' : ($paid > 0 ? 'warning' : 'muted') }}">{{ $fmt($paid) }}</td>
                        <td><span class="badge b-{{ $b->status }}">{{ $statusLabels[$b->status] ?? $b->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">Total</td>
                    <td class="right">{{ $bookings->sum(fn ($b) => $b->nights ?: \Carbon\Carbon::parse($b->check_in_date)->diffInDays($b->check_out_date)) }}</td>
                    <td class="right nowrap">{{ $fmt($bookings->sum('total_amount')) }}</td>
                    <td class="right nowrap">{{ $fmt($bookings->sum(fn ($b) => $b->payments->where('status', 'completed')->sum('amount'))) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="muted">Aucune réservation avec une arrivée sur cette période.</p>
    @endif

    <table class="signature">
        <tr>
            <td>
                <div class="muted small">Montants en francs CFA (FCFA). Réservations retenues selon leur date d'arrivée.
                    Le CA réservé et l'occupation ne comptent que les réservations confirmées ou terminées.</div>
            </td>
            <td class="right">
                <div>Visa de la direction</div>
                <div class="line" style="margin-left:auto"></div>
            </td>
        </tr>
    </table>
@endsection
