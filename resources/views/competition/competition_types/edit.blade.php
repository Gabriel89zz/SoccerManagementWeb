@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Type: {{ $type->type_name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('competition-types.update', $type->type_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: INFORMACIÓN -->
            <h6 class="text-primary mb-3"><i class="fas fa-tags me-2"></i>Type Information</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Type Name</label>
                    <input type="text" name="type_name" class="form-control" value="{{ $type->type_name }}" required>
                </div>
            </div>

            <!-- SECCIÓN 2: INFORMACIÓN DEL SISTEMA - ELIMINADA -->
            <!-- Se ha removido completamente la sección System Information -->

            <div class="text-end">
                <a href="{{ route('competition-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection