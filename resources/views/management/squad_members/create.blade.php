@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Assign Player to Squad</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('squad-members.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-users me-2"></i>Squad & Player</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Squad (Team - Season)</label>
                    <select name="squad_id" class="form-select select2-simple" required data-placeholder="Select Squad">
                        <option value=""></option>
                        @foreach($squads as $sq)
                            <option value="{{ $sq->squad_id }}">
                                {{ $sq->team->name ?? '?' }} - {{ $sq->season->name ?? '?' }}
                            </option>
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

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-tshirt me-2"></i>Membership Details</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Jersey Number</label>
                    <input type="number" name="jersey_number" class="form-control" placeholder="Ex: 10" min="1" max="99">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Join Date</label>
                    <input type="date" name="join_date" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Leave Date (Optional)</label>
                    <input type="date" name="leave_date" class="form-control">
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('squad-members.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Assignment</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Squads)
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARA JUGADORES
        $('.player-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search player name...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.squad-members.players.search") }}',
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