@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Record Player Season Stats</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('player-season-stats.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-id-card-alt me-2"></i>Context</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Player</label>
                    <select name="player_id" class="form-select player-ajax" required data-placeholder="Search Player...">
                        <option value="">-- Search Player --</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-simple" required data-placeholder="Select Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Competition Season</label>
                    <select name="competition_season_id" class="form-select select2-simple" required data-placeholder="Select League/Season">
                        <option value=""></option>
                        @foreach($compSeasons as $cs)
                            <option value="{{ $cs->competition_season_id }}">
                                {{ $cs->competition->name ?? '?' }} - {{ $cs->season->name ?? '?' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-running me-2"></i>Performance</h6>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Matches Played</label>
                    <input type="number" name="matches_played" class="form-control" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Minutes Played</label>
                    <input type="number" name="minutes_played" class="form-control" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-success">Goals</label>
                    <input type="number" name="goals" class="form-control" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-info">Assists</label>
                    <input type="number" name="assists" class="form-control" required min="0">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Shots on Target</label>
                    <input type="number" name="shots_on_target" class="form-control" required min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-warning">Yellow Cards</label>
                    <input type="number" name="yellow_cards" class="form-control" required min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-danger">Red Cards</label>
                    <input type="number" name="red_cards" class="form-control" required min="0">
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('player-season-stats.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Record</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Equipos y Competiciones)
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // AJAX PARA JUGADORES
        $('.player-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search player name...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.season-stats.players.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
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
    });
</script>
@endsection