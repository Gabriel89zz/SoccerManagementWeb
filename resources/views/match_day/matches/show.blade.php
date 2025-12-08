@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-futbol me-2"></i> Match Details
                    </h5>
                    <div>
                        <a href="{{ route('matches.edit', $match->match_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('matches.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- SCOREBOARD SECTION -->
                    <div class="text-center mb-5 p-4 bg-light rounded border">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-end">
                                <h3 class="fw-bold mb-0">{{ $match->homeTeam->name ?? 'Home' }}</h3>
                                @if($match->homeTeam && $match->homeTeam->short_name)
                                    <span class="badge bg-secondary">{{ $match->homeTeam->short_name }}</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="display-4 fw-bold">
                                    {{ $match->home_score ?? '-' }} : {{ $match->away_score ?? '-' }}
                                </div>
                                <div class="badge {{ $match->match_status == 'Finished' ? 'bg-secondary' : ($match->match_status == 'Live' ? 'bg-danger animate__animated animate__pulse animate__infinite' : 'bg-success') }} mt-2 fs-6">
                                    {{ $match->match_status }}
                                </div>
                            </div>
                            <div class="col-md-4 text-start">
                                <h3 class="fw-bold mb-0">{{ $match->awayTeam->name ?? 'Away' }}</h3>
                                @if($match->awayTeam && $match->awayTeam->short_name)
                                    <span class="badge bg-secondary">{{ $match->awayTeam->short_name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Match Info</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Date & Time</label>
                            <p class="fs-5">
                                @if($match->match_date)
                                    {{ $match->match_date->format('d M, Y') }} <br>
                                    <span class="text-muted small">{{ $match->match_date->format('H:i') }}</span>
                                @else
                                    <span class="text-muted">TBD</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Competition / Stage</label>
                            <p class="fs-5">
                                @if($match->stage && $match->stage->competitionSeason && $match->stage->competitionSeason->competition)
                                    {{ $match->stage->competitionSeason->competition->name }} <br>
                                    <span class="text-muted small">{{ $match->stage->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Stadium</label>
                            <p class="fs-5">
                                @if($match->stadium)
                                    {{ $match->stadium->name }}
                                    @if($match->stadium->city)
                                        <br><span class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>{{ $match->stadium->city->name }}</span>
                                    @endif
                                @else
                                    <span class="text-muted small">Not Assigned</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Attendance</label>
                            <p class="fs-5">
                                @if($match->attendance)
                                    <i class="fas fa-users me-2 text-muted"></i> {{ number_format($match->attendance) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- 2. AUDIT DATA (Restricted to Admin) -->
                    @if(Auth::check() && Auth::user()->isAdmin())
                    <h6 class="text-danger border-bottom pb-2 mt-4"><i class="fas fa-lock me-2"></i>Audit Data</h6>
                    <div class="row bg-light p-3 rounded">
                        <!-- Fila 1: ID, Estado, Creador -->
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Internal ID</label>
                            <div class="fs-6">{{ $match->match_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($match->is_active)
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
                                {{ $match->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $match->created_at ? $match->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $match->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($match->updated_at)
                                        {{ $match->updated_at->format('d M Y, H:i') }}
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
                                {{ $match->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($match->deleted_at)
                                        {{ $match->deleted_at->format('d M Y, H:i') }}
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