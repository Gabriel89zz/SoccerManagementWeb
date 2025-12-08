@extends('layouts.admin')

@section('content')

<div class="card shadow border-0">
    <!-- HEADER: Cyan Style -->
    <div class="card-header bg-info text-white p-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-globe-americas me-2"></i> City Details: {{ $city->name }}
        </h5>
        <div>
            <a href="{{ route('cities.edit', $city->city_id) }}" class="btn btn-light btn-sm fw-bold text-dark me-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('cities.index') }}" class="btn btn-light btn-sm fw-bold text-dark">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card-body p-4">
        
        <!-- SECTION 1: General Information (Visible para todos) -->
        <h5 class="text-primary mb-3">
            <i class="fas fa-info-circle me-2"></i> General Information
        </h5>
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="text-muted fw-bold small text-uppercase">City Name</label>
                <div class="fs-5 mt-1">
                    <i class="fas fa-map-marker-alt text-danger me-2"></i> {{ $city->name }}
                </div>
            </div>
            <div class="col-md-6">
                <label class="text-muted fw-bold small text-uppercase">Country</label>
                <div class="mt-1">
                    @if($city->country)
                        <span class="badge bg-secondary fs-6 px-3 py-2">
                            {{ $city->country->iso_code ?? 'ISO' }}
                        </span>
                        <span class="ms-2 fw-bold text-dark">{{ $city->country->name }}</span>
                    @else
                        <span class="text-danger">Not Assigned</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- SECTION 2: Audit Data (RESTRICTED - Solo Admin) -->
        {{-- Usamos la misma lógica que tu ejemplo de Player --}}
        @if(Auth::check() && Auth::user()->isAdmin())
        
        <h5 class="text-danger mb-3 mt-5">
            <i class="fas fa-lock me-2"></i> Audit Data
        </h5>
        
        <div class="bg-light p-4 rounded-3 border">
            <div class="row g-4">
                <!-- Internal ID -->
                <div class="col-md-4">
                    <label class="text-muted fw-bold small">Internal ID</label>
                    <p class="mb-0 fs-5">{{ $city->city_id }}</p>
                </div>

                <!-- Status -->
                <div class="col-md-4">
                    <label class="text-muted fw-bold small">Status</label>
                    <div class="mt-1">
                        @if($city->is_active)
                            <span class="badge bg-success px-3 py-2">Active Record</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Inactive</span>
                        @endif
                    </div>
                </div>

                <!-- Created By -->
                <div class="col-md-4">
                    <label class="text-muted fw-bold small">Created By</label>
                    <div class="d-flex align-items-center mt-1">
                        <i class="fas fa-user text-secondary me-2"></i>
                        <div>
                            {{-- Ajusta 'createdBy->name' según tu relación real en el modelo City --}}
                            <div class="fw-bold"> {{ $city->creator->username ?? 'System' }}</div>
                            <small class="text-muted">{{ $city->created_at ? $city->created_at->format('d M Y, H:i') : '-' }}</small>
                        </div>
                    </div>
                </div>

                <!-- Last Updated By -->
                <div class="col-md-4">
                    <label class="text-muted fw-bold small">Last Updated By</label>
                    <div class="d-flex align-items-center mt-1">
                        <i class="fas fa-pen text-secondary me-2"></i>
                        <div>
                            <div class="fw-bold">{{ $city->updatedBy->name ?? '-' }}</div>
                            <small class="text-muted">{{ $city->updated_at ? $city->updated_at->format('d M Y, H:i') : '-' }}</small>
                        </div>
                    </div>
                </div>

                <!-- Deleted By -->
                <div class="col-md-4">
                    <label class="text-muted fw-bold small">Deleted By</label>
                    <div class="d-flex align-items-center mt-1">
                        <i class="fas fa-trash text-secondary me-2"></i>
                        <div>
                            <div class="fw-bold">{{ $city->deletedBy->name ?? '-' }}</div>
                            <small class="text-muted">{{ $city->deleted_at ? $city->deleted_at->format('d M Y, H:i') : '-' }}</small>
                        </div>
                    </div>
                </div>
                
                 <!-- Deleted At -->
                 <div class="col-md-4">
                    <label class="text-muted fw-bold small">Deleted At</label>
                    <div class="mt-1">
                         <span class="text-muted">{{ $city->deleted_at ? $city->deleted_at->format('d M Y, H:i') : '-' }}</span>
                    </div>
                </div>

            </div>
        </div>
        @endif
        {{-- Fin del bloque restringido --}}

    </div>
</div>
@endsection