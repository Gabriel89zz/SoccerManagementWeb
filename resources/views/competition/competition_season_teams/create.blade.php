@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Team Participation</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('competition-season-teams.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: VINCULACIÓN -->
            <h6 class="text-primary mb-3"><i class="fas fa-link me-2"></i>Association Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Competition - Season</label>
                    <select name="competition_season_id" class="form-select select2-search" required data-placeholder="Select Competition & Season">
                        <option value=""></option>
                        @foreach($compSeasons as $cs)
                            <option value="{{ $cs->competition_season_id }}">
                                {{ $cs->competition->name ?? 'Unknown' }} - {{ $cs->season->name ?? 'Unknown' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-search" required data-placeholder="Select Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: RESULTADOS -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-trophy me-2"></i>Results & Status</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Final Position</label>
                    <input type="number" name="final_position" class="form-control" placeholder="Ex: 1, 4, 18">
                    <div class="form-text text-muted">Leave empty if season is ongoing.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Overall Status</label>
                    <input type="text" name="overall_status" class="form-control" placeholder="Ex: Champion, Qualified for UCL, Relegated">
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('competition-season-teams.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Participation</button>
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