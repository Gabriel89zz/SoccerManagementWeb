@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Coach: {{ $coach->full_name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('coaches.update', $coach->coach_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- INFORMACIÓN PERSONAL -->
            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Personal Information</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ $coach->first_name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ $coach->last_name }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ $coach->date_of_birth }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nationality</label>
                    <select name="country_id" class="form-select select2-search" required>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}" 
                                {{ $coach->country_id == $country->country_id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- DETALLES PROFESIONALES -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase me-2"></i>Professional Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">License Level</label>
                    <select name="license_level" class="form-select" required>
                        @foreach(['UEFA Pro', 'UEFA A', 'UEFA B', 'CONMEBOL Pro', 'National A', 'Other'] as $level)
                            <option value="{{ $level }}" {{ $coach->license_level == $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SISTEMA - ELIMINADO -->
            <!-- Se ha removido completamente la sección System Information -->

            <div class="text-end">
                <a href="{{ route('coaches.index') }}" class="btn btn-secondary me-2">Cancel</a>
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