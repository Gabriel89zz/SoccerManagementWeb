@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Assignment</h5></div>
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

        <form action="{{ route('squad-members.update', $member->squad_member_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-users me-2"></i>Squad & Player</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Squad (Team - Season)</label>
                    <select name="squad_id" class="form-select select2-simple" required>
                        @foreach($squads as $sq)
                            <option value="{{ $sq->squad_id }}" {{ $member->squad_id == $sq->squad_id ? 'selected' : '' }}>
                                {{ $sq->team->name ?? '?' }} - {{ $sq->season->name ?? '?' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Player</label>
                    <select name="player_id" class="form-select player-ajax" required>
                        @if($member->player)
                            <option value="{{ $member->player_id }}" selected>
                                {{ $member->player->full_name }} 
                                ({{ $member->player->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-tshirt me-2"></i>Membership Details</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Jersey Number</label>
                    <input type="number" name="jersey_number" class="form-control" value="{{ $member->jersey_number }}" min="1" max="99">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Join Date</label>
                    <input type="date" name="join_date" class="form-control" value="{{ $member->join_date ? \Carbon\Carbon::parse($member->join_date)->format('Y-m-d') : '' }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Leave Date</label>
                    <input type="date" name="leave_date" class="form-control" value="{{ $member->leave_date ? \Carbon\Carbon::parse($member->leave_date)->format('Y-m-d') : '' }}">
                </div>
            </div>

            <h6 class="text-muted mb-3 mt-4"><i class="fas fa-info-circle me-2"></i>System Information</h6>
            <div class="row mb-3 bg-light p-3 rounded">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Internal ID</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $member->squad_member_id }}" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Status</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $member->is_active ? 'Active' : 'Inactive' }}" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Created At</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $member->created_at }}" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Last Updated</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $member->updated_at }}" disabled>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('squad-members.index') }}" class="btn btn-secondary me-2">Cancel</a>
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