@extends('backend.layouts.master')

@section('title', 'Tableau de bord')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
    $paymentLabels = ['pending' => 'En attente', 'processing' => 'En cours', 'completed' => 'Payé', 'failed' => 'Échec'];
    $paymentClass = ['pending' => 'st-pending', 'processing' => 'st-pending', 'completed' => 'st-paid', 'failed' => 'st-failed'];
    $hour = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Bonjour' : ($hour < 18 ? 'Bon après-midi' : 'Bonsoir');
    $maxTop = max($topResidences->max('total') ?: 0, 1);
    $trendBadge = function ($value) {
        if ($value === null) return '';
        $up = $value >= 0;
        return '<span class="sh-trend ' . ($up ? 'up' : 'down') . '"><i class="ri-arrow-' . ($up ? 'up' : 'down') . '-line"></i>' . abs($value) . ' %</span>';
    };
@endphp

@section('content')
    {{-- En-tête --}}
    <div class="sh-page-head">
        <div>
            <h1>{{ $greeting }}{{ Auth::user()?->username ? ', ' . Auth::user()->username : '' }}</h1>
            <div class="text-muted">{{ ucfirst(now()->translatedFormat('l j F Y')) }} · voici l'activité de Sages Home</div>
        </div>
        <div class="sh-actions">
            <a href="{{ route('admin.bookings.report') }}" class="btn btn-light"><i class="ri-file-chart-line me-1"></i>Compte d'exploitation</a>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-gold"><i class="ri-add-line me-1"></i>Nouvelle réservation</a>
        </div>
    </div>

    {{-- Alerte à traiter --}}
    @if ($kpis['pending'] > 0)
        <div class="alert alert-warning d-flex flex-wrap align-items-center gap-2 border-0" role="alert">
            <i class="ri-notification-3-line fs-5"></i>
            <span><strong>{{ $kpis['pending'] }} réservation{{ $kpis['pending'] > 1 ? 's' : '' }}</strong> en attente de confirmation.</span>
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning ms-auto">Traiter maintenant</a>
        </div>
    @endif

    {{-- Indicateurs clés --}}
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="sh-kpi is-featured">
                <div class="sh-kpi-icon"><i class="ri-wallet-3-line"></i></div>
                <div class="sh-kpi-label">Encaissé ce mois</div>
                <div class="sh-kpi-value">{{ $fmt($kpis['revenue_month']) }} <small>FCFA</small></div>
                <div class="sh-kpi-meta">@if ($kpis['revenue_trend'] !== null){!! $trendBadge($kpis['revenue_trend']) !!} vs même période le mois dernier @else Aujourd'hui : {{ $fmt($kpis['revenue_today']) }} FCFA @endif</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="sh-kpi is-gold">
                <div class="sh-kpi-icon"><i class="ri-calendar-check-line"></i></div>
                <div class="sh-kpi-label">Réservations ce mois</div>
                <div class="sh-kpi-value">{{ $kpis['bookings_month'] }}</div>
                <div class="sh-kpi-meta">@if ($kpis['bookings_trend'] !== null){!! $trendBadge($kpis['bookings_trend']) !!} vs même période le mois dernier @else {{ $kpis['pending'] }} en attente de confirmation @endif</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="sh-kpi">
                <div class="sh-kpi-icon"><i class="ri-hotel-bed-line"></i></div>
                <div class="sh-kpi-label">Occupation ce soir</div>
                <div class="sh-kpi-value">{{ $kpis['occupancy_today'] }} <small>%</small></div>
                <div class="sh-bar my-2"><span style="width: {{ $kpis['occupancy_today'] }}%"></span></div>
                <div class="sh-kpi-meta">{{ $kpis['occupied_today'] }} / {{ $kpis['total_residences'] }} résidences occupées</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="sh-kpi is-warning">
                <div class="sh-kpi-icon"><i class="ri-hand-coin-line"></i></div>
                <div class="sh-kpi-label">Reste à encaisser</div>
                <div class="sh-kpi-value">{{ $fmt($kpis['to_collect']) }} <small>FCFA</small></div>
                <div class="sh-kpi-meta">réservations actives non payées</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        {{-- Graphique --}}
        <div class="col-xl-8">
            <div class="card h-100 mb-0">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="card-title mb-0">Encaissements des 12 derniers mois</h5>
                        <div class="small text-muted">Année en cours : <strong>{{ $fmt($kpis['revenue_year']) }} FCFA</strong> · aujourd'hui : {{ $fmt($kpis['revenue_today']) }} FCFA</div>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <div id="revenueChart" style="min-height: 300px"></div>
                </div>
            </div>
        </div>

        {{-- Aujourd'hui --}}
        <div class="col-xl-4">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <ul class="nav nav-pills nav-justified gap-1" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-1" data-bs-toggle="tab" data-bs-target="#tab-arrivals" type="button" role="tab">
                                Arrivées <span class="badge bg-white text-dark ms-1">{{ $arrivals->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-1" data-bs-toggle="tab" data-bs-target="#tab-departures" type="button" role="tab">
                                Départs <span class="badge bg-light text-dark ms-1">{{ $departures->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-1" data-bs-toggle="tab" data-bs-target="#tab-upcoming" type="button" role="tab">
                                7 jours <span class="badge bg-light text-dark ms-1">{{ $upcoming->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" style="max-height: 360px; overflow:auto">
                    @foreach (['arrivals' => [$arrivals, 'Aucune arrivée prévue aujourd\'hui', 'check_in_date'], 'departures' => [$departures, 'Aucun départ aujourd\'hui', 'check_out_date'], 'upcoming' => [$upcoming, 'Aucune arrivée dans les 7 prochains jours', 'check_in_date']] as $key => [$items, $empty, $dateField])
                        <div class="tab-pane fade {{ $key === 'arrivals' ? 'show active' : '' }}" id="tab-{{ $key }}" role="tabpanel">
                            @if ($items->count())
                                <ul class="sh-list">
                                    @foreach ($items as $b)
                                        <li>
                                            <span class="sh-avatar">{{ Str::upper(Str::substr($b->first_name, 0, 1) . Str::substr($b->last_name, 0, 1)) }}</span>
                                            <div class="sh-list-main">
                                                <a href="{{ route('admin.bookings.show', $b) }}" class="sh-list-title d-block text-reset">{{ $b->first_name }} {{ $b->last_name }}</a>
                                                <div class="sh-list-sub text-truncate">
                                                    {{ $b->residence?->name }}
                                                    @if ($key === 'upcoming') · {{ ucfirst(\Carbon\Carbon::parse($b->$dateField)->translatedFormat('D j M')) }} @endif
                                                </div>
                                            </div>
                                            @if ($b->phone)
                                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $b->phone) }}" class="btn btn-sm btn-soft-primary" title="Appeler {{ $b->phone }}"><i class="ri-phone-line"></i></a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="sh-empty py-4">
                                    <div class="sh-empty-icon"><i class="ri-cup-line"></i></div>
                                    <p class="text-muted small mb-0">{{ $empty }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Réservations récentes --}}
        <div class="col-xl-8">
            <div class="card h-100 mb-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Dernières réservations</h5>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-soft-primary">Tout voir <i class="ri-arrow-right-line ms-1"></i></a>
                </div>
                @if ($recentBookings->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-card mb-0 table-nowrap">
                            <thead>
                                <tr>
                                    <th>Réservation</th>
                                    <th>Client</th>
                                    <th>Séjour</th>
                                    <th class="text-end">Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentBookings as $b)
                                    <tr data-href="{{ route('admin.bookings.show', $b) }}">
                                        <td>
                                            <span class="fw-semibold">{{ $b->booking_number }}</span>
                                            <div class="small text-muted">{{ $b->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-medium">{{ $b->first_name }} {{ $b->last_name }}</div>
                                            <div class="small text-muted text-truncate" style="max-width: 180px">{{ $b->residence?->name }}</div>
                                        </td>
                                        <td class="small">
                                            {{ \Carbon\Carbon::parse($b->check_in_date)->translatedFormat('d M') }} → {{ \Carbon\Carbon::parse($b->check_out_date)->translatedFormat('d M') }}
                                        </td>
                                        <td class="text-end td-amount fw-semibold">{{ $fmt($b->total_amount) }}</td>
                                        <td>
                                            <span class="sh-badge st-{{ $b->status }}">{{ $statusLabels[$b->status] ?? $b->status }}</span>
                                            <div class="small mt-1 {{ $b->payment?->status === 'completed' ? 'text-success' : 'text-muted' }}">
                                                <i class="ri-wallet-3-line me-1"></i>{{ $b->payment ? ($paymentLabels[$b->payment->status] ?? $b->payment->status) : 'Aucun paiement' }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="sh-empty">
                        <div class="sh-empty-icon"><i class="ri-calendar-line"></i></div>
                        <p class="text-muted mb-0">Aucune réservation pour le moment.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-xl-4">
            {{-- Top résidences --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top résidences du mois</h5>
                </div>
                <div class="card-body">
                    @forelse ($topResidences as $r)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-semibold text-truncate me-2">{{ $loop->iteration }}. {{ $r->name }}</span>
                                <span class="td-amount fw-semibold">{{ $fmt($r->total) }}</span>
                            </div>
                            <div class="sh-bar"><span style="width: {{ round($r->total / $maxTop * 100) }}%"></span></div>
                            <div class="small text-muted mt-1">{{ $r->nb }} réservation{{ $r->nb > 1 ? 's' : '' }}</div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Pas encore de réservation confirmée ce mois-ci.</p>
                    @endforelse
                </div>
            </div>

            {{-- Statuts --}}
            <div class="card mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Réservations par statut</h5>
                </div>
                <div class="card-body">
                    @php $totalStatus = max($bookingStats->sum(), 1); @endphp
                    @foreach ($statusLabels as $key => $label)
                        @if (($bookingStats[$key] ?? 0) > 0)
                            <a href="{{ route('admin.bookings.index', ['status' => $key]) }}" class="d-flex align-items-center justify-content-between py-2 text-reset border-bottom">
                                <span class="sh-badge st-{{ $key }}">{{ $label }}</span>
                                <span class="small text-muted">{{ round(($bookingStats[$key] ?? 0) / $totalStatus * 100) }} %</span>
                                <span class="fw-bold">{{ $bookingStats[$key] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector('#revenueChart');
            if (!el || !window.ApexCharts) return;
            var data = @json($chart);
            var fcfa = function (v) { return new Intl.NumberFormat('fr-FR').format(Math.round(v)) + ' FCFA'; };

            new ApexCharts(el, {
                chart: { type: 'line', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif', zoom: { enabled: false } },
                series: [
                    { name: 'Encaissements', type: 'column', data: data.revenue },
                    { name: 'Réservations', type: 'line', data: data.bookings }
                ],
                labels: data.labels,
                colors: ['#37542A', '#C29B32'],
                stroke: { width: [0, 3], curve: 'straight' },
                plotOptions: { bar: { columnWidth: '45%', borderRadius: 6 } },
                fill: { opacity: [0.9, 1] },
                markers: { size: [0, 4] },
                dataLabels: { enabled: false },
                grid: { borderColor: '#EEECE4', strokeDashArray: 4 },
                legend: { position: 'top', horizontalAlign: 'right' },
                yaxis: [
                    { labels: { formatter: function (v) { return v >= 1000000 ? (v / 1000000).toFixed(1).replace('.', ',') + ' M' : (v >= 1000 ? Math.round(v / 1000) + ' k' : v); } } },
                    { opposite: true, min: 0, forceNiceScale: true, labels: { formatter: function (v) { return Math.round(v); } } }
                ],
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: { formatter: function (v, o) { return o.seriesIndex === 0 ? fcfa(v) : v + ' réservation' + (v > 1 ? 's' : ''); } }
                }
            }).render();
        });
    </script>
@endsection
