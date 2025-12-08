@extends('layouts.admin')

@section('content')

{{-- CARGA FORZADA DE LIBRERÍAS (Solución al error visual) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Add Player to Lineup</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('lineup-players.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-clipboard-list me-2"></i>Lineup Selection</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Match Lineup (Search by Team)</label>
                    {{-- Nota: data-placeholder ayuda a Select2 --}}
                    <select name="match_lineup_id" class="form-select lineup-ajax" required></select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-user-check me-2"></i>Player Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Player (Type to search)</label>
                    <select name="player_id" class="form-select player-ajax" required></select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Position (in this match)</label>
                    <select name="position_id" class="form-select select2-simple" required>
                        <option value=""></option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->position_id }}">{{ $pos->name }} ({{ $pos->acronym }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Is Starter?</label>
                    <select name="is_starter" class="form-select">
                        <option value="1">Yes (Starting XI)</option>
                        <option value="0">No (Substitute)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Is Captain?</label>
                    <select name="is_captain" class="form-select">
                        <option value="0">No</option>
                        <option value="1">Yes (Captain)</option>
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('lineup-players.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Add Player</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Usamos jQuery.noConflict() para evitar problemas si otra librería usa $
    var $j = jQuery.noConflict();

    $j(document).ready(function() {
        console.log('Select2 Iniciando en Create...');

        // Select Simple
        $j('.select2-simple').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select Position'
        });

        // 1. AJAX LINEUPS (Usando la nueva ruta única)
        $j('.lineup-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search team name...',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route("api.lineup-players.specific.lineups") }}', // <--- RUTA NUEVA
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });

        // 2. AJAX PLAYERS (Usando la nueva ruta única)
        $j('.player-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search player name...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.lineup-players.specific.players") }}', // <--- RUTA NUEVA
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            },
            templateResult: formatPlayer,
            templateSelection: formatPlayerSelection
        });

        function formatPlayer (player) {
            if (player.loading) return player.text;
            var markup = '<div><strong>' + player.firstName + ' ' + player.lastName + '</strong></div>';
            if (player.country) { markup += '<div class="text-muted small">Country: ' + player.country + '</div>'; }
            return $j(markup);
        }

        function formatPlayerSelection (player) {
            return player.text || (player.firstName + ' ' + player.lastName);
        }
    });
</script>
@endsection