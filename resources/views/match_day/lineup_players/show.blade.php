@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check me-2"></i> Player Lineup Details: {{ $lineupPlayer->player->full_name ?? 'Unknown' }}
                    </h5>
                    <div>
                        <a href="{{ route('lineup-players.edit', $lineupPlayer->lineup_player_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('lineup-players.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Context & Player</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Team</label>
                            <p class="fs-5">{{ $lineupPlayer->lineup->team->name ?? 'Unknown Team' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Match</label>
                            <p class="fs-5">
                                @if($lineupPlayer->lineup && $lineupPlayer->lineup->match)
                                    {{ $lineupPlayer->lineup->match->homeTeam->name ?? '?' }} vs {{ $lineupPlayer->lineup->match->awayTeam->name ?? '?' }}
                                    <br>
                                    <span class="text-muted small">
                                        {{ $lineupPlayer->lineup->match->match_date ? $lineupPlayer->lineup->match->match_date->format('d M, Y') : 'TBD' }}
                                    </span>
                                @else
                                    <span class="text-muted">Match Not Found</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Player Name</label>
                            <p class="fs-5">{{ $lineupPlayer->player->full_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Position (This Match)</label>
                            <p class="fs-5">
                                @if($lineupPlayer->position)
                                    {{ $lineupPlayer->position->name }} ({{ $lineupPlayer->position->acronym }})
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Role</label>
                            <p class="fs-5">
                                @if($lineupPlayer->is_starter)
                                    <span class="badge bg-success">Starter</span>
                                @else
                                    <span class="badge bg-secondary">Substitute</span>
                                @endif

                                @if($lineupPlayer->is_captain)
                                    <span class="badge bg-warning text-dark ms-2"><i class="fas fa-copyright me-1"></i> Captain</span>
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
                            <div class="fs-6">{{ $lineupPlayer->lineup_player_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($lineupPlayer->is_active)
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
                                {{ $lineupPlayer->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $lineupPlayer->created_at ? $lineupPlayer->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $lineupPlayer->editor->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($lineupPlayer->updated_at)
                                        {{ $lineupPlayer->updated_at->format('d M Y, H:i') }}
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
                                {{ $lineupPlayer->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($lineupPlayer->deleted_at)
                                        {{ $lineupPlayer->deleted_at->format('d M Y, H:i') }}
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