@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i> Substitution Details
                    </h5>
                    <div>
                        <a href="{{ route('substitutions.edit', $substitution->substitution_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('substitutions.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Substitution Info</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Team</label>
                            <p class="fs-5">{{ $substitution->team->name ?? 'Unknown Team' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Match Minute</label>
                            <p class="fs-1 fw-bold text-dark">{{ $substitution->minute }}'</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Reason</label>
                            <p class="fs-5">
                                {{ $substitution->reason ?? 'Tactical' }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 border-end">
                            <label class="fw-bold text-danger small"><i class="fas fa-arrow-down me-1"></i> OUT (Leaving)</label>
                            <p class="fs-4 text-danger">
                                {{ $substitution->playerOut->full_name ?? 'Unknown' }}
                            </p>
                        </div>
                        <div class="col-md-6 ps-4">
                            <label class="fw-bold text-success small"><i class="fas fa-arrow-up me-1"></i> IN (Entering)</label>
                            <p class="fs-4 text-success">
                                {{ $substitution->playerIn->full_name ?? 'Unknown' }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="fw-bold text-muted small">Match Context</label>
                            <p class="fs-6">
                                @if($substitution->match)
                                    {{ $substitution->match->homeTeam->name ?? '?' }} <span class="text-muted">vs</span> {{ $substitution->match->awayTeam->name ?? '?' }}
                                    <br>
                                    <span class="text-muted small">
                                        {{ $substitution->match->match_date ? $substitution->match->match_date->format('d M, Y') : 'TBD' }}
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
                            <div class="fs-6">{{ $substitution->substitution_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($substitution->is_active)
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
                                {{ $substitution->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $substitution->created_at ? $substitution->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $substitution->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($substitution->updated_at)
                                        {{ $substitution->updated_at->format('d M Y, H:i') }}
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
                                {{ $substitution->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($substitution->deleted_at)
                                        {{ $substitution->deleted_at->format('d M Y, H:i') }}
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