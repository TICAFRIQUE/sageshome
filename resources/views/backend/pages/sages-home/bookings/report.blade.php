@extends('backend.layouts.master')

@section('title', "Compte d'exploitation")

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $dec = fn ($n) => number_format((float) $n, 1, ',', ' ');
    $start = \Carbon\Carbon::parse($filters['start_date']);
    $end = \Carbon\Carbon::parse($filters['end_date']);
    $selectedResidence = $filters['residence_id'] ? $residences->firstWhere('id', $filters['residence_id']) : null;
    $presets = [
        'this_month' => 'Ce mois',
        'last_month' => 'Mois dernier',
        'this_quarter' => 'Ce trimestre',
        'this_year' => 'Cette année',
        'last_year' => 'Année dernière',
    ];
    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
    $methodInfo = [
        'cash' => ['Espèces', 'ri-money-dollar-box-line'],
        'wave' => ['Wave', 'ri-smartphone-line'],
        'bank_transfer' => ['Virement bancaire', 'ri-bank-line'],
        'credit_card' => ['Carte bancaire', 'ri-bank-card-line'],
        'paypal' => ['PayPal', 'ri-paypal-line'],
    ];
    $maxResidenceRevenue = max($residenceStats->max('booked_revenue') ?: 0, 1);
    $totalPayments = max($paymentMethodStats->sum('total'), 1);
    $collectRate = $stats['booked_revenue'] > 0 ? min(100, round($stats['total_revenue'] / $stats['booked_revenue'] * 100)) : 0;
    $query = array_filter(['start_date' => $filters['start_date'], 'end_date' => $filters['end_date'], 'residence_id' => $filters['residence_id'], 'status' => $filters['status']]);
@endphp

