@extends('backend.layouts.master')

@section('title', 'Calendrier des Réservations')

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Calendrier des Réservations</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item active">Calendrier</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <!-- Filtres -->
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-3">
                        <select class="form-select" id="residence-filter">
                            <option value="">Toutes les résidences</option>
                            @foreach($residences as $residence)
                                <option value="{{ $residence->id }}">{{ $residence->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select" id="status-filter">
                            <option value="">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="confirmed">Confirmées</option>
                            <option value="cancelled">Annulées</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <input type="date" class="form-control" id="start-date" value="{{ date('Y-m-01') }}">
                            <input type="date" class="form-control" id="end-date" value="{{ date('Y-m-t') }}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="button" class="btn btn-primary" onclick="loadCalendar()">
                            <i class="ri-search-line me-1"></i> Filtrer
                        </button>
                    </div>
                    <div class="col-lg-2">
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                Vue
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="changeView('month')">Mensuelle</a></li>
                                <li><a class="dropdown-item" href="#" onclick="changeView('week')">Hebdomadaire</a></li>
                                <li><a class="dropdown-item" href="#" onclick="changeView('day')">Quotidienne</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Légende -->
        <div class="card">
            <div class="card-body py-2">
                <div class="row text-center">
                    <div class="col-auto">
                        <span class="badge bg-warning me-1"></span>
                        <small>En attente</small>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-success me-1"></span>
                        <small>Confirmées</small>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-danger me-1"></span>
                        <small>Annulées</small>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-info me-1"></span>
                        <small>Check-in aujourd'hui</small>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-secondary me-1"></span>
                        <small>Check-out aujourd'hui</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendrier -->
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les détails de réservation -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la réservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="booking-details">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>

<script>
let calendar;
let currentView = 'dayGridMonth';

document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    loadCalendar();
});

function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: currentView,
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour'
        },
        height: 'auto',
        eventClick: function(info) {
            showBookingDetails(info.event.id);
        },
        eventDisplay: 'block',
        dayMaxEvents: 3,
        moreLinkClick: 'popover'
    });

    calendar.render();
}

function loadCalendar() {
    const residenceId = document.getElementById('residence-filter').value;
    const status = document.getElementById('status-filter').value;
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    // Construire l'URL avec les paramètres
    const params = new URLSearchParams();
    if (residenceId) params.append('residence_id', residenceId);
    if (status) params.append('status', status);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);

    fetch(`{{ route('admin.bookings.calendar-data') }}?${params}`)
        .then(response => response.json())
        .then(data => {
            // Effacer les événements existants
            calendar.removeAllEvents();
            
            // Ajouter les nouveaux événements
            data.forEach(booking => {
                calendar.addEvent({
                    id: booking.id,
                    title: `${booking.residence_name} - ${booking.guest_name}`,
                    start: booking.check_in_date,
                    end: booking.check_out_date,
                    backgroundColor: getStatusColor(booking.status),
                    borderColor: getStatusColor(booking.status),
                    extendedProps: {
                        status: booking.status,
                        residence: booking.residence_name,
                        guest: booking.guest_name,
                        amount: booking.total_amount
                    }
                });
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement du calendrier:', error);
        });
}

function getStatusColor(status) {
    switch(status) {
        case 'pending':
            return '#ffc107'; // warning
        case 'confirmed':
            return '#198754'; // success
        case 'cancelled':
            return '#dc3545'; // danger
        default:
            return '#6c757d'; // secondary
    }
}

function changeView(view) {
    let calendarView;
    switch(view) {
        case 'month':
            calendarView = 'dayGridMonth';
            break;
        case 'week':
            calendarView = 'timeGridWeek';
            break;
        case 'day':
            calendarView = 'timeGridDay';
            break;
    }
    
    calendar.changeView(calendarView);
    currentView = calendarView;
}

function showBookingDetails(bookingId) {
    const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
    const modalBody = document.getElementById('booking-details');
    
    // Afficher le spinner
    modalBody.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Charger les détails
    fetch(`{{ route('admin.bookings.index') }}/${bookingId}/quick-view`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informations générales</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Référence:</strong></td>
                                    <td>${data.booking.reference}</td>
                                </tr>
                                <tr>
                                    <td><strong>Résidence:</strong></td>
                                    <td>${data.booking.residence_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Client:</strong></td>
                                    <td>${data.booking.guest_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Téléphone:</strong></td>
                                    <td><a href="tel:${data.booking.phone}">${data.booking.phone}</a></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><a href="mailto:${data.booking.email}">${data.booking.email}</a></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Détails du séjour</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Entrée du client:</strong></td>
                                    <td>${new Date(data.booking.check_in_date).toLocaleDateString('fr-FR')}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sortie du client:</strong></td>
                                    <td>${new Date(data.booking.check_out_date).toLocaleDateString('fr-FR')}</td>
                                </tr>
                                <tr>
                                    <td><strong>Voyageurs:</strong></td>
                                    <td>${data.booking.guests_count}</td>
                                </tr>
                                <tr>
                                    <td><strong>Montant:</strong></td>
                                    <td><strong>${new Intl.NumberFormat('fr-FR').format(data.booking.total_amount)} FCFA</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Statut:</strong></td>
                                    <td>
                                        <span class="badge bg-${data.booking.status === 'confirmed' ? 'success' : data.booking.status === 'pending' ? 'warning' : 'danger'}">
                                            ${data.booking.status === 'confirmed' ? 'Confirmée' : data.booking.status === 'pending' ? 'En attente' : 'Annulée'}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.bookings.index') }}/${bookingId}" class="btn btn-primary btn-sm">
                            <i class="ri-eye-line me-1"></i> Voir les détails complets
                        </a>
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        Erreur lors du chargement des détails de la réservation.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    Erreur lors du chargement des détails de la réservation.
                </div>
            `;
        });
}

// Charger automatiquement au changement des filtres
document.getElementById('residence-filter').addEventListener('change', loadCalendar);
document.getElementById('status-filter').addEventListener('change', loadCalendar);
</script>

<style>
/* Personnalisation du calendrier */
.fc-toolbar-title {
    font-size: 1.25rem !important;
    font-weight: 600 !important;
}

.fc-button {
    background-color: #405189 !important;
    border-color: #405189 !important;
}

.fc-button:hover {
    background-color: #364574 !important;
    border-color: #364574 !important;
}

.fc-button:focus {
    box-shadow: 0 0 0 0.2rem rgba(64, 81, 137, 0.25) !important;
}

.fc-event {
    font-size: 0.875rem;
    border-radius: 0.25rem;
    padding: 2px 4px;
}

.fc-event-title {
    font-weight: 500;
}

.fc-more-link {
    color: #405189 !important;
}

.fc-popover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.fc-day-today {
    background-color: rgba(64, 81, 137, 0.1) !important;
}
</style>
@endsection