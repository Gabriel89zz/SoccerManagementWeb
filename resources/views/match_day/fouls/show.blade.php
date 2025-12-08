@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i> Foul Details
                    </h5>
                    <div>
                        <a href="{{ route('fouls.edit', $foul->foul_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('fouls.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Foul Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Offender</label>
                            <p class="fs-5 text-danger">{{ $foul->offender->full_name ?? 'Unknown Player' }}</p>
                            <span class="text-muted small">Team: {{ $foul->foulingTeam->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Victim</label>
                            <p class="fs-5">{{ $foul->victim->full_name ?? 'None' }}</p>
                            <span class="text-muted small">Team: {{ $foul->fouledTeam->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Match Minute</label>
                            <p class="fs-1 fw-bold text-dark">{{ $foul->minute }}'</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Outcome</label>
                            <p class="fs-5">
                                @if($foul->is_penalty_kick)
                                    <span class="badge bg-danger">Penalty Kick Awarded</span>
                                @else
                                    <span class="badge bg-light text-dark border">Standard Free Kick</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Match Context</label>
                            <p class="fs-6">
                                @if($foul->match)
                                    {{ $foul->match->homeTeam->name ?? '?' }} <span class="text-muted">vs</span> {{ $foul->match->awayTeam->name ?? '?' }}
                                    <br>
                                    <span class="text-muted small">
                                        {{ $foul->match->match_date ? $foul->match->match_date->format('d M, Y') : 'TBD' }}
                                    </span>
                                @else
                                    <span class="text-muted">Match Not Found</span>
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
                            <div class="fs-6">{{ $foul->foul_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($foul->is_active)
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
                                {{ $foul->creator->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $foul->created_at ? $foul->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $foul->editor->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($foul->updated_at)
                                        {{ $foul->updated_at->format('d M Y, H:i') }}
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
                                {{ $foul->editor->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($foul->deleted_at)
                                        {{ $foul->deleted_at->format('d M Y, H:i') }}
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