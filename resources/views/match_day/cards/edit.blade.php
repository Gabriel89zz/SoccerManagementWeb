@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Card</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('cards.update', $card->card_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Timing</h6>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required>
                        @if($card->match)
                            <option value="{{ $card->match_id }}" selected>
                                {{ $card->match->homeTeam->name ?? '?' }} vs {{ $card->match->awayTeam->name ?? '?' }} 
                                ({{ $card->match->match_date ? $card->match->match_date->format('d/m/Y') : 'TBD' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minute</label>
                    <input type="number" name="minute" class="form-control" value="{{ $card->minute }}" required min="1" max="130">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-user-tag me-2"></i>Player & Team</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" id="team_id" class="form-select select2-simple" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $card->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Player (Type to search)</label>
                    <select name="player_id" id="player_id" class="form-select player-ajax" required>
                        @if($card->player)
                            <option value="{{ $card->player_id }}" selected>
                                {{ $card->player->full_name }} ({{ $card->player->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-info-circle me-2"></i>Card Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Card Type</label>
                    <select name="card_type" class="form-select" required>
                        <option value="Yellow" {{ $card->card_type == 'Yellow' ? 'selected' : '' }}>Yellow Card</option>
                        <option value="Red" {{ $card->card_type == 'Red' ? 'selected' : '' }}>Red Card</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Reason</label>
                    <input type="text" name="reason" class="form-control" value="{{ $card->reason }}">
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('cards.index') }}" class="btn btn-secondary me-2">Cancel</a>
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
                url: '{{ route("api.cards.matches.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });

        // 2. AJAX JUGADORES EN EDIT
        $('#player_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search for a player...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.cards.players.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term, page: params.page || 1 }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });
    });
</script>
@endsection