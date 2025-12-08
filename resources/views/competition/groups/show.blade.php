@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header Cyan -->
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-object-group me-2"></i> Group Details: {{ $group->group_name }}
                    </h5>
                    <div>
                        <a href="{{ route('groups.edit', $group->group_id) }}" class="btn btn-light btn-sm fw-bold text-dark">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('groups.index') }}" class="btn btn-light btn-sm fw-bold text-dark ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    
                    <!-- 1. GENERAL INFORMATION -->
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-info-circle me-2"></i>General Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Group Name</label>
                            <p class="fs-5">{{ $group->group_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">Qualification Slots</label>
                            <p class="fs-5">
                                <span class="badge bg-secondary rounded-pill">{{ $group->qualification_slots }} teams advance</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Stage</label>
                            <p class="fs-5">{{ $group->stage->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Competition</label>
                            <p class="fs-5">
                                @if($group->stage && $group->stage->competitionSeason && $group->stage->competitionSeason->competition)
                                    {{ $group->stage->competitionSeason->competition->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-muted small">Season</label>
                            <p class="fs-5">
                                @if($group->stage && $group->stage->competitionSeason && $group->stage->competitionSeason->season)
                                    <span class="badge bg-info text-dark">{{ $group->stage->competitionSeason->season->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
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
                            <div class="fs-6">{{ $group->group_id }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="small text-muted fw-bold">Status</label>
                            <div>
                                @if($group->is_active)
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
                                {{ $group->createdBy->username ?? 'System' }}
                                <br>
                                <span class="text-muted small">{{ $group->created_at ? $group->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Fila 2: Editor, Eliminador, Fecha Eliminación -->
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Last Updated By</label>
                            <div>
                                <i class="fas fa-pen text-muted me-1"></i>
                                {{ $group->updatedBy->username ?? '-' }}
                                <br>
                                <span class="text-muted small">
                                    @if($group->updated_at)
                                        {{ $group->updated_at->format('d M Y, H:i') }}
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
                                {{ $group->deletedBy->username ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="small text-muted fw-bold">Deleted At</label>
                            <div>
                                <span class="text-muted small">
                                    @if($group->deleted_at)
                                        {{ $group->deleted_at->format('d M Y, H:i') }}
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