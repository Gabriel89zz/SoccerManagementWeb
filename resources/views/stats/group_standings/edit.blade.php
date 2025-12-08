@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Standing Record</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('group-standings.update', $standing->group_standing_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: CONTEXTO -->
            <h6 class="text-primary mb-3"><i class="fas fa-object-group me-2"></i>Group & Team</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Group</label>
                    <select name="group_id" class="form-select select2-search" required>
                        @foreach($groups as $grp)
                            <option value="{{ $grp->group_id }}" {{ $standing->group_id == $grp->group_id ? 'selected' : '' }}>
                                @if($grp->stage && $grp->stage->competitionSeason && $grp->stage->competitionSeason->competition)
                                    {{ $grp->stage->competitionSeason->competition->name }} - 
                                @endif
                                {{ $grp->group_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-search" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $standing->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: ESTADÍSTICAS -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-chart-bar me-2"></i>Performance Stats</h6>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Rank</label>
                    <input type="number" name="rank" class="form-control" value="{{ $standing->rank }}" required min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Played</label>
                    <input type="number" name="played" class="form-control" value="{{ $standing->played }}" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-success">Points</label>
                    <input type="number" name="points" class="form-control" value="{{ $standing->points }}" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Goal Diff</label>
                    <input type="number" name="goal_difference" class="form-control" value="{{ $standing->goal_difference }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label text-success">Won</label>
                    <input type="number" name="won" class="form-control" value="{{ $standing->won }}" required min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted">Drawn</label>
                    <input type="number" name="drawn" class="form-control" value="{{ $standing->drawn }}" required min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-danger">Lost</label>
                    <input type="number" name="lost" class="form-control" value="{{ $standing->lost }}" required min="0">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Goals For (GF)</label>
                    <input type="number" name="goals_for" class="form-control" value="{{ $standing->goals_for }}" required min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Goals Against (GA)</label>
                    <input type="number" name="goals_against" class="form-control" value="{{ $standing->goals_against }}" required min="0">
                </div>
            </div>


            <div class="text-end">
                <a href="{{ route('group-standings.index') }}" class="btn btn-secondary me-2">Cancel</a>
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