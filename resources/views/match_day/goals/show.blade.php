@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-futbol me-2"></i> Goal Details
                    </h5>
                    <div>
                        <a href="{{ route('goals.edit', $goal->goal_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('goals.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Goal Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Scorer</label>
                            <p class="fs-5">{{ $goal->scorer->full_name ?? 'Unknown Player' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Team</label>
                            <p class="fs-5">{{ $goal->team->name ?? 'Unknown Team' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Match Minute</label>
                            <p class="fs-1 fw-bold text-success">{{ $goal->minute }}'</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Assisted By</label>
                            <p class="fs-5">
                                @if($goal->assistant)
                                    <i class="fas fa-hands-helping me-1 text-muted"></i> {{ $goal->assistant->full_name }}
                                @else
                                    <span class="text-muted small">No Assist / Solo</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Details</label>
                            <p class="fs-5">
                                <span class="badge bg-light text-dark border">{{ $goal->body_part ?? 'N/A' }}</span>
                                @if($goal->is_penalty)
                                    <span class="badge bg-warning text-dark ms-1">Penalty</span>
                                @endif
                                @if($goal->is_own_goal)
                                    <span class="badge bg-danger ms-1">Own Goal</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Match Context</label>
                            <p class="fs-6">
                                @if($goal->match)
                                    {{ $goal->match->homeTeam->name ?? '?' }} <span class="text-muted">vs</span> {{ $goal->match->awayTeam->name ?? '?' }}
                                    <br>
                                    <span class="text-muted small">
                                        {{ $goal->match->match_date ? $goal->match->match_date->format('d M Y') : 'TBD' }}
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
                            <div class="fs-6">{{ $goal->goal_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($goal->is_active)
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
                                {{ $goal->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $goal->created_at ? $goal->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $goal->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($goal->updated_at)
                                        {{ $goal->updated_at->format('d M Y, H:i') }}
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
                                {{ $goal->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($goal->deleted_at)
                                        {{ $goal->deleted_at->format('d M Y, H:i') }}
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