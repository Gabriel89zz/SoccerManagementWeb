@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Record Team Match Stats</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('team-match-stats.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match & Team</h6>
            <div class="row mb-3">
                <div class="col-md-7">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required data-placeholder="Search Match...">
                        <option value="">-- Search Match --</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-simple" required data-placeholder="Select Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-chart-pie me-2"></i>Performance Data</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Possession (%)</label>
                    <div class="input-group">
                        <input type="number" name="possession_percentage" class="form-control" placeholder="Ex: 55.5" step="0.1" min="0" max="100" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Corners</label>
                    <input type="number" name="corners" class="form-control" placeholder="Ex: 5" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Offsides</label>
                    <input type="number" name="offsides" class="form-control" placeholder="Ex: 2" min="0" required>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('team-match-stats.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Stats</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Equipos)
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // AJAX PARA PARTIDOS
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search match...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.team-match-stats.matches.search") }}',
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