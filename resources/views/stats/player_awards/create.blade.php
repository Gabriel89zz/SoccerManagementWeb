@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Record Player Award</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('player-awards.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-medal me-2"></i>Award Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Award</label>
                    <select name="award_id" class="form-select select2-simple" required data-placeholder="Select Award">
                        <option value=""></option>
                        @foreach($awards as $award)
                            <option value="{{ $award->award_id }}">{{ $award->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Player (Type to search)</label>
                    <select name="player_id" class="form-select player-ajax" required data-placeholder="Search Player...">
                        <option value="">-- Search Player --</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Season</label>
                    <select name="season_id" class="form-select select2-simple" required data-placeholder="Select Season">
                        <option value=""></option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->season_id }}">{{ $season->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('player-awards.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Record</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Awards y Seasons)
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // AJAX PARA JUGADORES
        $('.player-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search player name...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.player-awards.players.search") }}',
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
    });
</script>
@endsection