@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Team Match Stats</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('team-match-stats.update', $stat->team_match_stat_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Team</h6>
            <div class="row mb-3">
                <div class="col-md-7">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required>
                        @if($stat->match)
                            <option value="{{ $stat->match_id }}" selected>
                                {{ $stat->match->homeTeam->name ?? '?' }} vs {{ $stat->match->awayTeam->name ?? '?' }} 
                                ({{ $stat->match->match_date ? $stat->match->match_date->format('d/m/Y') : 'TBD' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-simple" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $stat->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-chart-pie me-2"></i>Performance Data</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Possession (%)</label>
                    <div class="input-group">
                        <input type="number" name="possession_percentage" class="form-control" value="{{ $stat->possession_percentage }}" step="0.1" min="0" max="100" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Corners</label>
                    <input type="number" name="corners" class="form-control" value="{{ $stat->corners }}" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Offsides</label>
                    <input type="number" name="offsides" class="form-control" value="{{ $stat->offsides }}" min="0" required>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('team-match-stats.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // AJAX PARTIDOS EN EDIT
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search match...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.team-match-stats.matches.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });
    });
</script>
@endsection