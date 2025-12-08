@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Injury Report</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('injuries.update', $injury->injury_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-crutch me-2"></i>Injury Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Player</label>
                    <select name="player_id" class="form-select player-ajax" required>
                        @if($injury->player)
                            <option value="{{ $injury->player_id }}" selected>
                                {{ $injury->player->full_name }} ({{ $injury->player->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Injury Type</label>
                    <select name="injury_type_id" class="form-select select2-simple" required>
                        @foreach($injuryTypes as $type)
                            <option value="{{ $type->injury_type_id }}" {{ $injury->injury_type_id == $type->injury_type_id ? 'selected' : '' }}>
                                {{ $type->name }} 
                                @if($type->severity_level) ({{ $type->severity_level }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-calendar-alt me-2"></i>Timeline</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Date Incurred</label>
                    <input type="date" name="date_incurred" class="form-control" 
                           value="{{ $injury->date_incurred ? \Carbon\Carbon::parse($injury->date_incurred)->format('Y-m-d') : '' }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Expected Return</label>
                    <input type="date" name="expected_return_date" class="form-control" 
                           value="{{ $injury->expected_return_date ? \Carbon\Carbon::parse($injury->expected_return_date)->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Actual Return</label>
                    <input type="date" name="actual_return_date" class="form-control" 
                           value="{{ $injury->actual_return_date ? \Carbon\Carbon::parse($injury->actual_return_date)->format('Y-m-d') : '' }}">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-futbol me-2"></i>Context (Optional)</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Match Incurred</label>
                    <select name="match_id_incurred" class="form-select match-ajax">
                        <option value="">-- Outside of Match / Training --</option>
                        @if($injury->match)
                            <option value="{{ $injury->match_id_incurred }}" selected>
                                {{ $injury->match->homeTeam->name ?? '?' }} vs {{ $injury->match->awayTeam->name ?? '?' }} 
                                ({{ $injury->match->match_date ? \Carbon\Carbon::parse($injury->match->match_date)->format('d/m/Y') : 'TBD' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

          
            <div class="text-end">
                <a href="{{ route('injuries.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX JUGADORES
        $('.player-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search player name...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.injuries.players.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });

        // 2. AJAX PARTIDOS
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search match...',
            minimumInputLength: 2,
            allowClear: true,
            ajax: {
                url: '{{ route("api.injuries.matches.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });
    });
</script>
@endsection