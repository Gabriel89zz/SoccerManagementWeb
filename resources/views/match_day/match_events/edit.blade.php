@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Match Event</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('match-events.update', $event->event_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Timing</h6>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required>
                        @if($event->match)
                            <option value="{{ $event->match_id }}" selected>
                                {{ $event->match->homeTeam->name ?? '?' }} vs {{ $event->match->awayTeam->name ?? '?' }} 
                                ({{ $event->match->match_date ? $event->match->match_date->format('d/m/Y') : 'TBD' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Minute</label>
                    <input type="number" name="minute" class="form-control" value="{{ $event->minute }}" required min="1" max="130">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-bolt me-2"></i>Event Details</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Event Type</label>
                    <select name="event_type_id" class="form-select select2-simple" required>
                        @foreach($eventTypes as $type)
                            <option value="{{ $type->event_type_id }}" {{ $event->event_type_id == $type->event_type_id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Team</label>
                    <select name="team_id" id="team_id" class="form-select select2-simple" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $event->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Player (Optional, Type to search)</label>
                    <select name="player_id" id="player_id" class="form-select player-ajax">
                        <option value="">-- None --</option>
                        @if($event->player)
                            <option value="{{ $event->player_id }}" selected>
                                {{ $event->player->full_name }} ({{ $event->player->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

           

            <div class="text-end">
                <a href="{{ route('match-events.index') }}" class="btn btn-secondary me-2">Cancel</a>
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
                url: '{{ route("api.match-events.matches.search") }}',
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
            allowClear: true,
            ajax: {
                url: '{{ route("api.match-events.players.search") }}',
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