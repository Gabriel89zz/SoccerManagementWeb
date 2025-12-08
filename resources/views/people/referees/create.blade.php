@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Referee Profile</h5></div>
    <div class="card-body">
        <form action="{{ route('referees.store') }}" method="POST">
            @csrf
            
            <!-- PERSONAL -->
            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Personal Information</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" required placeholder="Ex: Pierluigi">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" required placeholder="Ex: Collina">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nationality</label>
                    <select name="country_id" class="form-select select2-search" required data-placeholder="Select Country">
                        <option value=""></option>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}">{{ $country->name }}</option>
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
                        <option value="FIFA International">FIFA International</option>
                        <option value="National Pro">National Pro</option>
                        <option value="National B">National B</option>
                        <option value="Regional">Regional</option>
                        <option value="Assistant">Assistant</option>
                        <option value="VAR Certified">VAR Certified</option>
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('referees.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Referee</button>
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