@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Scout: {{ $scout->full_name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('scouts.update', $scout->scout_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: INFORMACIÓN PERSONAL -->
            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Personal Information</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ $scout->first_name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ $scout->last_name }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ $scout->date_of_birth }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nationality</label>
                    <select name="country_id" class="form-select select2-search" required>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}" 
                                {{ $scout->country_id == $country->country_id ? 'selected' : '' }}>
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
                    <label class="form-label">Target Region</label>
                    <input type="text" name="region" class="form-control" value="{{ $scout->region }}" placeholder="Ex: South America">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Employing Team</label>
                    <select name="employing_team_id" class="form-select select2-search" data-placeholder="Select Team">
                        <option value="">-- Freelance / Independent --</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}"
                                {{ $scout->employing_team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 3: INFORMACIÓN DEL SISTEMA - ELIMINADA -->
            <!-- Se ha removido completamente la sección System Information -->

            <div class="text-end">
                <a href="{{ route('scouts.index') }}" class="btn btn-secondary me-2">Cancel</a>
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