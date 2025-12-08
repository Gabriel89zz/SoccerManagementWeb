@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Association</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('competition-seasons.update', $competitionSeason->competition_season_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: VINCULACIÓN -->
            <h6 class="text-primary mb-3"><i class="fas fa-link me-2"></i>Association Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Competition</label>
                    <select name="competition_id" class="form-select select2-search" required>
                        @foreach($competitions as $comp)
                            <option value="{{ $comp->competition_id }}"
                                {{ $competitionSeason->competition_id == $comp->competition_id ? 'selected' : '' }}>
                                {{ $comp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Season</label>
                    <select name="season_id" class="form-select select2-search" required>
                        @foreach($seasons as $season)
                            <option value="{{ $season->season_id }}"
                                {{ $competitionSeason->season_id == $season->season_id ? 'selected' : '' }}>
                                {{ $season->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: INFORMACIÓN DEL SISTEMA - ELIMINADA -->
            <!-- Se ha removido completamente la sección System Information -->

            <div class="text-end">
                <a href="{{ route('competition-seasons.index') }}" class="btn btn-secondary me-2">Cancel</a>
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