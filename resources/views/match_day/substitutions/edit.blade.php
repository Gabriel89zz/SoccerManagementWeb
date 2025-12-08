@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Substitution</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('substitutions.update', $substitution->substitution_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Timing</h6>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required>
                        @if($substitution->match)
                            <option value="{{ $substitution->match_id }}" selected>
                                {{ $substitution->match->homeTeam->name ?? '?' }} vs {{ $substitution->match->awayTeam->name ?? '?' }} 
                                ({{ $substitution->match->match_date ? $substitution->match->match_date->format('d/m/Y') : 'TBD' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minute</label>
                    <input type="number" name="minute" class="form-control" value="{{ $substitution->minute }}" required min="1" max="130">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-exchange-alt me-2"></i>The Swap</h6>
            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Team</label>
                    <select name="team_id" id="team_id" class="form-select select2-simple" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $substitution->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-danger"><i class="fas fa-arrow-down me-1"></i> Player OUT (Type to search)</label>
                    <select name="player_out_id" id="player_out_id" class="form-select player-ajax" required>
                        @if($substitution->playerOut)
                            <option value="{{ $substitution->player_out_id }}" selected>
                                {{ $substitution->playerOut->full_name }} 
                                ({{ $substitution->playerOut->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-success"><i class="fas fa-arrow-up me-1"></i> Player IN (Type to search)</label>
                    <select name="player_in_id" id="player_in_id" class="form-select player-ajax" required>
                        @if($substitution->playerIn)
                            <option value="{{ $substitution->player_in_id }}" selected>
                                {{ $substitution->playerIn->full_name }} 
                                ({{ $substitution->playerIn->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="row mb-3 mt-4">
                <div class="col-md-12">
                    <label class="form-label">Reason (Optional)</label>
                    <input type="text" name="reason" class="form-control" value="{{ $substitution->reason }}">
                </div>
            </div>

            
            <div class="text-end">
                <a href="{{ route('substitutions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARA PARTIDOS EN EDIT
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search match...',
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

        // 2. AJAX PARA JUGADORES EN EDIT
        function initPlayerSelectEdit(selector, placeholder) {
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
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            });
        }

        initPlayerSelectEdit('#player_out_id', 'Search player leaving...');
        initPlayerSelectEdit('#player_in_id', 'Search player entering...');

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