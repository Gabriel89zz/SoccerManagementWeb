@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Season: {{ $season->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('seasons.update', $season->season_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: INFORMACIÓN -->
            <h6 class="text-primary mb-3"><i class="fas fa-calendar-alt me-2"></i>Season Information</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Season Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $season->name }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" 
                           value="{{ $season->start_date ? $season->start_date->format('Y-m-d') : '' }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" 
                           value="{{ $season->end_date ? $season->end_date->format('Y-m-d') : '' }}" required>
                </div>
            </div>

            <!-- SECCIÓN 2: INFORMACIÓN DEL SISTEMA - ELIMINADA -->
            <!-- Se ha removido completamente la sección System Information -->

            <div class="text-end">
                <a href="{{ route('seasons.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection