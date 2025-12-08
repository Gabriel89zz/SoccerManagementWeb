@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i> Agent Details: {{ $agent->first_name }} {{ $agent->last_name }}
                    </h5>
                    <div>
                        <a href="{{ route('agents.edit', $agent->agent_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('agents.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Personal Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Full Name</label>
                            <p class="fs-5">{{ $agent->first_name }} {{ $agent->last_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Date of Birth</label>
                            <p class="fs-5">
                                @if($agent->date_of_birth)
                                    {{ \Carbon\Carbon::parse($agent->date_of_birth)->format('d M, Y') }}
                                    <span class="badge bg-light text-dark ms-2 border">Age: {{ \Carbon\Carbon::parse($agent->date_of_birth)->age }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Nationality</label>
                            <p class="fs-5">
                                @if($agent->country)
                                    <i class="fas fa-flag me-1 text-muted"></i> {{ $agent->country->name }}
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">License Number</label>
                            <p class="fs-5">
                                @if($agent->license_number)
                                    <span class="badge bg-secondary">{{ $agent->license_number }}</span>
                                @else
                                    <span class="text-muted">No License</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- 2. PROFESSIONAL -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-briefcase me-2"></i>Professional Profile</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Agency</label>
                            <p class="fs-5">
                                @if($agent->agency)
                                    <i class="fas fa-building me-1 text-muted"></i> {{ $agent->agency->name }}
                                @else
                                    <span class="badge bg-light text-dark border">Freelance / Independent</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- 3. AUDIT DATA (Restricted to Admin) -->
                    @if(Auth::check() && Auth::user()->isAdmin())
                    <h6 class="text-danger border-bottom pb-2 mt-4"><i class="fas fa-lock me-2"></i>Audit Data</h6>
                    <div class="row bg-light p-3 rounded">
                        <!-- Fila 1: ID, Estado, Creador -->
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Internal ID</label>
                            <div class="fs-6">{{ $agent->agent_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($agent->is_active)
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
                                {{ $agent->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $agent->created_at ? $agent->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $agent->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($agent->updated_at)
                                        {{ $agent->updated_at->format('d M Y, H:i') }}
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
                                {{ $agent->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($agent->deleted_at)
                                        {{ $agent->deleted_at->format('d M Y, H:i') }}
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