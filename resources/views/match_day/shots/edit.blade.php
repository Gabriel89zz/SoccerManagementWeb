@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Shot</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('shots.update', $shot->shot_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Timing</h6>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required>
                        @if($shot->match)
                            <option value="{{ $shot->match_id }}" selected>
                                {{ $shot->match->homeTeam->name ?? '?' }} vs {{ $shot->match->awayTeam->name ?? '?' }} 
                                ({{ $shot->match->match_date ? $shot->match->match_date->format('d/m/Y') : 'TBD' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minute</label>
                    <input type="number" name="minute" class="form-control" value="{{ $shot->minute }}" required min="1" max="130">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-user-tag me-2"></i>Shooter</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" id="team_id" class="form-select select2-simple" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $shot->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Player (Type to search)</label>
                    <select name="player_id" id="player_id" class="form-select player-ajax" required>
                        @if($shot->player)
                            <option value="{{ $shot->player_id }}" selected>
                                {{ $shot->player->full_name }} ({{ $shot->player->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-bullseye me-2"></i>Shot Details</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Outcome</label>
                    
                    <!-- FIX: Inputs hidden para enviar '0' cuando se desmarca -->
                    <div class="form-check">
                        <input type="hidden" name="is_on_target" value="0">
                        <input class="form-check-input" type="checkbox" id="is_on_target" name="is_on_target" value="1" {{ $shot->is_on_target ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_on_target">Is On Target?</label>
                    </div>
                    
                    <div class="form-check mt-2">
                        <input type="hidden" name="is_goal" value="0">
                        <input class="form-check-input" type="checkbox" id="is_goal" name="is_goal" value="1" {{ $shot->is_goal ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-success" for="is_goal">Is Goal?</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Body Part</label>
                    <select name="body_part" class="form-select">
                        <option value="">-- Select --</option>
                        @foreach(['Right Foot', 'Left Foot', 'Head', 'Other'] as $part)
                            <option value="{{ $part }}" {{ $shot->body_part == $part ? 'selected' : '' }}>{{ $part }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Coordinates (X, Y)</label>
                    <div class="input-group">
                        <input type="number" name="location_x" class="form-control" placeholder="X" step="0.01" value="{{ $shot->location_x }}">
                        <input type="number" name="location_y" class="form-control" placeholder="Y" step="0.01" value="{{ $shot->location_y }}">
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('shots.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARA PARTIDOS
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search match...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.shots.matches.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });

        // 2. AJAX PARA JUGADORES
        $('#player_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search for a player...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.shots.players.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, page: params.page || 1 };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: { more: (params.page * 20) < data.total_count }
                    };
                },
                cache: true
            },
            templateResult: function(player) {
                if (player.loading) return player.text;
                var $container = $('<span>');
                $container.append($('<b>').text(player.firstName + ' ' + player.lastName));
                if (player.country) {
                    $container.append($('<br><small class="text-muted">').text('Country: ' + player.country));
                }
                return $container;
            },
            templateSelection: function(player) {
                return player.text || (player.firstName + ' ' + player.lastName);
            }
        });

        // Auto-check logic
        document.getElementById('is_goal').addEventListener('change', function() {
            if(this.checked) {
                document.getElementById('is_on_target').checked = true;
            }
        });
    });
</script>
@endsection