@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i> Event Details: {{ $event->eventType->name ?? 'Event' }}
                    </h5>
                    <div>
                        <a href="{{ route('match-events.edit', $event->event_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('match-events.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Event Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Event Type</label>
                            <p class="fs-5">
                                <span class="badge bg-secondary">{{ $event->eventType->name ?? 'Unknown' }}</span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Match Minute</label>
                            <p class="fs-1 fw-bold text-dark">{{ $event->minute }}'</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Team</label>
                            <p class="fs-5">{{ $event->team->name ?? 'Unknown Team' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Player Involved</label>
                            <p class="fs-5">
                                @if($event->player)
                                    <i class="fas fa-user me-1 text-muted"></i> {{ $event->player->full_name }}
                                @else
                                    <span class="text-muted small">Not Applicable / Team Event</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Match Context</label>
                            <p class="fs-6">
                                @if($event->match)
                                    {{ $event->match->homeTeam->name ?? '?' }} <span class="text-muted">vs</span> {{ $event->match->awayTeam->name ?? '?' }}
                                    <br>
                                    <span class="text-muted small">
                                        {{ $event->match->match_date ? $event->match->match_date->format('d M, Y') : 'TBD' }}
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
                            <div class="fs-6">{{ $event->event_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($event->is_active)
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
                                {{ $event->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $event->created_at ? $event->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $event->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($event->updated_at)
                                        {{ $event->updated_at->format('d M Y, H:i') }}
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
                                {{ $event->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($event->deleted_at)
                                        {{ $event->deleted_at->format('d M Y, H:i') }}
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