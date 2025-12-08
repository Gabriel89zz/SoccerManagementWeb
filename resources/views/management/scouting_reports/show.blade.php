@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-signature me-2"></i> Report Details
                    </h5>
                    <div>
                        <a href="{{ route('scouting-reports.edit', $report->report_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('scouting-reports.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Report Summary</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Target Player</label>
                            <p class="fs-5">{{ $report->player->full_name ?? 'Unknown' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Report By (Scout)</label>
                            <p class="fs-5">
                                <i class="fas fa-binoculars me-1 text-muted"></i> {{ $report->scout->full_name ?? 'Unknown' }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Overall Rating</label>
                            <div class="fs-4">
                                @if($report->overall_rating >= 80)
                                    <span class="text-success fw-bold">{{ $report->overall_rating }}/100</span>
                                @elseif($report->overall_rating >= 60)
                                    <span class="text-warning fw-bold">{{ $report->overall_rating }}/100</span>
                                @else
                                    <span class="text-danger fw-bold">{{ $report->overall_rating }}/100</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Report Date</label>
                            <p class="fs-5">
                                @if($report->report_date)
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('d M, Y') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-8">
                            <label class="fw-bold text-muted small">Match Observed</label>
                            <p class="fs-5">
                                @if($report->match)
                                    {{ $report->match->homeTeam->name ?? '?' }} vs {{ $report->match->awayTeam->name ?? '?' }}
                                    <span class="text-muted small ms-2">
                                        (@if($report->match->match_date)
                                            {{ \Carbon\Carbon::parse($report->match->match_date)->format('d/m/Y') }}
                                        @endif)
                                    </span>
                                @else
                                    <span class="text-muted">No specific match linked</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="fw-bold text-muted small">Analysis / Notes</label>
                            <div class="p-3 bg-light rounded border">
                                {{ $report->summary_text ?? 'No additional notes provided.' }}
                            </div>
                        </div>
                    </div>

                    <!-- 2. AUDIT DATA (Restricted to Admin) -->
                    @if(Auth::check() && Auth::user()->isAdmin())
                    <h6 class="text-danger border-bottom pb-2 mt-4"><i class="fas fa-lock me-2"></i>Audit Data</h6>
                    <div class="row bg-light p-3 rounded">
                        <!-- Fila 1: ID, Estado, Creador -->
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Internal ID</label>
                            <div class="fs-6">{{ $report->report_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($report->is_active)
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
                                {{ $report->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">
                                    @if($report->created_at)
                                        {{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, H:i') }}
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
                                {{ $report->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($report->updated_at)
                                        {{ \Carbon\Carbon::parse($report->updated_at)->format('d M Y, H:i') }}
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
                                {{ $report->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($report->deleted_at)
                                        {{ \Carbon\Carbon::parse($report->deleted_at)->format('d M Y, H:i') }}
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