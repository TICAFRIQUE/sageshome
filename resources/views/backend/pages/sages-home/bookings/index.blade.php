@extends('backend.layouts.master')

@section('title', 'Réservations')

@php
    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
    $tabs = ['' => 'Toutes', 'pending' => 'En attente', 'confirmed' => 'Confirmées', 'completed' => 'Terminées', 'cancelled' => 'Annulées'];
    $paymentLabels = ['pending' => 'En attente', 'processing' => 'En cours', 'completed' => 'Payé', 'failed' => 'Échec'];
    $paymentClass = ['pending' => 'st-pending', 'processing' => 'st-pending', 'completed' => 'st-paid', 'failed' => 'st-failed'];
    $methodLabels = ['cash' => 'Espèces', 'wave' => 'Wave', 'paypal' => 'PayPal', 'bank_transfer' => 'Virement', 'credit_card' => 'Carte'];
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $currentStatus = request('status', '');
    $totalAll = $statusCounts->sum();
    $filterKeys = ['search', 'residence_id', 'payment_status', 'date_from', 'date_to'];
    $activeFilters = collect(request()->only($filterKeys))->filter(fn ($v) => filled($v));
    $without = fn ($key) => route('admin.bookings.index', array_filter(request()->except([$key, 'page']), fn ($v) => filled($v)));
    $filterText = [
        'search' => fn ($v) => '« ' . $v . ' »',
        'residence_id' => fn ($v) => optional($residences->firstWhere('id', $v))->name ?? 'Résidence',
        'payment_status' => fn ($v) => 'Paiement : ' . ($v === 'none' ? 'aucun' : strtolower($paymentLabels[$v] ?? $v)),
        'date_from' => fn ($v) => 'Arrivée dès le ' . \Carbon\Carbon::parse($v)->format('d/m/Y'),
        'date_to' => fn ($v) => 'Arrivée jusqu\'au ' . \Carbon\Carbon::parse($v)->format('d/m/Y'),
    ];
@endphp

