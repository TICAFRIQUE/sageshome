@extends('backend.layouts.master')

@section('title', 'Planning & calendrier')

@php
    $view = request('view', 'planning');
    $prev = $month->copy()->subMonth()->format('Y-m');
    $next = $month->copy()->addMonth()->format('Y-m');
    $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'completed' => 'Terminée', 'cancelled' => 'Annulée'];
    $avgOccupancy = count($planning) ? round(collect($planning)->avg('occupancy')) : 0;
@endphp

@section('content')
    <div class="sh-page-head">
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Réservations</a></li>
                <li class="breadcrumb-item active">Planning</li>
            </ol>
            <h1>Planning des résidences</h1>
        </div>
        <div class="sh-actions">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-light"><i class="ri-list-check me-1"></i>Liste</a>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-gold"><i class="ri-add-line me-1"></i>Nouvelle réservation</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills gap-1" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $view === 'planning' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#pane-planning" type="button" role="tab" data-view="planning">
                        <i class="ri-layout-row-line me-1"></i>Planning
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $view === 'calendar' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#pane-calendar" type="button" role="tab" data-view="calendar">
                        <i class="ri-calendar-2-line me-1"></i>Calendrier
                    </button>
                </li>
            </ul>
            <div class="d-flex flex-wrap align-items-center gap-3 small">
                <span><span class="legend-dot st-pending"></span>En attente</span>
                <span><span class="legend-dot st-confirmed"></span>Confirmée</span>
                <span><span class="legend-dot st-completed"></span>Terminée</span>
                <span><span class="legend-dot st-free"></span>Libre</span>
            </div>
        </div>

        <div class="tab-content">
            {{-- ================= PLANNING ================= --}}
            <div class="tab-pane fade {{ $view === 'planning' ? 'show active' : '' }}" id="pane-planning" role="tabpanel">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-2 border-bottom">
                    <div class="d-flex align-items-center gap-1">
                        <a href="{{ route('admin.bookings.calendar', ['month' => $prev]) }}" class="btn btn-sm btn-light" aria-label="Mois précédent"><i class="ri-arrow-left-s-line"></i></a>
                        <a href="{{ route('admin.bookings.calendar', ['month' => now()->format('Y-m')]) }}" class="btn btn-sm btn-light">Aujourd'hui</a>
                        <a href="{{ route('admin.bookings.calendar', ['month' => $next]) }}" class="btn btn-sm btn-light" aria-label="Mois suivant"><i class="ri-arrow-right-s-line"></i></a>
                        <h5 class="mb-0 ms-2">{{ ucfirst($month->translatedFormat('F Y')) }}</h5>
                    </div>
                    <div class="small text-muted">Occupation moyenne du mois : <strong class="text-body">{{ $avgOccupancy }} %</strong></div>
                </div>

                @if (count($planning))
                    <div class="planning-wrap">
                        <table class="planning">
                            <thead>
                                <tr>
                                    <th class="pl-res">Résidence</th>
                                    @foreach ($days as $d)
                                        <th @class(['pl-day', 'is-weekend' => $d->isWeekend(), 'is-today' => $d->isToday()])>
                                            <span>{{ mb_substr($d->translatedFormat('D'), 0, 2) }}</span>{{ $d->day }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($planning as $row)
                                    <tr>
                                        <th class="pl-res">
                                            <div class="text-truncate fw-semibold" title="{{ $row['residence']->name }}">{{ $row['residence']->name }}</div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="sh-bar flex-grow-1" style="height:4px"><span style="width: {{ $row['occupancy'] }}%"></span></div>
                                                <span class="small text-muted">{{ $row['occupancy'] }} %</span>
                                            </div>
                                        </th>
                                        @foreach ($row['cells'] as $cell)
                                            @if ($cell['booking'])
                                                @php $b = $cell['booking']; @endphp
                                                <td colspan="{{ $cell['span'] }}" class="pl-cell">
                                                    <a href="{{ route('admin.bookings.show', $b->id) }}" data-quick-view="{{ $b->id }}"
                                                        @class(['pl-booking', 'st-' . $b->status, 'cut-start' => $cell['starts_before'], 'cut-end' => $cell['ends_after']])
                                                        title="{{ $b->booking_number }} · {{ $b->first_name }} {{ $b->last_name }} · {{ \Carbon\Carbon::parse($b->check_in_date)->format('d/m') }} → {{ \Carbon\Carbon::parse($b->check_out_date)->format('d/m') }} · {{ $statusLabels[$b->status] ?? $b->status }}">
                                                        {{ $b->first_name }} {{ Str::substr($b->last_name, 0, 1) }}.
                                                    </a>
                                                </td>
                                            @else
                                                <td @class(['pl-cell', 'is-free', 'is-weekend' => $cell['date']->isWeekend(), 'is-today' => $cell['date']->isToday(), 'is-past' => $cell['date']->lt(today())])></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="sh-empty">
                        <div class="sh-empty-icon"><i class="ri-building-line"></i></div>
                        <p class="text-muted">Aucune résidence enregistrée.</p>
                    </div>
                @endif
                <div class="px-3 py-2 small text-muted border-top">
                    <i class="ri-information-line me-1"></i>Chaque case correspond à une nuit. Cliquez sur une réservation pour l'aperçu rapide.
                </div>
            </div>

            {{-- ================= CALENDRIER ================= --}}
            <div class="tab-pane fade {{ $view === 'calendar' ? 'show active' : '' }}" id="pane-calendar" role="tabpanel">
                <div class="d-flex flex-wrap gap-2 px-3 py-2 border-bottom">
                    <select class="form-select form-select-sm" id="residence-filter" style="width:auto; min-width: 200px" aria-label="Filtrer par résidence">
                        <option value="">Toutes les résidences</option>
                        @foreach ($residences as $residence)
                            <option value="{{ $residence->id }}">{{ $residence->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" id="status-filter" style="width:auto" aria-label="Filtrer par statut">
                        <option value="">Actives (hors annulées)</option>
                        <option value="pending">En attente</option>
                        <option value="confirmed">Confirmées</option>
                        <option value="completed">Terminées</option>
                        <option value="cancelled">Annulées</option>
                    </select>
                    <span class="ms-auto small text-muted align-self-center" id="calendar-loading" hidden>
                        <span class="spinner-border spinner-border-sm me-1"></span>Chargement…
                    </span>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Aperçu rapide --}}
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalTitle">Réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body" id="booking-details"></div>
                <div class="modal-footer" id="booking-actions"></div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .legend-dot { display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: .35rem; vertical-align: -1px; }
        .st-pending.legend-dot, .pl-booking.st-pending { background: #F5C451; color: #4A3400; }
        .st-confirmed.legend-dot, .pl-booking.st-confirmed { background: #37542A; color: #fff; }
        .st-completed.legend-dot, .pl-booking.st-completed { background: #6C8FB5; color: #fff; }
        .st-free.legend-dot { background: #fff; border: 1px solid #DDD9CC; }

        .planning-wrap { overflow-x: auto; position: relative; }
        .planning { border-collapse: separate; border-spacing: 0; width: 100%; font-size: .8rem; table-layout: fixed; min-width: 1100px; }
        .planning th, .planning td { border-bottom: 1px solid var(--sh-border); border-right: 1px solid #F1EFE7; height: 46px; padding: 0; }
        .planning thead th { height: 44px; position: sticky; top: 0; z-index: 2; background: var(--sh-bg); }
        .planning .pl-res {
            position: sticky; left: 0; z-index: 3; width: 210px; background: #fff;
            padding: .4rem .75rem; text-align: left; border-right: 1px solid var(--sh-border); font-weight: 400;
        }
        .planning thead .pl-res { background: var(--sh-bg); z-index: 4; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: var(--vz-secondary-color); }
        .pl-day { width: 34px; text-align: center; font-weight: 600; font-size: .78rem; color: var(--vz-heading-color); line-height: 1.1; }
        .pl-day span { display: block; font-size: .62rem; font-weight: 500; color: var(--vz-secondary-color); text-transform: capitalize; }
        .pl-day.is-weekend, .pl-cell.is-weekend { background: #FAF9F4; }
        .pl-day.is-today { background: var(--sh-gold-soft) !important; color: var(--sh-gold-text); }
        .pl-cell.is-today { background: #FFFBEF; box-shadow: inset 2px 0 0 var(--sh-gold-end), inset -2px 0 0 var(--sh-gold-end); }
        .pl-cell.is-past { background-image: repeating-linear-gradient(135deg, transparent 0 6px, rgba(0,0,0,.025) 6px 7px); }
        .pl-cell { padding: 4px 1px !important; }
        .pl-booking {
            display: block; height: 34px; line-height: 34px; padding: 0 .55rem; border-radius: 8px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600; font-size: .76rem;
            text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,.08); transition: transform .15s ease, box-shadow .15s ease;
        }
        .pl-booking:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,.15); color: inherit; filter: brightness(1.04); }
        .pl-booking.st-confirmed:hover, .pl-booking.st-completed:hover { color: #fff; }
        .pl-booking.cut-start { border-top-left-radius: 0; border-bottom-left-radius: 0; }
        .pl-booking.cut-end { border-top-right-radius: 0; border-bottom-right-radius: 0; }

        /* FullCalendar aux couleurs de la marque */
        #calendar { --fc-border-color: var(--sh-border); --fc-today-bg-color: #FFFBEF; --fc-page-bg-color: transparent; --fc-neutral-bg-color: var(--sh-bg); font-size: .85rem; }
        .fc .fc-toolbar-title { font-size: 1.15rem; font-weight: 700; text-transform: capitalize; }
        .fc .fc-button { background: #fff; border: 1px solid var(--sh-border); color: var(--vz-heading-color); border-radius: 8px; font-weight: 500; text-transform: capitalize; box-shadow: none !important; }
        .fc .fc-button:hover { background: var(--sh-bg); color: var(--vz-heading-color); }
        .fc .fc-button-primary:not(:disabled).fc-button-active, .fc .fc-button-primary:not(:disabled):active { background: #37542A; border-color: #37542A; color: #fff; }
        .fc .fc-button-group > .fc-button { border-radius: 0; }
        .fc .fc-button-group > .fc-button:first-child { border-radius: 8px 0 0 8px; }
        .fc .fc-button-group > .fc-button:last-child { border-radius: 0 8px 8px 0; }
        .fc .fc-col-header-cell-cushion { text-transform: capitalize; font-weight: 600; color: var(--vz-secondary-color); padding: .5rem; }
        .fc .fc-daygrid-day-number { font-weight: 600; color: var(--vz-heading-color); }
        .fc .fc-day-today .fc-daygrid-day-number { background: var(--sh-gold); border-radius: 999px; min-width: 26px; text-align: center; }
        .fc-event { border: 0 !important; border-radius: 6px !important; padding: 2px 6px; font-weight: 600; cursor: pointer; }
        .fc-h-event .fc-event-main { color: inherit; }
        .fc .fc-more-link { color: #37542A; font-weight: 600; }
        .fc .fc-list-event:hover td { background: var(--sh-bg); }
        .booking-quick dt { font-weight: 500; color: var(--vz-secondary-color); font-size: .8rem; }
        .booking-quick dd { font-weight: 600; margin-bottom: .6rem; }
    </style>
@endpush

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.9/locales/fr.global.min.js"></script>
    <script>
        (function () {
            var colors = {
                pending: { bg: '#F5C451', text: '#4A3400' },
                confirmed: { bg: '#37542A', text: '#ffffff' },
                completed: { bg: '#6C8FB5', text: '#ffffff' },
                cancelled: { bg: '#E4A0A0', text: '#5C1010' }
            };
            var statusLabels = @json($statusLabels);
            var paymentLabels = { pending: 'En attente', processing: 'En cours', completed: 'Payé', failed: 'Échec' };
            var methodLabels = { cash: 'Espèces', wave: 'Wave', paypal: 'PayPal', bank_transfer: 'Virement', credit_card: 'Carte' };
            var dataUrl = @json(route('admin.bookings.calendar-data'));
            var quickUrl = @json(url('admin/sages-home/bookings')) + '/';
            var csrf = document.querySelector('meta[name="csrf-token"]').content;
            var calendar = null;

            function esc(s) {
                return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }
            var fcfa = function (v) { return new Intl.NumberFormat('fr-FR').format(Math.round(v || 0)) + ' FCFA'; };

            /* ---------- Calendrier (chargé à la demande) ---------- */
            function initCalendar() {
                if (calendar || !window.FullCalendar) return;
                var loading = document.getElementById('calendar-loading');
                calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                    locale: 'fr',
                    initialView: window.innerWidth < 768 ? 'listMonth' : 'dayGridMonth',
                    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,dayGridWeek,listMonth' },
                    buttonText: { today: "Aujourd'hui", month: 'Mois', week: 'Semaine', list: 'Liste' },
                    height: 'auto',
                    dayMaxEvents: 4,
                    displayEventTime: false,
                    firstDay: 1,
                    loading: function (isLoading) { if (loading) loading.hidden = !isLoading; },
                    events: function (info, success, failure) {
                        var params = new URLSearchParams({ start: info.startStr.slice(0, 10), end: info.endStr.slice(0, 10) });
                        var res = document.getElementById('residence-filter').value;
                        var st = document.getElementById('status-filter').value;
                        if (res) params.append('residence_id', res);
                        if (st) params.append('status', st);
                        fetch(dataUrl + '?' + params, { headers: { 'Accept': 'application/json' } })
                            .then(function (r) { return r.json(); })
                            .then(function (rows) {
                                success(rows.map(function (b) {
                                    var c = colors[b.status] || colors.confirmed;
                                    return {
                                        id: b.id,
                                        title: b.guest_name + ' · ' + (b.residence_name || ''),
                                        start: b.check_in_date,
                                        end: b.check_out_date, // date de départ exclue (nuit précédente = dernière nuit)
                                        allDay: true,
                                        backgroundColor: c.bg,
                                        textColor: c.text,
                                        extendedProps: b
                                    };
                                }));
                            })
                            .catch(failure);
                    },
                    eventDidMount: function (arg) {
                        var b = arg.event.extendedProps;
                        arg.el.title = b.reference + ' · ' + b.guest_name + ' · ' + (statusLabels[b.status] || b.status) + ' · ' + fcfa(b.total_amount);
                    },
                    eventClick: function (info) {
                        info.jsEvent.preventDefault();
                        showBookingDetails(info.event.id);
                    }
                });
                calendar.render();
            }

            ['residence-filter', 'status-filter'].forEach(function (id) {
                document.getElementById(id).addEventListener('change', function () { if (calendar) calendar.refetchEvents(); });
            });

            document.querySelectorAll('[data-bs-toggle="tab"][data-view]').forEach(function (btn) {
                btn.addEventListener('shown.bs.tab', function () {
                    var url = new URL(window.location.href);
                    url.searchParams.set('view', btn.dataset.view);
                    history.replaceState(null, '', url);
                    if (btn.dataset.view === 'calendar') {
                        initCalendar();
                        calendar && calendar.updateSize();
                    }
                });
            });
            document.addEventListener('DOMContentLoaded', function () {
                if (@json($view) === 'calendar') initCalendar();
                // Centrer le planning sur aujourd'hui
                var today = document.querySelector('.planning thead .is-today');
                var wrap = document.querySelector('.planning-wrap');
                if (today && wrap) wrap.scrollLeft = Math.max(0, today.offsetLeft - 260);
            });

            /* ---------- Aperçu rapide ---------- */
            document.addEventListener('click', function (e) {
                var link = e.target.closest('[data-quick-view]');
                if (!link || e.metaKey || e.ctrlKey) return;
                e.preventDefault();
                showBookingDetails(link.dataset.quickView);
            });

            function showBookingDetails(id) {
                var modalEl = document.getElementById('bookingModal');
                var body = document.getElementById('booking-details');
                var actions = document.getElementById('booking-actions');
                body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement…</span></div></div>';
                actions.innerHTML = '';
                bootstrap.Modal.getOrCreateInstance(modalEl).show();

                fetch(quickUrl + id + '/quick-view', { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data.success) throw new Error();
                        var b = data.booking;
                        var pay = b.payment_status
                            ? '<span class="sh-badge st-' + (b.payment_status === 'completed' ? 'paid' : (b.payment_status === 'failed' ? 'failed' : 'pending')) + '">' + esc(paymentLabels[b.payment_status] || b.payment_status) + '</span> <span class="small text-muted">' + esc(methodLabels[b.payment_method] || b.payment_method || '') + '</span>'
                            : '<span class="sh-badge st-none">Aucun paiement</span>';
                        document.getElementById('bookingModalTitle').textContent = 'Réservation ' + b.reference;
                        body.innerHTML =
                            '<div class="d-flex align-items-center gap-3 mb-3">' +
                                '<span class="sh-avatar" style="width:44px;height:44px">' + esc((b.guest_name || '?').split(' ').map(function (p) { return p[0]; }).slice(0, 2).join('').toUpperCase()) + '</span>' +
                                '<div class="flex-grow-1"><div class="fw-bold fs-6">' + esc(b.guest_name) + '</div><div class="small text-muted">' + esc(b.residence_name) + '</div></div>' +
                                '<span class="sh-badge st-' + esc(b.status) + '">' + esc(statusLabels[b.status] || b.status) + '</span>' +
                            '</div>' +
                            '<dl class="row booking-quick mb-0">' +
                                '<div class="col-6"><dt>Arrivée</dt><dd>' + esc(b.check_in_label) + '</dd></div>' +
                                '<div class="col-6"><dt>Départ</dt><dd>' + esc(b.check_out_label) + '</dd></div>' +
                                '<div class="col-6"><dt>Durée</dt><dd>' + esc(b.nights) + ' nuit' + (b.nights > 1 ? 's' : '') + ' · ' + esc(b.guests_count || '–') + ' voyageur' + (b.guests_count > 1 ? 's' : '') + '</dd></div>' +
                                '<div class="col-6"><dt>Montant</dt><dd>' + fcfa(b.total_amount) + '</dd></div>' +
                                '<div class="col-12"><dt>Paiement</dt><dd>' + pay + '</dd></div>' +
                                (b.phone || b.email ? '<div class="col-12"><dt>Contact</dt><dd class="d-flex flex-wrap gap-2">' +
                                    (b.phone ? '<a class="btn btn-sm btn-light" href="tel:' + esc(b.phone) + '"><i class="ri-phone-line me-1"></i>' + esc(b.phone) + '</a>' +
                                        '<a class="btn btn-sm btn-light" target="_blank" rel="noopener" href="https://wa.me/' + esc(String(b.phone).replace(/\D/g, '')) + '"><i class="ri-whatsapp-line me-1 text-success"></i>WhatsApp</a>' : '') +
                                    (b.email ? '<a class="btn btn-sm btn-light" href="mailto:' + esc(b.email) + '"><i class="ri-mail-line me-1"></i>E-mail</a>' : '') +
                                '</dd></div>' : '') +
                                (b.special_requests ? '<div class="col-12"><dt>Demandes spéciales</dt><dd class="fw-normal">' + esc(b.special_requests) + '</dd></div>' : '') +
                            '</dl>';

                        actions.innerHTML =
                            (b.confirm_url
                                ? '<form method="POST" action="' + esc(b.confirm_url) + '" class="me-auto" data-confirm="Confirmer la réservation ' + esc(b.reference) + ' ?" data-confirm-icon="question" data-confirm-button="Confirmer">' +
                                    '<input type="hidden" name="_token" value="' + esc(csrf) + '"><input type="hidden" name="status" value="confirmed">' +
                                    '<button type="submit" class="btn btn-soft-success"><i class="ri-check-line me-1"></i>Confirmer</button></form>'
                                : '') +
                            '<button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>' +
                            '<a href="' + esc(b.show_url) + '" class="btn btn-primary">Ouvrir la fiche <i class="ri-arrow-right-line ms-1"></i></a>';
                    })
                    .catch(function () {
                        body.innerHTML = '<div class="alert alert-danger mb-0">Impossible de charger cette réservation. Réessayez.</div>';
                        actions.innerHTML = '<button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>';
                    });
            }
        })();
    </script>
@endsection
