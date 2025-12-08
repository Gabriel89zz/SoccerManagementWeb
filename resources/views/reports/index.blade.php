@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-dark"><i class="fas fa-chart-line me-2 text-primary"></i> Analytics & Reports</h2>
            <p class="text-muted">Accede a las vistas especializadas de la base de datos.</p>
        </div>
    </div>

    <div class="row g-4">
        @foreach($reports as $key => $report)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 hover-shadow transition-all">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light text-primary rounded p-3 me-3">
                            <i class="fas {{ $report['icon'] }} fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-0 text-dark">{{ $report['title'] }}</h5>
                    </div>
                    <p class="card-text text-muted small mb-4">
                        {{ $report['desc'] }}
                    </p>
                </div>
                <div class="card-footer bg-white border-0 pt-0 pb-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.show', $key) }}" class="btn btn-outline-primary fw-bold">
                            <i class="fas fa-eye me-2"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endsection