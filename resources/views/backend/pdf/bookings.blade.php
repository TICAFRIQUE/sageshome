@extends('backend.pdf.layout')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
    $paymentLabels = ['pending' => 'En attente', 'processing' => 'En cours', 'completed' => 'Payé', 'failed' => 'Échec'];
    $paymentClass = ['pending' => 'b-pending', 'processing' => 'b-pending', 'completed' => 'b-paid', 'failed' => 'b-failed'];
    $methodLabels = ['cash' => 'Espèces', 'wave' => 'Wave', 'paypal' => 'PayPal', 'bank_transfer' => 'Virement', 'credit_card' => 'Carte'];
@endphp

@section('title', 'Liste des réservations')
@section('subtitle')
    {{ $bookings->count() }} réservation{{ $bookings->count() > 1 ? 's' : '' }}
    @if ($filterSummary) · {{ $filterSummary }} @endif
@endsection

@section('content')
    <table class="kpis">
        <tr>
            <td class="featured">
                <div class="kpi-label">Montant total</div>
                <div class="kpi-value">{{ $fmt($bookings->sum('total_amount')) }} <small>FCFA</small></div>
            </td>
            <td>
                <div class="kpi-label">Confirmées / terminées</div>
                <div class="kpi-value">{{ $bookings->whereIn('status', ['confirmed', 'completed'])->count() }}</div>
            </td>
            <td>
                <div class="kpi-label">En attente</div>
                <div class="kpi-value">{{ $bookings->where('status', 'pending')->count() }}</div>
            </td>
            <td>
                <div class="kpi-label">Nuitées</div>
                <div class="kpi-value">{{ $bookings->sum('nights') }}</div>
            </td>
        </tr>
    </table>

    @if ($bookings->count())
        <table class="data" style="margin-top: 4mm">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Créée le</th>
                    <th>Client</th>
                    <th>Contact</th>
                    <th>Résidence</th>
                    <th>Séjour</th>
                    <th class="right">Nuits</th>
                    <th class="right">Montant (FCFA)</th>
                    <th>Statut</th>
                    <th>Paiement</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $b)
                    <tr>
                        <td class="nowrap bold">{{ $b->booking_number }}</td>
                        <td class="nowrap">{{ $b->created_at?->format('d/m/Y') }}</td>
                        <td>{{ trim($b->first_name . ' ' . $b->last_name) }}</td>
                        <td class="small">{{ $b->phone }}<br><span class="muted">{{ $b->email }}</span></td>
                        <td>{{ $b->residence?->name }}</td>
                        <td class="nowrap">{{ \Carbon\Carbon::parse($b->check_in_date)->format('d/m/y') }} → {{ \Carbon\Carbon::parse($b->check_out_date)->format('d/m/y') }}</td>
                        <td class="right">{{ $b->nights }}</td>
                        <td class="right nowrap bold">{{ $fmt($b->total_amount) }}</td>
                        <td><span class="badge b-{{ $b->status }}">{{ $statusLabels[$b->status] ?? $b->status }}</span></td>
                        <td>
                            @if ($b->payment)
                                <span class="badge {{ $paymentClass[$b->payment->status] ?? 'b-none' }}">{{ $paymentLabels[$b->payment->status] ?? $b->payment->status }}</span>
                                <div class="small muted">{{ $methodLabels[$b->payment->payment_method] ?? $b->payment->payment_method }}</div>
                            @else
                                <span class="badge b-none">Aucun</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">Total ({{ $bookings->count() }})</td>
                    <td class="right">{{ $bookings->sum('nights') }}</td>
                    <td class="right nowrap">{{ $fmt($bookings->sum('total_amount')) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="muted">Aucune réservation ne correspond aux filtres.</p>
    @endif
@endsection
