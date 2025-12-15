@extends('backend.layouts.master')
@section('title')
    Tableau de bord
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                @auth
                                    <h4 class="fs-16 mb-1">Bonjour, {{ Auth::user()->username }} !</h4>
                                @endauth
                                <p class="text-muted mb-0">Voici ce qui se passe avec Sages Home aujourd'hui.</p>
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form action="javascript:void(0);">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-sm-auto">
                                            <div class="input-group input-group-lg">
                                                <input type="text"
                                                    class="form-control border-0 minimal-border shadow fs-5" id="horloge"
                                                    readonly>
                                                <input type="text"
                                                    class="form-control border-0 minimal-border shadow fs-5" id="date"
                                                    readonly>
                                                <div class="input-group-text bg-primary border-primary text-white">
                                                    <i class="ri-time-line me-2"></i>
                                                    <i class="ri-calendar-line"></i>
                                                </div>
                                            </div>
                                            <script>
                                                function mettreAJourHorloge() {
                                                    var maintenant = new Date();
                                                    var heures = maintenant.getHours().toString().padStart(2, '0');
                                                    var minutes = maintenant.getMinutes().toString().padStart(2, '0');
                                                    var secondes = maintenant.getSeconds().toString().padStart(2, '0');
                                                    document.getElementById('horloge').value = heures + ':' + minutes + ':' + secondes;

                                                    var options = {
                                                        weekday: 'long',
                                                        year: 'numeric',
                                                        month: 'long',
                                                        day: 'numeric'
                                                    };
                                                    var dateEnFrancais = maintenant.toLocaleDateString('fr-FR', options);
                                                    document.getElementById('date').value = dateEnFrancais;
                                                }

                                                setInterval(mettreAJourHorloge, 1000);
                                                mettreAJourHorloge(); // Appel initial pour afficher l'heure et la date immédiatement
                                            </script>
                                        </div>
                                        <!--end col-->

                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <!-- Statistiques Dashboard -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-white"
                            style="background: linear-gradient(135deg, #F2D18A, #C29B32) !important;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $totalResidences }}</h4>
                                        <p class="mb-0">Résidences</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-building fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card text-white"
                            style="background: linear-gradient(135deg, #F2D18A, #C29B32) !important;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $totalBookings }}</h4>
                                        <p class="mb-0">Réservations</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card text-white"
                            style="background: linear-gradient(135deg, #2F4A33, #1a7e20) !important;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0 text-white">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA
                                        </h4>
                                        <p class="mb-0">Revenus</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-coins fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques de revenus par période -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="fas fa-chart-line"></i> Revenus par période</h5>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-info border-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Aujourd'hui</p>
                                        <h4 class="mb-0">{{ number_format($revenueToday, 0, ',', ' ') }}</h4>
                                        <small class="text-muted">FCFA</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-day fa-2x text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-start border-primary border-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Cette semaine</p>
                                        <h4 class="mb-0">{{ number_format($revenueWeek, 0, ',', ' ') }}</h4>
                                        <small class="text-muted">FCFA</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-week fa-2x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-start border-warning border-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Ce mois</p>
                                        <h4 class="mb-0">{{ number_format($revenueMonth, 0, ',', ' ') }}</h4>
                                        <small class="text-muted">FCFA</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-alt fa-2x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-start border-success border-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Cette année</p>
                                        <h4 class="mb-0">{{ number_format($revenueYear, 0, ',', ' ') }}</h4>
                                        <small class="text-muted">FCFA</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar fa-2x text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenus par résidence -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#revenue-today" role="tab">
                                            <i class="fas fa-calendar-day"></i> Aujourd'hui
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#revenue-week" role="tab">
                                            <i class="fas fa-calendar-week"></i> Cette semaine
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#revenue-month"
                                            role="tab">
                                            <i class="fas fa-calendar-alt"></i> Ce mois
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#revenue-year" role="tab">
                                            <i class="fas fa-calendar"></i> Cette année
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <!-- Ce mois -->
                                    <div class="tab-pane fade show active" id="revenue-month" role="tabpanel">
                                        <h5 class="mb-3">Top Résidences - Ce Mois</h5>
                                        @if ($revenueByResidenceMonth->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Résidence</th>
                                                            <th class="text-end">Revenus</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($revenueByResidenceMonth as $index => $residence)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <i class="fas fa-building text-muted"></i>
                                                                    {{ $residence->name }}
                                                                </td>
                                                                <td class="text-end">
                                                                    <strong>{{ number_format($residence->total, 0, ',', ' ') }}
                                                                        FCFA</strong>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-active">
                                                            <td colspan="2"><strong>Total</strong></td>
                                                            <td class="text-end">
                                                                <strong>{{ number_format($revenueMonth, 0, ',', ' ') }}
                                                                    FCFA</strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center py-4">Aucun revenu ce mois.</p>
                                        @endif
                                    </div>

                                    <!-- Cette semaine -->
                                    <div class="tab-pane fade" id="revenue-week" role="tabpanel">
                                        <h5 class="mb-3">Top Résidences - Cette Semaine</h5>
                                        @if ($revenueByResidenceWeek->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Résidence</th>
                                                            <th class="text-end">Revenus</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($revenueByResidenceWeek as $index => $residence)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <i class="fas fa-building text-muted"></i>
                                                                    {{ $residence->name }}
                                                                </td>
                                                                <td class="text-end">
                                                                    <strong>{{ number_format($residence->total, 0, ',', ' ') }}
                                                                        FCFA</strong>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-active">
                                                            <td colspan="2"><strong>Total</strong></td>
                                                            <td class="text-end">
                                                                <strong>{{ number_format($revenueWeek, 0, ',', ' ') }}
                                                                    FCFA</strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center py-4">Aucun revenu cette semaine.</p>
                                        @endif
                                    </div>

                                    <!-- Aujourd'hui -->
                                    <div class="tab-pane fade" id="revenue-today" role="tabpanel">
                                        <h5 class="mb-3">Résidences - Aujourd'hui</h5>
                                        @if ($revenueByResidenceToday->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Résidence</th>
                                                            <th class="text-end">Revenus</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($revenueByResidenceToday as $index => $residence)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <i class="fas fa-building text-muted"></i>
                                                                    {{ $residence->name }}
                                                                </td>
                                                                <td class="text-end">
                                                                    <strong>{{ number_format($residence->total, 0, ',', ' ') }}
                                                                        FCFA</strong>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-active">
                                                            <td colspan="2"><strong>Total</strong></td>
                                                            <td class="text-end">
                                                                <strong>{{ number_format($revenueToday, 0, ',', ' ') }}
                                                                    FCFA</strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center py-4">Aucun revenu aujourd'hui.</p>
                                        @endif
                                    </div>

                                    <!-- Cette année -->
                                    <div class="tab-pane fade" id="revenue-year" role="tabpanel">
                                        <h5 class="mb-3">Top Résidences - Cette Année</h5>
                                        @if ($revenueByResidenceYear->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Résidence</th>
                                                            <th class="text-end">Revenus</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($revenueByResidenceYear as $index => $residence)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    <i class="fas fa-building text-muted"></i>
                                                                    {{ $residence->name }}
                                                                </td>
                                                                <td class="text-end">
                                                                    <strong>{{ number_format($residence->total, 0, ',', ' ') }}
                                                                        FCFA</strong>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-active">
                                                            <td colspan="2"><strong>Total</strong></td>
                                                            <td class="text-end">
                                                                <strong>{{ number_format($revenueYear, 0, ',', ' ') }}
                                                                    FCFA</strong>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center py-4">Aucun revenu cette année.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-clock"></i> Réservations récentes</h5>
                            </div>
                            <div class="card-body">
                                @if ($recentBookings->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Client</th>
                                                    <th>Résidence</th>
                                                    <th>Dates</th>
                                                    <th>Statut</th>
                                                    <th>Montant</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($recentBookings as $booking)
                                                    <tr style="cursor: pointer;"
                                                        onclick="window.location='{{ route('admin.bookings.show', $booking->id) }}'">
                                                        <td>{{ $booking->first_name }} {{ $booking->last_name }}</td>
                                                        <td>{{ $booking->residence->name }}</td>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m') }}
                                                            -
                                                            {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                                                {{ ucfirst($booking->status) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">Aucune réservation récente.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-pie"></i> Statut des réservations</h5>
                            </div>
                            <div class="card-body">
                                @if ($bookingStats->count() > 0)
                                    @foreach ($bookingStats as $stat)
                                        <div class="d-flex justify-content-between align-items-center mb-2"
                                            style="cursor: pointer;"
                                            onclick="window.location='{{ route('admin.bookings.index', ['status' => $stat->status]) }}'">
                                            <span>
                                              
                                                
                                                {{ ucfirst($stat->status === 'confirmed' ? 'Confirmées' : ($stat->status === 'pending' ? 'En attente' : 'Annulées') ) }}
                                            
                                            </span>
                                            <span class="badge bg-primary {{ $stat->status === 'confirmed' ? 'bg-success' : ($stat->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">{{ $stat->count }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">Aucune donnée disponible.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- end .h-100-->

        </div> <!-- end col -->


    </div>
@endsection
@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/maps/world-merc.js') }}"></script>
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <!-- dashboard init -->
    <script src="{{ URL::asset('build/js/pages/dashboard-ecommerce.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>




    {{-- <script>
        var options = {
            series: [{
                name: "Revenu",
                data: @json($data)
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            xaxis: {
                categories: @json($labels)
            }
        };

        var chart = new ApexCharts(document.querySelector("#revenuChart"), options);
        chart.render();
    </script> --}}


    {{-- <script>
        var options = {
            series: [{
                name: "Revenu",
                data: @json($data)
            }],
            chart: {
                type: 'bar', // Changer 'line' en 'bar'
                height: 350
            },
            // plotOptions: {
            //     bar: {
            //         borderRadius: 4,
            //         borderRadiusApplication: 'end',
            //         horizontal: true,
            //     }
            // },
            xaxis: {
                categories: @json($labels), // Affichage des mois en texte
                title: {
                    text: "Mois"
                }
            },
            yaxis: {
                title: {
                    text: "Revenu"
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#revenuChart"), options);
        chart.render();
    </script> --}}
@endsection
