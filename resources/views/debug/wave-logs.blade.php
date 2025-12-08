@extends('layouts.app')

@section('title', 'Diagnostic Wave Payment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-bug me-2"></i>Diagnostic du paiement Wave</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Pour consulter les logs détaillés :</strong>
                        <ol class="mb-0 mt-2">
                            <li>Ouvrez votre terminal</li>
                            <li>Naviguez vers le dossier du projet : <code>cd c:\laragon\www\sci_sage\sageshome2</code></li>
                            <li>Exécutez : <code>php artisan tail</code> ou consultez <code>storage/logs/laravel.log</code></li>
                        </ol>
                    </div>

                    @if($logs)
                        <h5 class="mt-4">Derniers logs Wave :</h5>
                        <div class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">
                            <pre class="mb-0 text-light">{{ $logs }}</pre>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Aucun log disponible pour le moment.
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
