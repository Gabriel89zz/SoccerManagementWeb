@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Referee: {{ $referee->full_name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('referees.update', $referee->referee_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- PERSONAL -->
            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Personal Information</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ $referee->first_name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ $referee->last_name }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ $referee->date_of_birth }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nationality</label>
                    <select name="country_id" class="form-select select2-search" required>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}" 
                                {{ $referee->country_id == $country->country_id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- PROFESSIONAL -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase me-2"></i>Professional Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Certification Level</label>
                    <select name="certification_level" class="form-select" required>
                        @foreach(['FIFA International', 'National Pro', 'National B', 'Regional', 'Assistant', 'VAR Certified'] as $level)
                            <option value="{{ $level }}" {{ $referee->certification_level == $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('referees.index') }}" class="btn btn-secondary me-2">Cancel</a>
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