@section('content')
    <div class="sh-page-head">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active">Réservations</li>
            </ol>
            <h1>Réservations</h1>
        </div>
        <div class="sh-actions">
            <a href="{{ route('admin.bookings.calendar') }}" class="btn btn-light"><i class="ri-calendar-2-line me-1"></i>Calendrier</a>
            <div class="btn-group">
                <a href="{{ route('admin.bookings.export', request()->except('page')) }}" class="btn btn-light" data-no-progress>
                    <i class="ri-file-excel-2-line me-1 text-success"></i>Excel
                </a>
                <a href="{{ route('admin.bookings.export-pdf', request()->except('page')) }}" class="btn btn-light" data-no-progress>
                    <i class="ri-file-pdf-2-line me-1 text-danger"></i>PDF
                </a>
            </div>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-gold"><i class="ri-add-line me-1"></i>Nouvelle réservation</a>
        </div>
    </div>

    {{-- Indicateurs du jour --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-reset">
                <div class="sh-kpi is-warning card-animate">
                    <div class="sh-kpi-icon"><i class="ri-time-line"></i></div>
                    <div class="sh-kpi-label">À traiter</div>
                    <div class="sh-kpi-value">{{ $kpis['pending'] }}</div>
                    <div class="sh-kpi-meta">réservation{{ $kpis['pending'] > 1 ? 's' : '' }} en attente</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a href="{{ route('admin.bookings.index', ['date_from' => today()->toDateString(), 'date_to' => today()->toDateString()]) }}" class="text-reset">
                <div class="sh-kpi card-animate">
                    <div class="sh-kpi-icon"><i class="ri-login-box-line"></i></div>
                    <div class="sh-kpi-label">Arrivées du jour</div>
                    <div class="sh-kpi-value">{{ $kpis['arrivals_today'] }}</div>
                    <div class="sh-kpi-meta">{{ now()->translatedFormat('l j F') }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <div class="sh-kpi is-info">
                <div class="sh-kpi-icon"><i class="ri-logout-box-r-line"></i></div>
                <div class="sh-kpi-label">Départs du jour</div>
                <div class="sh-kpi-value">{{ $kpis['departures_today'] }}</div>
                <div class="sh-kpi-meta">logements à préparer</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="sh-kpi is-featured">
                <div class="sh-kpi-icon"><i class="ri-money-cny-circle-line"></i></div>
                <div class="sh-kpi-label">Encaissé ce mois</div>
                <div class="sh-kpi-value">{{ $fmt($kpis['month_revenue']) }} <small>FCFA</small></div>
                <div class="sh-kpi-meta">{{ ucfirst(now()->translatedFormat('F Y')) }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        {{-- Onglets de statut --}}
        <nav class="sh-tabs" aria-label="Filtrer par statut">
            @foreach ($tabs as $key => $label)
                @php $count = $key === '' ? $totalAll : ($statusCounts[$key] ?? 0); @endphp
                @if ($key === '' || $count > 0 || $currentStatus === $key)
                    <a href="{{ route('admin.bookings.index', array_filter(array_merge(request()->except(['status', 'page']), ['status' => $key]), fn ($v) => filled($v))) }}"
                        class="{{ $currentStatus === $key ? 'active' : '' }}">
                        {{ $label }} <span class="count">{{ $count }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- Filtres --}}
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="sh-filters" data-auto-submit>
            @if ($currentStatus)
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            @endif
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4 col-xl-3">
                    <label class="form-label" for="search">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="search" class="form-control" id="search" name="search" value="{{ request('search') }}"
                            placeholder="Réf., client, e-mail, téléphone…" data-search-shortcut autocomplete="off">
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label" for="residence_id">Résidence</label>
                    <select class="form-select" id="residence_id" name="residence_id">
                        <option value="">Toutes</option>
                        @foreach ($residences as $residence)
                            <option value="{{ $residence->id }}" @selected(request('residence_id') == $residence->id)>{{ $residence->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label" for="payment_status">Paiement</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="">Tous</option>
                        <option value="completed" @selected(request('payment_status') === 'completed')>Payé</option>
                        <option value="pending" @selected(request('payment_status') === 'pending')>En attente</option>
                        <option value="processing" @selected(request('payment_status') === 'processing')>En cours</option>
                        <option value="failed" @selected(request('payment_status') === 'failed')>Échec</option>
                        <option value="none" @selected(request('payment_status') === 'none')>Aucun paiement</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label" for="date_from">Arrivée du</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label" for="date_to">au</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 col-md-4 col-xl-1">
                    <label class="form-label" for="sort">Tri</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="">Récentes</option>
                        <option value="check_in" @selected(request('sort') === 'check_in')>Arrivée ↑</option>
                        <option value="check_in_desc" @selected(request('sort') === 'check_in_desc')>Arrivée ↓</option>
                        <option value="amount_desc" @selected(request('sort') === 'amount_desc')>Montant ↓</option>
                    </select>
                </div>
            </div>
            <noscript><button class="btn btn-primary mt-2">Filtrer</button></noscript>

            @if ($activeFilters->isNotEmpty())
                <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                    @foreach ($activeFilters as $key => $value)
                        <a href="{{ $without($key) }}" class="sh-chip" title="Retirer ce filtre">{{ $filterText[$key]($value) }} <i class="ri-close-line"></i></a>
                    @endforeach
                    <a href="{{ route('admin.bookings.index', array_filter(['status' => $currentStatus])) }}" class="small fw-semibold ms-1">Tout effacer</a>
                </div>
            @endif
        </form>

        {{-- Tableau --}}
        @if ($bookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle table-card">
                    <thead>
                        <tr>
                            <th>Réservation</th>
                            <th>Client</th>
                            <th>Séjour</th>
                            <th class="text-end">Montant</th>
                            <th>Statut / paiement</th>
                            <th class="text-end col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            @php
                                $in = \Carbon\Carbon::parse($booking->check_in_date);
                                $out = \Carbon\Carbon::parse($booking->check_out_date);
                                $nights = $booking->nights ?: $in->diffInDays($out);
                                $initials = Str::upper(Str::substr($booking->first_name, 0, 1) . Str::substr($booking->last_name, 0, 1));
                                $payment = $booking->payment;
                            @endphp
                            <tr data-href="{{ route('admin.bookings.show', $booking) }}" @class(['table-warning-subtle' => is_null($booking->seen_at) && $booking->status === 'pending'])>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="fw-semibold">{{ $booking->booking_number }}</a>
                                    @if (is_null($booking->seen_at))
                                        <span class="badge bg-danger-subtle text-danger ms-1">Nouveau</span>
                                    @endif
                                    <div class="small text-truncate" style="max-width: 190px" title="{{ $booking->residence?->name }}">{{ $booking->residence?->name ?? '—' }}</div>
                                    <div class="small text-muted">{{ $booking->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="sh-avatar">{{ $initials ?: '?' }}</span>
                                        <div class="min-w-0">
                                            <div class="fw-semibold text-truncate" style="max-width: 180px">{{ $booking->first_name }} {{ $booking->last_name }}</div>
                                            <div class="small text-muted text-truncate" style="max-width: 180px">{{ $booking->phone ?: $booking->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium"><span class="text-nowrap">{{ $in->format('d/m/y') }} → {{ $out->format('d/m/y') }}</span></div>
                                    <div class="small text-muted">
                                        {{ $nights }} nuit{{ $nights > 1 ? 's' : '' }} · {{ $booking->guests ?? $booking->guests_count }} pers.
                                        @if ($in->isToday())
                                            · <span class="text-success fw-semibold">arrivée aujourd'hui</span>
                                        @elseif ($in->isFuture() && $in->diffInDays(today()) <= 3 && $booking->status !== 'cancelled')
                                            · <span class="text-gold fw-semibold">dans {{ (int) today()->diffInDays($in) }} j</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end td-amount fw-semibold">{{ $fmt($booking->total_amount) }} <span class="text-muted fw-normal small">FCFA</span></td>
                                <td>
                                    <span class="sh-badge st-{{ $booking->status }}">{{ $statusLabels[$booking->status] ?? $booking->status }}</span>
                                    <div class="small mt-1 {{ $payment?->status === 'completed' ? 'text-success' : 'text-muted' }}">
                                        <i class="ri-wallet-3-line me-1"></i>{{ $payment ? ($paymentLabels[$payment->status] ?? $payment->status) . ' · ' . ($methodLabels[$payment->payment_method] ?? ucfirst((string) $payment->payment_method)) : 'Aucun paiement' }}
                                    </div>
                                </td>
                                <td class="text-end col-actions">
                                    <div class="d-inline-flex gap-1">
                                        @if ($booking->status === 'pending')
                                            <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST"
                                                data-confirm="Confirmer la réservation {{ $booking->booking_number }} ?" data-confirm-icon="question" data-confirm-button="Confirmer">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-sm btn-soft-success" title="Confirmer" data-bs-toggle="tooltip"><i class="ri-check-line"></i></button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-soft-primary" title="Voir le détail" data-bs-toggle="tooltip"><i class="ri-eye-line"></i></a>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-label="Plus d'actions"><i class="ri-more-2-fill"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if ($payment && in_array($payment->status, ['pending', 'processing']))
                                                    <li>
                                                        <form action="{{ route('admin.bookings.confirm-payment', $booking) }}" method="POST"
                                                            data-confirm="Marquer le paiement de {{ $fmt($booking->total_amount) }} FCFA comme reçu ?" data-confirm-icon="question" data-confirm-button="Paiement reçu">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item"><i class="ri-money-dollar-circle-line me-2 text-success"></i>Paiement reçu</button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if ($booking->phone)
                                                    <li><a class="dropdown-item" href="tel:{{ preg_replace('/[^\d+]/', '', $booking->phone) }}"><i class="ri-phone-line me-2 text-muted"></i>Appeler</a></li>
                                                    <li><a class="dropdown-item" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', $booking->phone) }}"><i class="ri-whatsapp-line me-2 text-success"></i>WhatsApp</a></li>
                                                @endif
                                                @if ($booking->email)
                                                    <li><a class="dropdown-item" href="mailto:{{ $booking->email }}"><i class="ri-mail-line me-2 text-muted"></i>Envoyer un e-mail</a></li>
                                                @endif
                                                @if ($booking->status !== 'cancelled')
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST"
                                                            data-confirm="Annuler la réservation {{ $booking->booking_number }} ? Le client devra être prévenu." data-confirm-button="Oui, annuler">
                                                            @csrf
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger"><i class="ri-close-circle-line me-2"></i>Annuler la réservation</button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2 border-top">
                <div class="d-flex align-items-center gap-2 small text-muted">
                    {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} sur {{ $bookings->total() }}
                    <form method="GET" class="d-inline-flex align-items-center gap-1 ms-2" data-auto-submit>
                        @foreach (request()->except(['per_page', 'page']) as $k => $v)
                            @if (filled($v) && !is_array($v))
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <select name="per_page" class="form-select form-select-sm" style="width:auto" aria-label="Lignes par page">
                            @foreach ([10, 25, 50, 100] as $n)
                                <option value="{{ $n }}" @selected($bookings->perPage() === $n)>{{ $n }} / page</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                {{ $bookings->onEachSide(1)->links() }}
            </div>
        @else
            <div class="sh-empty">
                <div class="sh-empty-icon"><i class="ri-calendar-close-line"></i></div>
                @if ($activeFilters->isNotEmpty() || $currentStatus)
                    <h5>Aucune réservation ne correspond à ces critères</h5>
                    <p class="text-muted">Modifiez ou effacez les filtres pour élargir la recherche.</p>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-light">Effacer les filtres</a>
                @else
                    <h5>Aucune réservation pour le moment</h5>
                    <p class="text-muted">Les réservations du site apparaîtront ici. Vous pouvez aussi en créer une manuellement.</p>
                    <a href="{{ route('admin.bookings.create') }}" class="btn btn-gold"><i class="ri-add-line me-1"></i>Nouvelle réservation</a>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .table-warning-subtle > td { background-color: #FFFBEF !important; }
        .min-w-0 { min-width: 0; }
        /* Actions toujours visibles, même avec le défilement horizontal */
        .col-actions { width: 1%; white-space: nowrap; position: sticky; right: 0; z-index: 1; background: #fff; box-shadow: -8px 0 8px -8px rgba(27,33,23,.15); }
        thead .col-actions { background: var(--sh-bg) !important; z-index: 2; }
        .table-hover tbody tr:hover .col-actions { background: #FAF8F1; }
        tr.table-warning-subtle .col-actions { background: #FFFBEF; }
        .pagination { flex-wrap: wrap; }
        nav[role="navigation"] p.small { display: none; }
    </style>
@endpush
