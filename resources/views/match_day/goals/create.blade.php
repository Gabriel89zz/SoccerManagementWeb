@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Register New Goal</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('goals.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Timing</h6>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Match (Search Teams)</label>
                    <select name="match_id" class="form-select match-ajax" required data-placeholder="Search Match (Home or Away team)...">
                        <option value="">-- Search Match --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minute</label>
                    <input type="number" name="minute" class="form-control" required min="1" max="130" placeholder="Ex: 45">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-running me-2"></i>Players Involved</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Scoring Team</label>
                    <select name="scoring_team_id" id="scoring_team_id" class="form-select select2-simple" required data-placeholder="Select Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Scorer (Type to search)</label>
                    <select name="scoring_player_id" id="scoring_player_id" class="form-select player-ajax" required data-placeholder="Search scorer...">
                        <option value="">-- Search Scorer --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assist (Optional)</label>
                    <select name="assist_player_id" id="assist_player_id" class="form-select player-ajax" data-placeholder="Search assistant...">
                        <option value="">-- None --</option>
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-info-circle me-2"></i>Goal Details</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Body Part</label>
                    <select name="body_part" class="form-select">
                        <option value="Right Foot">Right Foot</option>
                        <option value="Left Foot">Left Foot</option>
                        <option value="Head">Head</option>
                        <option value="Chest">Chest</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Goal Type</label>
                    <select name="is_penalty" class="form-select">
                        <option value="0">Regular Play</option>
                        <option value="1">Penalty Kick</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Own Goal?</label>
                    <select name="is_own_goal" class="form-select">
                        <option value="0">No</option>
                        <option value="1">Yes (Own Goal)</option>
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('goals.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Goal</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Equipos)
        $('.select2-simple').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $(this).data('placeholder')
        });

        // 1. AJAX PARA PARTIDOS (NUEVO)
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Type team name to find match...',
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

        // 2. AJAX PARA JUGADORES (Scorer & Assistant)
        // Configuracion común para reutilizar
        var playerAjaxConfig = {
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search for a player...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.goals.players.search") }}', // Ajusta ruta si es necesario
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term, page: params.page || 1 }; },
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
        };

        $('#scoring_player_id').select2(playerAjaxConfig);
        $('#assist_player_id').select2(playerAjaxConfig);

        // Opcional: Filtrar jugadores por equipo seleccionado
        $('#scoring_team_id').on('change', function() {
            var teamId = $(this).val();
            // Lógica adicional si deseas implementar filtros cruzados
        });
    });
</script>
@endsection