@section('content')
    <div class="sh-page-head">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active">Compte d'exploitation</li>
            </ol>
            <h1>Compte d'exploitation
                @if ($selectedResidence)
                    <span class="text-muted fw-normal">· {{ $selectedResidence->name }}</span>
                @endif
            </h1>
            <div class="text-muted small">
                Du {{ $start->translatedFormat('j F Y') }} au {{ $end->translatedFormat('j F Y') }} · {{ $stats['days'] }} jours
                · réservations par date d'arrivée
            </div>
        </div>
        <div class="sh-actions">
            <a href="{{ route('admin.bookings.report.export', $query) }}" class="btn btn-light">
                <i class="ri-file-excel-2-line me-1 text-success"></i>Exporter (Excel)
            </a>
            <a href="{{ route('admin.bookings.report.pdf', $query) }}" class="btn btn-primary" data-no-progress>
                <i class="ri-file-pdf-2-line me-1"></i>Télécharger le PDF
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card no-print">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach ($presets as $key => $label)
                    <a href="{{ route('admin.bookings.report', array_filter(['preset' => $key, 'residence_id' => $filters['residence_id'], 'status' => $filters['status']])) }}"
                        class="btn btn-sm {{ $filters['preset'] === $key ? 'btn-primary' : 'btn-light' }}">{{ $label }}</a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('admin.bookings.report') }}" class="row g-2 align-items-end" data-auto-submit>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="start_date">Du</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $filters['start_date'] }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="end_date">Au</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $filters['end_date'] }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="residence_id">Résidence</label>
                    <select class="form-select" id="residence_id" name="residence_id">
                        <option value="">Toutes les résidences</option>
                        @foreach ($residences as $residence)
                            <option value="{{ $residence->id }}" @selected($filters['residence_id'] == $residence->id)>{{ $residence->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" for="status">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous</option>
                        @foreach ($statusLabels as $key => $label)
                            <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-1">
                    <a href="{{ route('admin.bookings.report') }}" class="btn btn-light w-100" title="Réinitialiser"><i class="ri-refresh-line"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- En-tête imprimé --}}
    <div class="d-none d-print-block text-center mb-4">
        <h2 class="mb-1">SAGES HOME</h2>
        <h4 class="mb-1">Compte d'exploitation</h4>
        <p class="mb-0">Période du {{ $start->format('d/m/Y') }} au {{ $end->format('d/m/Y') }}
            @if ($selectedResidence) · {{ $selectedResidence->name }} @endif
        </p>
        <hr>
    </div>

    {{-- Indicateurs --}}
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="sh-kpi is-featured">
                <div class="sh-kpi-icon"><i class="ri-wallet-3-line"></i></div>
                <div class="sh-kpi-label">Encaissé</div>
                <div class="sh-kpi-value">{{ $fmt($stats['total_revenue']) }} <small>FCFA</small></div>
                <div class="sh-kpi-meta">{{ $collectRate }} % du chiffre d'affaires réservé</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="sh-kpi is-gold">
                <div class="sh-kpi-icon"><i class="ri-hand-coin-line"></i></div>
                <div class="sh-kpi-label">Chiffre d'affaires réservé</div>
                <div class="sh-kpi-value">{{ $fmt($stats['booked_revenue']) }} <small>FCFA</small></div>
                <div class="sh-kpi-meta">
                    @if ($stats['remaining'] > 0)
                        <span class="text-warning fw-semibold">{{ $fmt($stats['remaining']) }} FCFA</span> restant à encaisser
                    @else
                        Tout est encaissé
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="sh-kpi">
                <div class="sh-kpi-icon"><i class="ri-hotel-bed-line"></i></div>
                <div class="sh-kpi-label">Taux d'occupation</div>
                <div class="sh-kpi-value">{{ $dec($stats['occupancy']) }} <small>%</small></div>
                <div class="sh-bar my-2"><span style="width: {{ $stats['occupancy'] }}%"></span></div>
                <div class="sh-kpi-meta">{{ $stats['total_nights'] }} nuitées / {{ $stats['residence_count'] * $stats['days'] }} disponibles</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="sh-kpi is-info">
                <div class="sh-kpi-icon"><i class="ri-price-tag-3-line"></i></div>
                <div class="sh-kpi-label">Prix moyen / nuit</div>
                <div class="sh-kpi-value">{{ $fmt($stats['adr']) }} <small>FCFA</small></div>
                <div class="sh-kpi-meta">Séjour moyen : {{ $dec($stats['avg_stay']) }} nuit{{ $stats['avg_stay'] > 1 ? 's' : '' }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        @foreach ([
            ['Réservations', $stats['total_bookings'], 'ri-calendar-line', ''],
            ['Confirmées / terminées', $stats['confirmed_bookings'], 'ri-checkbox-circle-line', 'text-success'],
            ['En attente', $stats['pending_bookings'], 'ri-time-line', 'text-warning'],
            ['Annulées', $stats['cancelled_bookings'] . ' (' . $dec($stats['cancellation_rate']) . ' %)', 'ri-close-circle-line', 'text-danger'],
        ] as [$label, $value, $icon, $color])
            <div class="col-6 col-xl-3">
                <div class="card mb-0">
                    <div class="card-body py-3 d-flex align-items-center gap-3">
                        <i class="{{ $icon }} fs-3 {{ $color ?: 'text-muted' }}"></i>
                        <div>
                            <div class="fs-5 fw-bold lh-1">{{ $value }}</div>
                            <div class="small text-muted">{{ $label }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        {{-- Par résidence --}}
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Performance par résidence</h5>
                    <span class="small text-muted no-print">Cliquez sur une ligne pour filtrer</span>
                </div>
                @if ($residenceStats->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-card mb-0">
                            <thead>
                                <tr>
                                    <th>Résidence</th>
                                    <th class="text-center">Résa.</th>
                                    <th class="text-center">Nuitées</th>
                                    <th style="min-width: 140px">Occupation</th>
                                    <th class="text-end">CA réservé</th>
                                    <th class="text-end">Encaissé</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($residenceStats as $stat)
                                    <tr data-href="{{ route('admin.bookings.report', array_merge($query, ['residence_id' => $stat['residence']?->id])) }}"
                                        @class(['table-active' => $filters['residence_id'] == $stat['residence']?->id])>
                                        <td>
                                            <div class="fw-semibold">{{ $stat['residence']?->name ?? 'Résidence supprimée' }}</div>
                                            <div class="sh-bar mt-1" style="height: 4px"><span style="width: {{ round($stat['booked_revenue'] / $maxResidenceRevenue * 100) }}%"></span></div>
                                        </td>
                                        <td class="text-center">{{ $stat['total_bookings'] }}
                                            @if ($stat['cancelled_bookings'])
                                                <div class="small text-danger">{{ $stat['cancelled_bookings'] }} annul.</div>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $stat['total_nights'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="sh-bar flex-grow-1"><span style="width: {{ $stat['occupancy'] }}%"></span></div>
                                                <span class="small fw-semibold" style="min-width: 42px">{{ $dec($stat['occupancy']) }} %</span>
                                            </div>
                                        </td>
                                        <td class="text-end td-amount">{{ $fmt($stat['booked_revenue']) }}</td>
                                        <td class="text-end td-amount fw-semibold">{{ $fmt($stat['total_revenue']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-center">{{ $stats['total_bookings'] }}</td>
                                    <td class="text-center">{{ $stats['total_nights'] }}</td>
                                    <td>{{ $dec($stats['occupancy']) }} %</td>
                                    <td class="text-end td-amount">{{ $fmt($stats['booked_revenue']) }}</td>
                                    <td class="text-end td-amount">{{ $fmt($stats['total_revenue']) }} FCFA</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="sh-empty">
                        <div class="sh-empty-icon"><i class="ri-bar-chart-2-line"></i></div>
                        <p class="text-muted mb-0">Aucune réservation sur cette période.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Par moyen de paiement --}}
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Encaissements par moyen de paiement</h5>
                </div>
                <div class="card-body">
                    @forelse ($paymentMethodStats as $method => $data)
                        @php
                            [$label, $icon] = $methodInfo[$method] ?? [ucfirst($method), 'ri-question-line'];
                            $pct = round($data['total'] / $totalPayments * 100);
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold"><i class="{{ $icon }} me-1 text-gold"></i>{{ $label }}
                                    <span class="text-muted fw-normal small">· {{ $data['count'] }} paiement{{ $data['count'] > 1 ? 's' : '' }}</span></span>
                                <span class="td-amount fw-semibold">{{ $fmt($data['total']) }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="sh-bar flex-grow-1"><span style="width: {{ $pct }}%"></span></div>
                                <span class="small text-muted" style="min-width: 34px">{{ $pct }} %</span>
                            </div>
                        </div>
                    @empty
                        <div class="sh-empty py-4">
                            <div class="sh-empty-icon"><i class="ri-wallet-line"></i></div>
                            <p class="text-muted mb-0">Aucun encaissement sur la période.</p>
                        </div>
                    @endforelse

                    @if ($paymentMethodStats->count() > 0)
                        <div class="d-flex justify-content-between border-top pt-3 mt-2 fw-bold">
                            <span>Total encaissé</span>
                            <span class="td-amount">{{ $fmt($paymentMethodStats->sum('total')) }} FCFA</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Détail --}}
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Détail des réservations <span class="text-muted fw-normal">({{ $bookings->count() }})</span></h5>
        </div>
        @if ($bookings->count() > 0)
            <div class="table-responsive" style="max-height: 560px">
                <table class="table table-sm table-hover align-middle table-card table-sticky mb-0">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Résidence</th>
                            <th>Arrivée</th>
                            <th>Départ</th>
                            <th class="text-center">Nuits</th>
                            <th class="text-end">Montant</th>
                            <th class="text-end">Encaissé</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            @php $paid = $booking->payments->where('status', 'completed')->sum('amount'); @endphp
                            <tr data-href="{{ route('admin.bookings.show', $booking) }}">
                                <td class="fw-semibold">{{ $booking->booking_number }}</td>
                                <td>{{ $booking->first_name }} {{ $booking->last_name }}</td>
                                <td>{{ $booking->residence?->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $booking->nights ?: \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }}</td>
                                <td class="text-end td-amount">{{ $fmt($booking->total_amount) }}</td>
                                <td class="text-end td-amount {{ $paid >= $booking->total_amount ? 'text-success' : ($paid > 0 ? 'text-warning' : 'text-muted') }}">{{ $fmt($paid) }}</td>
                                <td><span class="sh-badge st-{{ $booking->status }}">{{ $statusLabels[$booking->status] ?? $booking->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="sh-empty">
                <div class="sh-empty-icon"><i class="ri-calendar-close-line"></i></div>
                <p class="text-muted mb-0">Aucune réservation avec une arrivée sur cette période.</p>
            </div>
        @endif
    </div>

    <div class="d-none d-print-flex justify-content-between mt-5 pt-3 border-top">
        <span><strong>Généré le :</strong> {{ now()->format('d/m/Y à H:i') }}</span>
        <span><strong>Signature :</strong> ____________________</span>
    </div>
@endsection

@section('script')
    <script>
        window.onbeforeprint = function () {
            document.title = 'Compte_exploitation_{{ $filters['start_date'] }}_{{ $filters['end_date'] }}';
        };
    </script>
@endsection
