@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-sort-numeric-down me-2"></i> Standing Details
                    </h5>
                    <div>
                        <a href="{{ route('group-standings.edit', $standing->group_standing_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('group-standings.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>Position Overview</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Team</label>
                            <p class="fs-5">{{ $standing->team->name ?? 'Unknown Team' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Group</label>
                            <p class="fs-5">{{ $standing->group->group_name ?? 'Unknown Group' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Competition</label>
                            <p class="fs-5">
                                @if($standing->group && $standing->group->stage && $standing->group->stage->competitionSeason && $standing->group->stage->competitionSeason->competition)
                                    {{ $standing->group->stage->competitionSeason->competition->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4 text-center border-end">
                            <label class="fw-bold text-muted small">Current Rank</label>
                            <p class="display-4 fw-bold text-primary">{{ $standing->rank }}</p>
                        </div>
                        <div class="col-md-4 text-center border-end">
                            <label class="fw-bold text-muted small">Points</label>
                            <p class="display-4 fw-bold text-success">{{ $standing->points }}</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="fw-bold text-muted small">Played</label>
                            <p class="display-4 fw-bold text-dark">{{ $standing->played }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-secondary small border-bottom pb-1">Detailed Stats</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-success">Won</th>
                                            <th class="text-muted">Drawn</th>
                                            <th class="text-danger">Lost</th>
                                            <th>GF</th>
                                            <th>GA</th>
                                            <th>GD</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fs-5 fw-bold">{{ $standing->won }}</td>
                                            <td class="fs-5 fw-bold">{{ $standing->drawn }}</td>
                                            <td class="fs-5 fw-bold">{{ $standing->lost }}</td>
                                            <td class="fs-5">{{ $standing->goals_for }}</td>
                                            <td class="fs-5">{{ $standing->goals_against }}</td>
                                            <td class="fs-5">{{ $standing->goal_difference > 0 ? '+'.$standing->goal_difference : $standing->goal_difference }}</td>
                                        </tr>
                                    </tbody>
                                </table>
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
                            <div class="fs-6">{{ $standing->group_standing_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($standing->is_active)
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
                                {{ $standing->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $standing->created_at ? $standing->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $standing->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($standing->updated_at)
                                        {{ $standing->updated_at->format('d M Y, H:i') }}
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
                                {{ $standing->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($standing->deleted_at)
                                        {{ $standing->deleted_at->format('d M Y, H:i') }}
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