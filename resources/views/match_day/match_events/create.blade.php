@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Register New Match Event</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('match-events.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Timing</h6>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Match (Search Teams)</label>
                    <select name="match_id" class="form-select match-ajax" required data-placeholder="Search Match...">
                        <option value="">-- Search Match --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minute</label>
                    <input type="number" name="minute" class="form-control" required min="1" max="130" placeholder="Ex: 55">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-bolt me-2"></i>Event Details</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Event Type</label>
                    <select name="event_type_id" class="form-select select2-simple" required data-placeholder="Select Type">
                        <option value=""></option>
                        @foreach($eventTypes as $type)
                            <option value="{{ $type->event_type_id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Team</label>
                    <select name="team_id" id="team_id" class="form-select select2-simple" required data-placeholder="Select Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Player (Optional, Type to search)</label>
                    <select name="player_id" id="player_id" class="form-select player-ajax" data-placeholder="Search player...">
                        <option value="">-- None --</option>
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('match-events.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Event</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Equipos y Tipos de Evento)
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARA PARTIDOS
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Type team name to find match...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.match-events.matches.search") }}',
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
            allowClear: true,
            ajax: {
                url: '{{ route("api.match-events.players.search") }}',
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

        // Opcional: Filtros y lógica extra
        $('#team_id').on('change', function() {
            // Lógica de filtrado de jugadores por equipo si se desea
        });
    });
</script>
@endsection