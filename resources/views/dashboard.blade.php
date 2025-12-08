@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    
    <!-- HERO SECTION -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-primary text-white shadow-sm overflow-hidden position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <h2 class="fw-bold mb-1">Welcome back, {{ Auth::user()->first_name ?? 'Manager' }}! 👋</h2>
                    <p class="mb-0 opacity-75">Overview of your league's performance and market activity.</p>
                </div>
                <i class="fas fa-futbol position-absolute text-white opacity-10" style="font-size: 10rem; right: -20px; top: -30px;"></i>
                <i class="fas fa-chart-line position-absolute text-white opacity-10" style="font-size: 8rem; right: 150px; bottom: -40px;"></i>
            </div>
        </div>
    </div>

    <!-- KPI CARDS -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Teams</h6>
                        <h2 class="mb-0 fw-bold text-dark">{{ $counts['teams'] }}</h2>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Active Players</h6>
                        <h2 class="mb-0 fw-bold text-dark">{{ $counts['players'] }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Scheduled Matches</h6>
                        <h2 class="mb-0 fw-bold text-dark">{{ $counts['matches_scheduled'] }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-1">Active Injuries</h6>
                        <h2 class="mb-0 fw-bold text-dark">{{ $counts['active_injuries'] }}</h2>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                        <i class="fas fa-user-injured fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <!-- COLUMNA IZQUIERDA -->
        <div class="col-lg-8">
            <!-- PRÓXIMOS PARTIDOS -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="card-title mb-0 fw-bold"><i class="fas fa-clock me-2 text-primary"></i> Upcoming Fixtures</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingMatches as $match)
                            <div class="list-group-item d-flex align-items-center justify-content-between py-3 border-light">
                                <div class="d-flex align-items-center flex-grow-1 justify-content-end text-end" style="width: 40%;">
                                    <span class="fw-bold me-2">{{ $match->homeTeam->name }}</span>
                                    <span class="badge bg-secondary">{{ $match->homeTeam->short_name }}</span>
                                </div>
                                <div class="mx-3 text-center" style="width: 20%;">
                                    <div class="badge bg-light text-dark border mb-1">
                                        {{ $match->match_date->format('H:i') }}
                                    </div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">{{ $match->match_date->format('d M') }}</div>
                                </div>
                                <div class="d-flex align-items-center flex-grow-1 justify-content-start" style="width: 40%;">
                                    <span class="badge bg-secondary me-2">{{ $match->awayTeam->short_name }}</span>
                                    <span class="fw-bold">{{ $match->awayTeam->name }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-5 text-muted">
                                <i class="far fa-calendar-times fa-3x mb-3 opacity-50"></i>
                                <p>No upcoming matches scheduled.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- GRÁFICOS ROW -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0">Player Positions Distribution</h6>
                        </div>
                        <div class="card-body d-flex justify-content-center">
                            <div style="height: 200px; width: 100%;">
                                <canvas id="positionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="fw-bold mb-0">Match Status Overview</h6>
                        </div>
                        <div class="card-body d-flex justify-content-center">
                            <div style="height: 200px; width: 100%;">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: MERCADO DE FICHAJES (NUEVO) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="fas fa-exchange-alt me-2"></i> Market Activity</h5>
                    <span class="badge bg-success bg-opacity-75 text-white animate__animated animate__pulse animate__infinite">Live</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($latestTransfers as $transfer)
                        <div class="list-group-item py-3 border-light">
                            <div class="d-flex align-items-center">
                                <!-- Icono de tipo -->
                                <div class="flex-shrink-0 text-center" style="width: 45px;">
                                    <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 35px; height: 35px;">
                                        @if($transfer->transfer_type == 'Loan')
                                            <i class="fas fa-clock" title="Loan"></i>
                                        @elseif($transfer->transfer_type == 'Free Agent')
                                            <i class="fas fa-file-signature" title="Free Agent"></i>
                                        @else
                                            <i class="fas fa-money-bill-wave" title="Transfer"></i>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Info -->
                                <div class="flex-grow-1 ms-2 overflow-hidden">
                                    <h6 class="mb-1 fw-bold text-truncate">{{ $transfer->player->full_name ?? 'Unknown' }}</h6>
                                    <div class="d-flex align-items-center small text-muted">
                                        <span class="text-danger fw-semibold text-truncate" style="max-width: 80px;">{{ $transfer->fromTeam->short_name ?? 'Free' }}</span>
                                        <i class="fas fa-long-arrow-alt-right mx-2"></i>
                                        <span class="text-success fw-semibold text-truncate" style="max-width: 80px;">{{ $transfer->toTeam->short_name ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <!-- Precio -->
                                <div class="text-end ms-2">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                        €{{ $transfer->transfer_fee_eur >= 1000000 ? number_format($transfer->transfer_fee_eur/1000000, 1) . 'M' : number_format($transfer->transfer_fee_eur/1000, 0) . 'K' }}
                                    </span>
                                    <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">
                                        {{ $transfer->transfer_date ? \Carbon\Carbon::parse($transfer->transfer_date)->diffForHumans() : '-' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="text-center p-5">
                                <div class="text-muted opacity-50 mb-2">
                                    <i class="fas fa-store-slash fa-3x"></i>
                                </div>
                                <p class="text-muted small">No recent market activity.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3 border-top-0">
                    <a href="{{ route('transfer-histories.index') }}" class="btn btn-sm btn-outline-dark w-100">
                        View All Transfers
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Gráfico de Posiciones (Donut)
        const posCtx = document.getElementById('positionChart').getContext('2d');
        // Validamos si hay datos para evitar errores en JS si las colecciones están vacías
        const posLabels = @json($playersByPosition->pluck('acronym'));
        const posData = @json($playersByPosition->pluck('total'));
        
        new Chart(posCtx, {
            type: 'doughnut',
            data: {
                labels: posLabels,
                datasets: [{
                    data: posData,
                    backgroundColor: ['#3498db', '#e74c3c', '#f1c40f', '#2ecc71', '#9b59b6'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, padding: 15 } }
                },
                layout: { padding: 10 }
            }
        });

        // 2. Gráfico de Partidos (Bar)
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusLabels = @json($matchesByStatus->pluck('match_status'));
        const statusData = @json($matchesByStatus->pluck('total'));

        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Count',
                    data: statusData,
                    backgroundColor: '#1abc9c',
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { display: true, drawBorder: false, color: '#f0f0f0' },
                        ticks: { stepSize: 1 } 
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection