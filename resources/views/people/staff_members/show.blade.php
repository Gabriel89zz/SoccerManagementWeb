@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-id-badge me-2"></i> Staff Member Details: {{ $staffMember->first_name }} {{ $staffMember->last_name }}
                    </h5>
                    <div>
                        <a href="{{ route('staff-members.edit', $staffMember->staff_member_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('staff-members.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
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
                            <p class="fs-5">{{ $staffMember->first_name }} {{ $staffMember->last_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Date of Birth</label>
                            <p class="fs-5">
                                @if($staffMember->date_of_birth)
                                    {{ \Carbon\Carbon::parse($staffMember->date_of_birth)->format('d M, Y') }}
                                    <span class="badge bg-light text-dark ms-2 border">Age: {{ \Carbon\Carbon::parse($staffMember->date_of_birth)->age }}</span>
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
                                @if($staffMember->country)
                                    <i class="fas fa-flag me-1 text-muted"></i> {{ $staffMember->country->name }}
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Role</label>
                            <p class="fs-5">
                                @if($staffMember->role)
                                    <span class="badge bg-secondary">{{ $staffMember->role->name }}</span>
                                @else
                                    <span class="text-muted">No Role Assigned</span>
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
                            <div class="fs-6">{{ $staffMember->staff_member_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($staffMember->is_active)
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
                                {{ $staffMember->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $staffMember->created_at ? $staffMember->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $staffMember->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($staffMember->updated_at)
                                        {{ $staffMember->updated_at->format('d M Y, H:i') }}
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
                                {{ $staffMember->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($staffMember->deleted_at)
                                        {{ $staffMember->deleted_at->format('d M Y, H:i') }}
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