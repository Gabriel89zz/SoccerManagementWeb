@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Competition Stage</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('competition-stages.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: DETALLES -->
            <h6 class="text-primary mb-3"><i class="fas fa-layer-group me-2"></i>Stage Details</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Competition - Season</label>
                    <select name="competition_season_id" class="form-select select2-search" required data-placeholder="Select Competition Season">
                        <option value=""></option>
                        @foreach($compSeasons as $cs)
                            <option value="{{ $cs->competition_season_id }}">
                                {{ $cs->competition->name ?? 'Unknown' }} - {{ $cs->season->name ?? 'Unknown' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Stage Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Ex: Group Stage, Quarter-finals">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stage Order</label>
                    <input type="number" name="stage_order" class="form-control" required placeholder="1, 2, 3..." min="1">
                    <div class="form-text text-muted">Defines the sequence of stages.</div>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('competition-stages.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Stage</button>
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