@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Agent: {{ $agent->full_name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('agents.update', $agent->agent_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: INFORMACIÓN PERSONAL -->
            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Personal Information</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ $agent->first_name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ $agent->last_name }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ $agent->date_of_birth }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nationality</label>
                    <select name="country_id" class="form-select select2-search" required>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}" 
                                {{ $agent->country_id == $country->country_id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: PERFIL PROFESIONAL -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase me-2"></i>Professional Profile</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">License Number</label>
                    <input type="text" name="license_number" class="form-control" value="{{ $agent->license_number }}" placeholder="Ex: FIFA-12345">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Agency</label>
                    <select name="agency_id" class="form-select select2-search" data-placeholder="Select Agency">
                        <option value="">-- Freelance / None --</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->agency_id }}"
                                {{ $agent->agency_id == $agency->agency_id ? 'selected' : '' }}>
                                {{ $agency->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 3: INFORMACIÓN DEL SISTEMA - ELIMINADA -->
            <!-- Se ha removido completamente la sección System Information -->

            <div class="text-end">
                <a href="{{ route('agents.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-search').select2({ theme: 'bootstrap-5', width: '100%' });
    });
</script>
@endsection