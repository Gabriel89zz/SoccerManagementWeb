@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i> Player Details: {{ $player->full_name }}
                    </h5>
                    <div>
                        <a href="{{ route('players.edit', $player->player_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('players.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
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
                            <p class="fs-5">{{ $player->first_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Last Name</label>
                            <p class="fs-5">{{ $player->last_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Date of Birth</label>
                            <p class="fs-5">
                                {{ \Carbon\Carbon::parse($player->date_of_birth)->format('d M, Y') }} 
                                <span class="badge bg-light text-dark ms-1">Age: {{ \Carbon\Carbon::parse($player->date_of_birth)->age }}</span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Nationality</label>
                            <p class="fs-5">
                                @if($player->country)
                                    {{ $player->country->name }} ({{ $player->country->iso_code }})
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- 2. TECHNICAL -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-futbol me-2"></i>Technical Profile</h6>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Position</label>
                            <p class="fs-5">
                                @if($player->position)
                                    {{ $player->position->name }} ({{ $player->position->acronym }})
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Preferred Foot</label>
                            <p class="fs-5">{{ $player->preferred_foot }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Height</label>
                            <p class="fs-5">{{ $player->height }} cm</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted small">Weight</label>
                            <p class="fs-5">{{ $player->weight }} kg</p>
                        </div>
                    </div>

                    <!-- 3. AUDIT DATA (Diseño solicitado) -->
                    @if(Auth::check() && Auth::user()->isAdmin())
                    <h6 class="text-danger border-bottom pb-2 mt-4"><i class="fas fa-lock me-2"></i>Audit Data</h6>
                    <div class="row bg-light p-3 rounded">
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold">Internal ID</label>
                            <div class="fs-6">{{ $player->player_id }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold">Created By</label>
                            <div>
                                <i class="fas fa-user-edit text-muted me-1"></i>
                                {{ $player->creator->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $player->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $player->editor->username ?? '-' }}
                                <br>
                                <!-- Formato igualado al de Created At con validacion de nulos -->
                                <span class="text-muted small">
                                    @if($player->updated_at)
                                        {{ $player->updated_at->format('d M Y, H:i') }}
                                    @else
                                        <span class="fst-italic text-secondary">Never Updated</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($player->is_active)
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