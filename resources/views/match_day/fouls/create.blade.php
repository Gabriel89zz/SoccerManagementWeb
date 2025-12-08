@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Register New Foul</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('fouls.store') }}" method="POST">
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
                    <input type="number" name="minute" class="form-control" required min="1" max="130" placeholder="Ex: 12">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-user-times me-2"></i>Offender (Who committed the foul)</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Fouling Team</label>
                    <select name="fouling_team_id" id="fouling_team_id" class="form-select select2-simple" required data-placeholder="Select Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Offender Player (Type to search)</label>
                    <select name="fouling_player_id" id="fouling_player_id" class="form-select player-ajax" required data-placeholder="Search offender...">
                        <option value="">-- Search Offender --</option>
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-user-injured me-2"></i>Victim (Who received the foul)</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Fouled Team (Optional)</label>
                    <select name="fouled_team_id" id="fouled_team_id" class="form-select select2-simple" data-placeholder="Select Team">
                        <option value="">-- None --</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Victim Player (Optional, Type to search)</label>
                    <select name="fouled_player_id" id="fouled_player_id" class="form-select player-ajax" data-placeholder="Search victim...">
                        <option value="">-- None --</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3 mt-4">
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        {{-- FIX: Input hidden para enviar '0' cuando no está marcado --}}
                        <input type="hidden" name="is_penalty_kick" value="0">
                        <input class="form-check-input" type="checkbox" id="is_penalty_kick" name="is_penalty_kick" value="1">
                        <label class="form-check-label fw-bold text-danger" for="is_penalty_kick">Resulted in Penalty Kick?</label>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('fouls.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Foul</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Equipos)
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARA PARTIDOS
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Type team name to find match...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.fouls.matches.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });

        // 2. AJAX PARA JUGADORES (Reutilizable)
        function initPlayerSelect(selector, placeholder) {
            $(selector).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholder,
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route("api.fouls.players.search") }}',
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
            });
        }

        initPlayerSelect('#fouling_player_id', 'Search offender...');
        initPlayerSelect('#fouled_player_id', 'Search victim...');

        // Filtros opcionales
        $('#fouling_team_id, #fouled_team_id').on('change', function() {
            var teamId = $(this).val();
            // Lógica adicional de filtrado si se requiere
        });
    });
</script>
@endsection