@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i> Team Details: {{ $team->name }}
                    </h5>
                    <div>
                        <a href="{{ route('teams.edit', $team->team_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('teams.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>General Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Team Name</label>
                            <p class="fs-5">{{ $team->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Abbreviation</label>
                            <p class="fs-5"><span class="badge bg-secondary">{{ $team->short_name }}</span></p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Foundation Date</label>
                            <p class="fs-5">
                                @if($team->foundation_date)
                                    {{ \Carbon\Carbon::parse($team->foundation_date)->format('d M, Y') }}
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Country</label>
                            <p class="fs-5">
                                @if($team->country)
                                    <i class="fas fa-flag me-1 text-muted"></i> {{ $team->country->name }}
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Home Stadium</label>
                            <p class="fs-5">
                                @if($team->stadium)
                                    <i class="fas fa-landmark me-1 text-muted"></i> {{ $team->stadium->name }}
                                @else
                                    <span class="text-muted fst-italic">No Stadium Assigned</span>
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
                            <div class="fs-6">{{ $team->team_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($team->is_active)
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
                                {{ $team->creator->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $team->created_at ? $team->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $team->editor->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($team->updated_at)
                                        {{ $team->updated_at->format('d M Y, H:i') }}
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
                                {{ $team->destroyer->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($team->deleted_at)
                                        {{ $team->deleted_at->format('d M Y, H:i') }}
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