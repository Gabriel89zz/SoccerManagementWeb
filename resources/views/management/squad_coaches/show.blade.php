@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i> Coach Assignment Details
                    </h5>
                    <div>
                        <a href="{{ route('squad-coaches.edit', $squadCoach->squad_coach_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('squad-coaches.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Assignment Overview</h6>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="fw-bold text-muted small">Coach Name</label>
                            <p class="fs-5">{{ $squadCoach->coach->full_name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Team</label>
                            <p class="fs-5">{{ $squadCoach->squad->team->name ?? 'Unknown Team' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Season</label>
                            <p class="fs-5">
                                <span class="badge bg-info text-dark">{{ $squadCoach->squad->season->name ?? 'N/A' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Start Date</label>
                            <p class="fs-5">{{ \Carbon\Carbon::parse($squadCoach->start_date)->format('d M, Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">End Date</label>
                            <p class="fs-5">
                                @if($squadCoach->end_date)
                                    {{ \Carbon\Carbon::parse($squadCoach->end_date)->format('d M, Y') }}
                                @else
                                    <span class="text-muted">Ongoing</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Status</label>
                            <p class="fs-5">
                                @if($squadCoach->end_date && \Carbon\Carbon::parse($squadCoach->end_date)->isPast())
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    <span class="badge bg-success">Active</span>
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
                            <div class="fs-6">{{ $squadCoach->squad_coach_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($squadCoach->is_active)
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
                                {{ $squadCoach->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $squadCoach->created_at ? $squadCoach->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $squadCoach->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($squadCoach->updated_at)
                                        {{ $squadCoach->updated_at->format('d M Y, H:i') }}
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
                                {{ $squadCoach->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($squadCoach->deleted_at)
                                        {{ $squadCoach->deleted_at->format('d M Y, H:i') }}
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