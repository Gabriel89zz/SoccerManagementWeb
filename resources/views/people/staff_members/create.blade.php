@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Staff Member Profile</h5></div>
    <div class="card-body">
        <form action="{{ route('staff-members.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: INFORMACIÓN PERSONAL -->
            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Personal Information</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" required placeholder="Ex: Carlo">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" required placeholder="Ex: Ancelotti">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control">
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

            <!-- SECCIÓN 2: PERFIL PROFESIONAL -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-briefcase me-2"></i>Professional Profile</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Staff Role</label>
                    <select name="role_id" class="form-select select2-search" required data-placeholder="Select Role">
                        <option value=""></option>
                        @foreach($roles as $role)
                            <option value="{{ $role->role_id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('staff-members.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Staff Member</button>
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