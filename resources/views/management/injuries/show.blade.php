@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-injured me-2"></i> Injury Details: {{ $injury->player->full_name ?? 'Unknown' }}
                    </h5>
                    <div>
                        <a href="{{ route('injuries.edit', $injury->injury_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('injuries.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Medical Report</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Player Name</label>
                            <p class="fs-5">{{ $injury->player->full_name ?? 'Unknown' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Injury Type</label>
                            <p class="fs-5">
                                <span class="badge bg-secondary">{{ $injury->injuryType->name ?? 'Unknown' }}</span>
                                @if(isset($injury->injuryType->severity_level))
                                    <small class="text-muted ms-2">Severity: {{ $injury->injuryType->severity_level }}</small>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Date Incurred</label>
                            <p class="fs-5">
                                @if($injury->date_incurred)
                                    {{ \Carbon\Carbon::parse($injury->date_incurred)->format('d M, Y') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Expected Return</label>
                            <p class="fs-5">
                                @if($injury->expected_return_date)
                                    {{ \Carbon\Carbon::parse($injury->expected_return_date)->format('d M, Y') }}
                                @else
                                    Unknown
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Recovery Status</label>
                            <p class="fs-5">
                                @if($injury->actual_return_date)
                                    <span class="badge bg-success">Recovered</span>
                                    <small class="d-block text-muted mt-1">
                                        Returned: {{ \Carbon\Carbon::parse($injury->actual_return_date)->format('d M, Y') }}
                                    </small>
                                @else
                                    <span class="badge bg-danger">Active Injury</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="fw-bold text-muted small">Context (Match)</label>
                            <p class="fs-6">
                                @if($injury->match)
                                    {{ $injury->match->homeTeam->name ?? '?' }} <span class="text-muted">vs</span> {{ $injury->match->awayTeam->name ?? '?' }}
                                    <br>
                                    <span class="text-muted small">
                                        @if($injury->match->match_date)
                                            {{ \Carbon\Carbon::parse($injury->match->match_date)->format('d M Y') }}
                                        @else
                                            TBD
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">Training / Non-Match</span>
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
                            <div class="fs-6">{{ $injury->injury_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($injury->is_active)
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
                                {{ $injury->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">
                                    @if($injury->created_at)
                                        {{ \Carbon\Carbon::parse($injury->created_at)->format('d M Y, H:i') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $injury->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($injury->updated_at)
                                        {{ \Carbon\Carbon::parse($injury->updated_at)->format('d M Y, H:i') }}
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
                                {{ $injury->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($injury->deleted_at)
                                        {{ \Carbon\Carbon::parse($injury->deleted_at)->format('d M Y, H:i') }}
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