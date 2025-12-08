@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Scouting Report</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('scouting-reports.update', $report->report_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-file-signature me-2"></i>Report Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Scouted Player</label>
                    <select name="scouted_player_id" class="form-select player-ajax" required>
                        @if($report->player)
                            <option value="{{ $report->scouted_player_id }}" selected>
                                {{ $report->player->full_name }} ({{ $report->player->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Scout</label>
                    <select name="scout_id" class="form-select select2-simple" required>
                        @foreach($scouts as $scout)
                            <option value="{{ $scout->scout_id }}" {{ $report->scout_id == $scout->scout_id ? 'selected' : '' }}>
                                {{ $scout->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Report Date</label>
                    <input type="date" name="report_date" class="form-control" 
                           value="{{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('Y-m-d') : '' }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Overall Rating (1-100)</label>
                    <input type="number" name="overall_rating" class="form-control" value="{{ $report->overall_rating }}" required min="1" max="100">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-eye me-2"></i>Observation & Analysis</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Match Observed</label>
                    <select name="match_observed_id" class="form-select match-ajax">
                        <option value="">-- General Observation / Training --</option>
                        @if($report->match)
                            <option value="{{ $report->match_observed_id }}" selected>
                                {{ $report->match->homeTeam->name ?? '?' }} vs {{ $report->match->awayTeam->name ?? '?' }} 
                                (@if($report->match->match_date)
                                    {{ \Carbon\Carbon::parse($report->match->match_date)->format('d/m/Y') }}
                                @else
                                    TBD
                                @endif)
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Summary / Notes</label>
                    <textarea name="summary_text" class="form-control" rows="4">{{ $report->summary_text }}</textarea>
                </div>
            </div>


            <div class="text-end">
                <a href="{{ route('scouting-reports.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // AJAX JUGADORES EN EDIT
        $('.player-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search player name...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.scouting.players.search") }}',
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

        // AJAX PARTIDOS EN EDIT
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search match...',
            minimumInputLength: 2,
            allowClear: true,
            ajax: {
                url: '{{ route("api.scouting.matches.search") }}',
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