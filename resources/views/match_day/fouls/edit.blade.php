@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Foul</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('fouls.update', $foul->foul_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: CONTEXTO -->
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Timing</h6>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required>
                        @if($foul->match)
                            <option value="{{ $foul->match_id }}" selected>
                                {{ $foul->match->homeTeam->name ?? '?' }} vs {{ $foul->match->awayTeam->name ?? '?' }} 
                                ({{ $foul->match->match_date ? $foul->match->match_date->format('d/m/Y') : 'TBD' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minute</label>
                    <input type="number" name="minute" class="form-control" value="{{ $foul->minute }}" required min="1" max="130">
                </div>
            </div>

            <!-- SECCIÓN 2: INFRACTOR -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-user-times me-2"></i>Offender</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Fouling Team</label>
                    <select name="fouling_team_id" id="fouling_team_id" class="form-select select2-simple" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $foul->fouling_team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Offender Player (Type to search)</label>
                    <select name="fouling_player_id" id="fouling_player_id" class="form-select player-ajax" required>
                        @if($foul->offender)
                            <option value="{{ $foul->fouling_player_id }}" selected>
                                {{ $foul->offender->full_name }} ({{ $foul->offender->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 3: VÍCTIMA -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-user-injured me-2"></i>Victim</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Fouled Team (Optional)</label>
                    <select name="fouled_team_id" id="fouled_team_id" class="form-select select2-simple">
                        <option value="">-- None --</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $foul->fouled_team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Victim Player (Optional, Type to search)</label>
                    <select name="fouled_player_id" id="fouled_player_id" class="form-select player-ajax">
                        <option value="">-- None --</option>
                        @if($foul->victim)
                            <option value="{{ $foul->fouled_player_id }}" selected>
                                {{ $foul->victim->full_name }} ({{ $foul->victim->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="row mb-3 mt-4">
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        {{-- FIX: Input hidden para enviar '0' cuando se desmarca --}}
                        <input type="hidden" name="is_penalty_kick" value="0">
                        <input class="form-check-input" type="checkbox" id="is_penalty_kick" name="is_penalty_kick" value="1" 
                            {{ $foul->is_penalty_kick ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-danger" for="is_penalty_kick">Resulted in Penalty Kick?</label>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('fouls.index') }}" class="btn btn-secondary me-2">Cancel</a>
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
                url: '{{ route("api.fouls.matches.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });

        // 2. AJAX JUGADORES EN EDIT (Reutilizable)
        function initPlayerSelectEdit(selector, placeholder) {
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

        initPlayerSelectEdit('#fouling_player_id', 'Search offender...');
        initPlayerSelectEdit('#fouled_player_id', 'Search victim...');
    });
</script>
@endsection