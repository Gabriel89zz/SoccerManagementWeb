@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Player Profile</h5></div>
    <div class="card-body">
        <form action="{{ route('players.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: INFORMACIÓN PERSONAL -->
            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Personal Information</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" required placeholder="Ex: Lionel">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" required placeholder="Ex: Messi">
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

            <!-- SECCIÓN 2: ATRIBUTOS TÉCNICOS -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-futbol me-2"></i>Technical Attributes</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Position</label>
                    <select name="primary_position_id" class="form-select select2-search" data-placeholder="Select Position">
                        <option value=""></option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->position_id }}">{{ $pos->name }} ({{ $pos->acronym }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Preferred Foot</label>
                    <select name="preferred_foot" class="form-select" required>
                        <option value="Right">Right</option>
                        <option value="Left">Left</option>
                        <option value="Both">Both</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Height (cm)</label>
                    <input type="number" name="height" class="form-control" placeholder="170">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Weight (kg)</label>
                    <input type="number" name="weight" class="form-control" placeholder="72">
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('players.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Player</button>
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