@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Register New Substitution</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('substitutions.store') }}" method="POST">
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
                    <input type="number" name="minute" class="form-control" required min="1" max="130" placeholder="Ex: 75">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-exchange-alt me-2"></i>The Swap</h6>
            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Team</label>
                    <select name="team_id" id="team_id" class="form-select select2-simple" required data-placeholder="Select Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-danger"><i class="fas fa-arrow-down me-1"></i> Player OUT (Type to search)</label>
                    <select name="player_out_id" id="player_out_id" class="form-select player-ajax" required data-placeholder="Search player leaving...">
                        <option value="">-- Search Player OUT --</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-success"><i class="fas fa-arrow-up me-1"></i> Player IN (Type to search)</label>
                    <select name="player_in_id" id="player_in_id" class="form-select player-ajax" required data-placeholder="Search player entering...">
                        <option value="">-- Search Player IN --</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3 mt-4">
                <div class="col-md-12">
                    <label class="form-label">Reason (Optional)</label>
                    <input type="text" name="reason" class="form-control" placeholder="Ex: Tactical, Injury, Fatigue">
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('substitutions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Substitution</button>
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
                url: '{{ route("api.substitutions.matches.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });

        // 2. AJAX PARA JUGADORES
        function initPlayerSelect(selector, placeholder) {
            $(selector).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholder,
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route("api.substitutions.players.search") }}',
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
        }

        initPlayerSelect('#player_out_id', 'Search player leaving...');
        initPlayerSelect('#player_in_id', 'Search player entering...');

        // Filtro opcional
        $('#team_id').on('change', function() {
            var teamId = $(this).val();
            // Lógica adicional de filtrado si es necesaria
        });

        // Validación
        function validateDifferentPlayers() {
            var playerIn = $('#player_in_id').val();
            var playerOut = $('#player_out_id').val();
            
            if (playerIn && playerOut && playerIn === playerOut) {
                alert('Error: Player IN and Player OUT must be different!');
                $('#player_in_id').val('').trigger('change');
            }
        }

        $('#player_in_id, #player_out_id').on('change', validateDifferentPlayers);
    });
</script>
@endsection