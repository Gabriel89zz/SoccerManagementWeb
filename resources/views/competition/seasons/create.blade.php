@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Season</h5></div>
    <div class="card-body">
        <form action="{{ route('seasons.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: INFORMACIÓN -->
            <h6 class="text-primary mb-3"><i class="fas fa-calendar-alt me-2"></i>Season Information</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Season Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Ex: 2023-2024">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('seasons.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Season</button>
            </div>
        </form>
    </div>
</div>
@endsection