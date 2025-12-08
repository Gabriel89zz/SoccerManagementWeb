@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Goal</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('goals.update', $goal->goal_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Timing</h6>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required>
                        @if($goal->match)
                            <option value="{{ $goal->match_id }}" selected>
                                {{ $goal->match->homeTeam->name ?? '?' }} vs {{ $goal->match->awayTeam->name ?? '?' }} 
                                ({{ $goal->match->match_date ? $goal->match->match_date->format('d/m/Y') : 'TBD' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minute</label>
                    <input type="number" name="minute" class="form-control" value="{{ $goal->minute }}" required min="1" max="130">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-running me-2"></i>Players Involved</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Scoring Team</label>
                    <select name="scoring_team_id" id="scoring_team_id" class="form-select select2-simple" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $goal->scoring_team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scorer</label>
                    <select name="scoring_player_id" id="scoring_player_id" class="form-select player-ajax" required>
                        @if($goal->scorer)
                            <option value="{{ $goal->scoring_player_id }}" selected>
                                {{ $goal->scorer->full_name }} ({{ $goal->scorer->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assist (Optional)</label>
                    <select name="assist_player_id" id="assist_player_id" class="form-select player-ajax">
                        @if($goal->assistant)
                            <option value="{{ $goal->assist_player_id }}" selected>
                                {{ $goal->assistant->full_name }} ({{ $goal->assistant->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-info-circle me-2"></i>Goal Details</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Body Part</label>
                    <select name="body_part" class="form-select">
                        @foreach(['Right Foot', 'Left Foot', 'Head', 'Chest', 'Other'] as $part)
                            <option value="{{ $part }}" {{ $goal->body_part == $part ? 'selected' : '' }}>{{ $part }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Goal Type</label>
                    <select name="is_penalty" class="form-select">
                        <option value="0" {{ !$goal->is_penalty ? 'selected' : '' }}>Regular Play</option>
                        <option value="1" {{ $goal->is_penalty ? 'selected' : '' }}>Penalty Kick</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Own Goal?</label>
                    <select name="is_own_goal" class="form-select">
                        <option value="0" {{ !$goal->is_own_goal ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $goal->is_own_goal ? 'selected' : '' }}>Yes (Own Goal)</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('goals.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARTIDOS EN EDIT
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search match...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.matches.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });

        // 2. AJAX JUGADORES EN EDIT
        var playerAjaxConfig = {
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search for a player...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.goals.players.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term, page: params.page || 1 }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        };

        $('#scoring_player_id').select2(playerAjaxConfig);
        $('#assist_player_id').select2(playerAjaxConfig);
    });
</script>
@endsection