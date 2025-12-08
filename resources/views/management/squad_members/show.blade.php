@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tag me-2"></i> Squad Member Details
                    </h5>
                    <div>
                        <a href="{{ route('squad-members.edit', $member->squad_member_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('squad-members.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Member Overview</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Player Name</label>
                            <p class="fs-5">{{ $member->player->full_name ?? 'Unknown' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Team</label>
                            <p class="fs-5">{{ $member->squad->team->name ?? 'Unknown Team' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Season</label>
                            <p class="fs-5">
                                <span class="badge bg-info text-dark">{{ $member->squad->season->name ?? 'N/A' }}</span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Jersey Number</label>
                            <p class="fs-5">
                                @if($member->jersey_number)
                                    <span class="badge bg-light text-dark border fs-5">#{{ $member->jersey_number }}</span>
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Membership Status</label>
                            <p class="fs-5">
                                @if($member->leave_date && \Carbon\Carbon::parse($member->leave_date)->isPast())
                                    <span class="badge bg-danger">Former Player</span>
                                    <small class="d-block text-muted">Left: {{ \Carbon\Carbon::parse($member->leave_date)->format('d M, Y') }}</small>
                                @else
                                    <span class="badge bg-success">Active Player</span>
                                    <small class="d-block text-muted">Since: {{ \Carbon\Carbon::parse($member->join_date)->format('d M, Y') }}</small>
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
                            <div class="fs-6">{{ $member->squad_member_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($member->is_active)
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
                                {{ $member->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $member->created_at ? $member->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $member->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($member->updated_at)
                                        {{ $member->updated_at->format('d M Y, H:i') }}
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
                                {{ $member->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($member->deleted_at)
                                        {{ $member->deleted_at->format('d M Y, H:i') }}
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