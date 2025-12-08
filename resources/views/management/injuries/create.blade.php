@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Report New Injury</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('injuries.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-crutch me-2"></i>Injury Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Player</label>
                    <select name="player_id" class="form-select player-ajax" required data-placeholder="Search Player...">
                        <option value="">-- Search Player --</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Injury Type</label>
                    <select name="injury_type_id" class="form-select select2-simple" required data-placeholder="Select Injury Type">
                        <option value=""></option>
                        @foreach($injuryTypes as $type)
                            <option value="{{ $type->injury_type_id }}">
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
                    <input type="date" name="date_incurred" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Expected Return</label>
                    <input type="date" name="expected_return_date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Actual Return</label>
                    <input type="date" name="actual_return_date" class="form-control">
                    <div class="form-text text-muted">Fill only when recovered.</div>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-futbol me-2"></i>Context (Optional)</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Match Incurred</label>
                    <select name="match_id_incurred" class="form-select match-ajax" data-placeholder="Search Match (if injury happened during game)">
                        <option value="">-- Outside of Match / Training --</option>
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('injuries.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Report</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Tipos de Lesión)
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARA JUGADORES
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

        // 2. AJAX PARA PARTIDOS
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