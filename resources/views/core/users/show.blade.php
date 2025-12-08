@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i> User Details & Audit</h5>
                    <div>
                        <a href="{{ route('users.edit', $user->user_id) }}" class="btn btn-warning btn-sm fw-bold">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-light btn-sm fw-bold ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- INFO BÁSICA -->
                    <h6 class="text-primary border-bottom pb-2 mb-3">Basic Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="text-muted small fw-bold">Full Name</label>
                            <p class="fs-5">{{ $user->full_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small fw-bold">Username</label>
                            <p class="fs-5">{{ $user->username }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small fw-bold">Email</label>
                            <p class="fs-5">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small fw-bold">Role</label>
                            <p class="fs-5">
                                <span class="badge {{ $user->isAdmin() ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ $user->role }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- AUDITORÍA -->
                    <h6 class="text-danger border-bottom pb-2 mb-3"><i class="fas fa-history me-2"></i>Audit Log</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm bg-light">
                            <tbody>
                                <tr>
                                    <th class="w-25 bg-light">Internal ID</th>
                                    <td>{{ $user->user_id }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Status</th>
                                    <td>
                                        @if($user->is_active) <span class="text-success fw-bold">Active</span>
                                        @else <span class="text-danger fw-bold">Inactive (Deleted)</span> @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Created At</th>
                                    <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Created By (User ID)</th>
                                    <td>{{ $user->created_by ?? 'System/Self' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Last Updated At</th>
                                    <td>{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Updated By (User ID)</th>
                                    <td>{{ $user->updated_by ?? '-' }}</td>
                                </tr>
                                @if(!$user->is_active)
                                <tr>
                                    <th class="bg-danger text-white">Deleted At</th>
                                    <td class="text-danger">{{ $user->deleted_at }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-danger text-white">Deleted By</th>
                                    <td class="text-danger">{{ $user->deleted_by }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection