@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i> Season Stats Details
                    </h5>
                    <div>
                        <a href="{{ route('player-season-stats.edit', $stat->player_season_stat_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('player-season-stats.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-id-card-alt me-2"></i>Context Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Player Name</label>
                            <p class="fs-5">{{ $stat->player->full_name ?? 'Unknown' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Team</label>
                            <p class="fs-5">{{ $stat->team->name ?? 'Unknown Team' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Competition / Season</label>
                            <p class="fs-6">
                                @if($stat->competitionSeason && $stat->competitionSeason->competition)
                                    {{ $stat->competitionSeason->competition->name }} <br>
                                    <span class="text-muted small">({{ $stat->competitionSeason->season->name ?? '-' }})</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- 2. PERFORMANCE -->
                    <h6 class="text-primary border-bottom pb-2 mt-4"><i class="fas fa-running me-2"></i>Performance Data</h6>
                    <div class="row mb-4">
                        <div class="col-md-3 text-center border-end">
                            <label class="fw-bold text-muted small">Matches</label>
                            <p class="display-6">{{ $stat->matches_played }}</p>
                        </div>
                        <div class="col-md-3 text-center border-end">
                            <label class="fw-bold text-muted small">Minutes</label>
                            <p class="display-6">{{ number_format($stat->minutes_played) }}'</p>
                        </div>
                        <div class="col-md-3 text-center border-end">
                            <label class="fw-bold text-muted small text-success">Goals</label>
                            <p class="display-6 text-success">{{ $stat->goals }}</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <label class="fw-bold text-muted small text-info">Assists</label>
                            <p class="display-6 text-info">{{ $stat->assists }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                         <div class="col-md-4">
                            <label class="fw-bold text-muted small">Shots on Target</label>
                            <p class="fs-5">{{ $stat->shots_on_target }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Yellow Cards</label>
                            <p class="fs-5 text-warning fw-bold">{{ $stat->yellow_cards }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Red Cards</label>
                            <p class="fs-5 text-danger fw-bold">{{ $stat->red_cards }}</p>
                        </div>
                    </div>

                    <!-- 3. AUDIT DATA (Restricted to Admin) -->
                    @if(Auth::check() && Auth::user()->isAdmin())
                    <h6 class="text-danger border-bottom pb-2 mt-4"><i class="fas fa-lock me-2"></i>Audit Data</h6>
                    <div class="row bg-light p-3 rounded">
                        <!-- Fila 1: ID, Estado, Creador -->
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Internal ID</label>
                            <div class="fs-6">{{ $stat->player_season_stat_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($stat->is_active)
                                    <span class="badge bg-success">Active Record</span>
                                @else
                                    <span class="badge bg-danger">Soft Deleted</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Created By</label>
                            <div>
                                <i class="fas fa-user-edit text-muted me-1"></i>
                                {{ $stat->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $stat->created_at ? $stat->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $stat->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($stat->updated_at)
                                        {{ $stat->updated_at->format('d M Y, H:i') }}
                                    @else
                                        <span class="fst-italic text-secondary">Never Updated</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted By</label>
                            <div>
                                <i class="fas fa-trash text-muted me-1"></i>
                                {{ $stat->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($stat->deleted_at)
                                        {{ $stat->deleted_at->format('d M Y, H:i') }}
                                    @else
                                        <span class="fst-italic text-secondary">-</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection