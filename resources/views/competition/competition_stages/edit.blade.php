@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Stage: {{ $stage->name }}</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('competition-stages.update', $stage->stage_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: DETALLES -->
            <h6 class="text-primary mb-3"><i class="fas fa-layer-group me-2"></i>Stage Details</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Competition - Season</label>
                    <select name="competition_season_id" class="form-select select2-search" required>
                        @foreach($compSeasons as $cs)
                            <option value="{{ $cs->competition_season_id }}"
                                {{ $stage->competition_season_id == $cs->competition_season_id ? 'selected' : '' }}>
                                {{ $cs->competition->name ?? 'Unknown' }} - {{ $cs->season->name ?? 'Unknown' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Stage Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $stage->name }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stage Order</label>
                    <input type="number" name="stage_order" class="form-control" value="{{ $stage->stage_order }}" required min="1">
                </div>
            </div>

            <!-- SECCIÓN 2: INFORMACIÓN DEL SISTEMA - ELIMINADA -->
            <!-- Se ha removido completamente la sección System Information -->

            <div class="text-end">
                <a href="{{ route('competition-stages.index') }}" class="btn btn-secondary me-2">Cancel</a>
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