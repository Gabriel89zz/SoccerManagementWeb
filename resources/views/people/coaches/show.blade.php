@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i> Coach Details: {{ $coach->full_name }}
                    </h5>
                    <div>
                        <a href="{{ route('coaches.edit', $coach->coach_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('coaches.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- 1. PERSONAL -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-user me-2"></i>Personal Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">First Name</label>
                            <p class="fs-5">{{ $coach->first_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Last Name</label>
                            <p class="fs-5">{{ $coach->last_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Date of Birth</label>
                            <p class="fs-5">
                                {{ \Carbon\Carbon::parse($coach->date_of_birth)->format('d M, Y') }} 
                                <span class="badge bg-light text-dark ms-1">Age: {{ \Carbon\Carbon::parse($coach->date_of_birth)->age }}</span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Nationality</label>
                            <p class="fs-5">
                                @if($coach->country)
                                    {{ $coach->country->name }} ({{ $coach->country->iso_code }})
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- 2. PROFESSIONAL -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-briefcase me-2"></i>Professional Profile</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">License Level</label>
                            <p class="fs-5">{{ $coach->license_level }}</p>
                        </div>
                    </div>

                    <!-- 3. ADMIN AUDIT DATA (DISEÑO SOLICITADO) -->
                    @if(Auth::check() && Auth::user()->isAdmin())
                    <h6 class="text-danger border-bottom pb-2 mt-4"><i class="fas fa-lock me-2"></i>Admin Audit Data</h6>
                    <div class="row bg-light p-3 rounded">
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold">Internal ID</label>
                            <div class="fs-6">{{ $coach->coach_id }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold">Created By</label>
                            <div>
                                <i class="fas fa-user-edit text-muted me-1"></i>
                                {{ $coach->creator->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $coach->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $coach->editor->username ?? '-' }}
                                <br>
                                <span class="text-muted small">{{ $coach->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($coach->is_active)
                                    <span class="badge bg-success">Active Record</span>
                                @else
                                    <span class="badge bg-danger">Soft Deleted</span>
                                @endif
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