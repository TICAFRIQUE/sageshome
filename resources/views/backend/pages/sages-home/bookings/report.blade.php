@extends('backend.layouts.master')

@section('title', 'Compte d\'Exploitation - Rapports')

@section('css')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .page-title-box {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        body {
            margin: 0;
            padding: 15px;
        }
    }
    
    .residence-row {
        transition: all 0.2s ease;
    }
    
    .residence-row:hover {
        background-color: #f8f9fa !important;
        transform: scale(1.01);
    }
    
    .residence-row.selected {
        background-color: #e7f3ff !important;
        border-left: 4px solid #0d6efd;
    }
</style>
@endsection

@section('content')
<!-- start page title -->
<div class="row no-print">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">
                @if($filters['residence_id'])
                    <span class="text-primary">{{ $residences->find($filters['residence_id'])->name }}</span> - 
                @endif
                Compte d'Exploitation
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Réservations</a></li>
                    <li class="breadcrumb-item active">Rapport</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<!-- Filtres -->
<div class="row no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-filter-3-line me-1"></i> Filtres</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.bookings.report') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Date de début</label>
                            <input type="date" class="form-control" name="start_date" 
                                   value="{{ $filters['start_date'] }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date de fin</label>
                            <input type="date" class="form-control" name="end_date" 
                                   value="{{ $filters['end_date'] }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Résidence</label>
                            <select class="form-select" name="residence_id">
                                <option value="">Toutes les résidences</option>
                                @foreach($residences as $residence)
                                    <option value="{{ $residence->id }}" 
                                            {{ $filters['residence_id'] == $residence->id ? 'selected' : '' }}>
                                        {{ $residence->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="confirmed" {{ $filters['status'] == 'confirmed' ? 'selected' : '' }}>Confirmées</option>
                                <option value="pending" {{ $filters['status'] == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="cancelled" {{ $filters['status'] == 'cancelled' ? 'selected' : '' }}>Annulées</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-search-line me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.bookings.report') }}" class="btn btn-secondary">
                                <i class="ri-refresh-line me-1"></i> Réinitialiser
                            </a>
                            <button type="button" class="btn btn-success" onclick="window.print()">
                                <i class="ri-printer-line me-1"></i> Imprimer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- En-tête du rapport (visible à l'impression) -->
<div class="row d-none d-print-block mb-4">
    <div class="col-12 text-center">
        <h2>SAGES HOME</h2>
        <h4>Compte d'Exploitation</h4>
        <p class="mb-0">Période : {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</p>
        @if($filters['residence_id'])
            <p class="mb-0">Résidence : {{ $residences->find($filters['residence_id'])->name }}</p>
        @endif
        <hr>
    </div>
</div>

<!-- Statistiques globales -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                @if($filters['residence_id'])
                    <h5 class="card-title mb-0">
                        <i class="ri-pie-chart-line me-1"></i> 
                        <span class="text-primary">{{ $residences->find($filters['residence_id'])->name }}</span> - Statistiques Générales
                    </h5>
                @else
                    <h5 class="card-title mb-0"><i class="ri-pie-chart-line me-1"></i> Statistiques Générales</h5>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-primary mb-1">{{ $stats['total_bookings'] }}</h3>
                            <small class="text-muted">Réservations Totales</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-success mb-1">{{ $stats['confirmed_bookings'] }}</h3>
                            <small class="text-muted">Confirmées</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-warning mb-1">{{ $stats['pending_bookings'] }}</h3>
                            <small class="text-muted">En Attente</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-danger mb-1">{{ $stats['cancelled_bookings'] }}</h3>
                            <small class="text-muted">Annulées</small>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4 mb-3">
                        <div class="border border-success rounded p-3 text-center bg-success-subtle">
                            <h4 class="text-success mb-1">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</h4>
                            <small class="text-muted">Revenus Encaissés</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="border border-warning rounded p-3 text-center bg-warning-subtle">
                            <h4 class="text-warning mb-1">{{ number_format($stats['pending_revenue'], 0, ',', ' ') }} FCFA</h4>
                            <small class="text-muted">Revenus En Attente</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="border border-info rounded p-3 text-center bg-info-subtle">
                            <h4 class="text-info mb-1">{{ $stats['total_nights'] }}</h4>
                            <small class="text-muted">Nuitées Totales</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques par résidence -->
@if($residenceStats->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="ri-building-line me-1"></i> Performance par Résidence</h5>
                    <small class="text-muted">
                        <i class="ri-information-line me-1"></i>Cliquez sur une résidence pour voir ses détails
                    </small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Résidence</th>
                                <th class="text-center">Réservations</th>
                                <th class="text-center">Confirmées</th>
                                <th class="text-center">Nuitées</th>
                                <th class="text-end">Revenus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($residenceStats as $stat)
                            <tr class="residence-row {{ $filters['residence_id'] == $stat['residence']->id ? 'selected' : '' }}" 
                                style="cursor: pointer;" 
                                onclick="window.location.href='{{ route('admin.bookings.report', array_merge($filters, ['residence_id' => $stat['residence']->id])) }}'">
                                <td>
                                    <strong class="text-primary">
                                        <i class="ri-building-line me-1"></i>{{ $stat['residence']->name }}
                                    </strong>
                                    @if($filters['residence_id'] == $stat['residence']->id)
                                        <span class="badge bg-primary ms-1">Sélectionnée</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $stat['residence']->address }}</small>
                                </td>
                                <td class="text-center">{{ $stat['total_bookings'] }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $stat['confirmed_bookings'] }}</span>
                                </td>
                                <td class="text-center">{{ $stat['total_nights'] }}</td>
                                <td class="text-end">
                                    <strong>{{ number_format($stat['total_revenue'], 0, ',', ' ') }} FCFA</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-active">
                            <tr>
                                <th>TOTAL</th>
                                <th class="text-center">{{ $stats['total_bookings'] }}</th>
                                <th class="text-center">{{ $stats['confirmed_bookings'] }}</th>
                                <th class="text-center">{{ $stats['total_nights'] }}</th>
                                <th class="text-end">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Détails des réservations -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-list-check me-1"></i> 
                    @if($filters['residence_id'])
                        <span class="text-primary">{{ $residences->find($filters['residence_id'])->name }}</span> - 
                    @endif
                    Détails des Réservations
                </h5>
            </div>
            <div class="card-body">
                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Référence</th>
                                    <th>Client</th>
                                    <th>Résidence</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th class="text-center">Nuits</th>
                                    <th class="text-end">Montant</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center no-print">Paiement</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary no-print">
                                            {{ $booking->booking_number }}
                                        </a>
                                        <span class="d-none d-print-inline">{{ $booking->booking_number }}</span>
                                    </td>
                                    <td>{{ $booking->first_name }} {{ $booking->last_name }}</td>
                                    <td>{{ $booking->residence->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }}
                                    </td>
                                    <td class="text-end">{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-center">
                                        @switch($booking->status)
                                            @case('confirmed')
                                                <span class="badge bg-success">Confirmée</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">En attente</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Annulée</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center no-print">
                                        @if($booking->payments->where('status', 'completed')->count() > 0)
                                            <span class="badge bg-success">Payé</span>
                                        @elseif($booking->payments->where('status', 'pending')->count() > 0)
                                            <span class="badge bg-warning">En attente</span>
                                        @else
                                            <span class="badge bg-secondary">Non payé</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="ri-information-line me-2"></i> Aucune réservation trouvée pour cette période.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Pied de page pour l'impression -->
<div class="row d-none d-print-block mt-5">
    <div class="col-12">
        <hr>
        <div class="row">
            <div class="col-6">
                <p class="mb-0"><strong>Généré le :</strong> {{ now()->format('d/m/Y à H:i') }}</p>
            </div>
            <div class="col-6 text-end">
                <p class="mb-0"><strong>Signature :</strong> ____________________</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Configuration de l'impression
window.onbeforeprint = function() {
    document.title = 'Rapport_Exploitation_' + '{{ $filters["start_date"] }}_{{ $filters["end_date"] }}';
};
</script>
@endsection
