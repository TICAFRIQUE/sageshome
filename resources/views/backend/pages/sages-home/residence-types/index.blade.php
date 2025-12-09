@extends('backend.layouts.master')

@section('title', 'Types de Résidences')

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
            <h4 class="mb-sm-0">Types de Résidences</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sages-home.dashboard') }}">Sages Home</a></li>
                    <li class="breadcrumb-item active">Types de Résidences</li>
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
                        <h4 class="card-title mb-0">Gestion des Types de Résidences</h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.residence-types.create') }}" class="btn btn-success">
                            <i class="ri-add-line me-1"></i> Ajouter un Type
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($residenceTypes->count() > 0)
                    <div class="table-responsive">
                        <table id="buttons-datatables" class="display table table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Slug</th>
                                    <th>Capacité</th>
                                    <th>Résidences</th>
                                    <th>Statut</th>
                                    <th>Ordre</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($residenceTypes as $type)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $type->name }}</span>
                                    </td>
                                    <td>
                                        <code class="text-muted small">{{ $type->slug }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $type->formatted_capacity }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $type->residences_count }}</span>
                                    </td>
                                    <td>
                                        @if($type->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>{{ $type->sort_order }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.residence-types.show', $type) }}">
                                                    <i class="ri-eye-line align-bottom me-2 text-muted"></i> Voir
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.residence-types.edit', $type) }}">
                                                    <i class="ri-pencil-line align-bottom me-2 text-muted"></i> Modifier
                                                </a></li>
                                                @if($type->residences_count == 0)
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('admin.residence-types.destroy', $type) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-line align-bottom me-2"></i> Supprimer
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
                        <div class="mb-3">
                            <i class="ri-building-line display-1 text-muted"></i>
                        </div>
                        <h5>Aucun type de résidence trouvé</h5>
                        <p class="text-muted">Commencez par ajouter des types de résidences</p>
                        <a href="{{ route('admin.residence-types.create') }}" class="btn btn-success">
                            <i class="ri-add-line me-1"></i> Ajouter le premier type
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

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
@endsection
@endsection