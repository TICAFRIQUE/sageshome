@extends('backend.layouts.master')

@section('title', 'Gestion des Réservations')

@section('css')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Gestion des Réservations</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item active">Réservations</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title mb-0">Liste des Réservations</h4>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="statusFilter">
                                <option value="">Tous les statuts</option>
                                <option value="pending">En attente</option>
                                <option value="confirmed">Confirmées</option>
                                <option value="cancelled">Annulées</option>
                            </select>
                            <a href="{{ route('admin.bookings.calendar') }}" class="btn btn-info btn-sm">
                                <i class="ri-calendar-line"></i> Calendrier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Client</th>
                                    <th>Résidence</th>
                                    <th>Dates</th>
                                    <th>Durée</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Paiement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $booking->id }}</strong>
                                        <br><small class="text-muted">{{ $booking->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <h6 class="mb-1">{{ $booking->first_name }} {{ $booking->last_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-mail-line"></i> {{ $booking->email }}
                                            <br><i class="ri-phone-line"></i> {{ $booking->phone }}
                                        </p>
                                    </td>
                                    <td>
                                        <h6 class="mb-1">{{ $booking->residence->name }}</h6>
                                        <p class="text-muted mb-0 small">{{ $booking->residence->location }}</p>
                                    </td>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</strong>
                                        <br>
                                        <span class="text-muted">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $booking->nights }} nuit(s)</span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</strong>
                                        @if($booking->guests > 1)
                                            <br><small class="text-muted">{{ $booking->guests }} personnes</small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($booking->status)
                                            @case('pending')
                                                <span class="badge bg-warning">En attente</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge bg-success">Confirmée</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Annulée</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($booking->payment)
                                            @switch($booking->payment->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">En attente</span>
                                                    <br><small>{{ ucfirst($booking->payment->method) }}</small>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">Payé</span>
                                                    <br><small>{{ ucfirst($booking->payment->method) }}</small>
                                                    @break
                                                @case('failed')
                                                    <span class="badge bg-danger">Échec</span>
                                                    <br><small>{{ ucfirst($booking->payment->method) }}</small>
                                                    @break
                                            @endswitch
                                        @else
                                            <span class="badge bg-secondary">Aucun paiement</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.bookings.show', $booking) }}">
                                                    <i class="ri-eye-line align-bottom me-2 text-muted"></i> Voir détails
                                                </a></li>
                                                
                                                @if($booking->status === 'pending')
                                                <li>
                                                    <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="ri-check-line align-bottom me-2"></i> Confirmer
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                                
                                                @if($booking->payment && $booking->payment->status === 'pending')
                                                <li>
                                                    <form action="{{ route('admin.bookings.confirm-payment', $booking) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-info">
                                                            <i class="ri-money-dollar-circle-line align-bottom me-2"></i> Confirmer paiement
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                                
                                                @if($booking->status !== 'cancelled')
                                                <li class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                                        @csrf
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="ri-close-line align-bottom me-2"></i> Annuler
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="avatar-md mx-auto mb-4">
                            <div class="avatar-title bg-light rounded-circle">
                                <i class="ri-calendar-check-line fs-24"></i>
                            </div>
                        </div>
                        <h5>Aucune réservation trouvée</h5>
                        <p class="text-muted">Les réservations apparaîtront ici lorsque les clients effectueront des bookings.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary" target="_blank">
                            <i class="ri-external-link-line me-1"></i> Voir le site public
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($bookings->count() > 0)
<!-- Statistiques rapides -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">En attente</p>
                        <h4 class="mb-0">{{ $bookings->where('status', 'pending')->count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded">
                            <div class="avatar-title bg-warning-subtle text-warning rounded">
                                <i class="ri-time-line fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Confirmées</p>
                        <h4 class="mb-0">{{ $bookings->where('status', 'confirmed')->count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded">
                            <div class="avatar-title bg-success-subtle text-success rounded">
                                <i class="ri-check-line fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Annulées</p>
                        <h4 class="mb-0">{{ $bookings->where('status', 'cancelled')->count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded">
                            <div class="avatar-title bg-danger-subtle text-danger rounded">
                                <i class="ri-close-line fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Total Revenus</p>
                        <h4 class="mb-0">{{ number_format($bookings->where('status', 'confirmed')->sum('total_amount'), 0, ',', ' ') }} FCFA</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded">
                            <div class="avatar-title bg-info-subtle text-info rounded">
                                <i class="ri-money-dollar-circle-line fs-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ URL::asset('build/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Attendre que DataTable soit complètement initialisé
            setTimeout(function() {
                // Récupérer le paramètre status de l'URL
                const urlParams = new URLSearchParams(window.location.search);
                const statusParam = urlParams.get('status');
                
                // Récupérer l'instance DataTable existante
                var table = $('#buttons-datatables').DataTable();
                
                // Si un statut est dans l'URL, filtrer automatiquement
                if (statusParam) {
                    $('#statusFilter').val(statusParam);
                    // Recherche dans la colonne Statut (index 6)
                    // Utiliser une recherche exacte sur le texte du badge
                    var searchTerm = '';
                    switch(statusParam) {
                        case 'pending':
                            searchTerm = 'En attente';
                            break;
                        case 'confirmed':
                            searchTerm = 'Confirmée';
                            break;
                        case 'cancelled':
                            searchTerm = 'Annulée';
                            break;
                    }
                    if (searchTerm) {
                        table.column(6).search(searchTerm).draw();
                    }
                }
                
                // Gérer le changement de filtre
                $('#statusFilter').on('change', function() {
                    var status = $(this).val();
                    var searchTerm = '';
                    switch(status) {
                        case 'pending':
                            searchTerm = 'En attente';
                            break;
                        case 'confirmed':
                            searchTerm = 'Confirmée';
                            break;
                        case 'cancelled':
                            searchTerm = 'Annulée';
                            break;
                        default:
                            searchTerm = '';
                    }
                    table.column(6).search(searchTerm).draw();
                });
            }, 500);
        });
    </script>
@endsection
@endsection