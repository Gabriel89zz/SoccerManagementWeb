@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Transfer Record</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('transfer-histories.update', $transfer->transfer_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-user-tag me-2"></i>Player & Date</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Player</label>
                    <select name="player_id" class="form-select player-ajax" required>
                        @if($transfer->player)
                            <option value="{{ $transfer->player_id }}" selected>
                                {{ $transfer->player->full_name }} ({{ $transfer->player->country->name ?? 'N/A' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Transfer Date</label>
                    <input type="date" name="transfer_date" class="form-control" 
                           value="{{ $transfer->transfer_date ? \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') : '' }}" required>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-exchange-alt me-2"></i>Movement Details</h6>
            <div class="row mb-3">
                <div class="col-md-5">
                    <label class="form-label text-danger">From Team</label>
                    <select name="from_team_id" class="form-select team-ajax">
                        <option value="">-- Free Agent / None --</option>
                        @if($transfer->fromTeam)
                            <option value="{{ $transfer->from_team_id }}" selected>
                                {{ $transfer->fromTeam->name }}
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-arrow-right fs-3 text-muted mt-4"></i>
                </div>
                <div class="col-md-5">
                    <label class="form-label text-success">To Team</label>
                    <select name="to_team_id" class="form-select team-ajax" required>
                        @if($transfer->toTeam)
                            <option value="{{ $transfer->to_team_id }}" selected>
                                {{ $transfer->toTeam->name }}
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-file-contract me-2"></i>Contract & Fee</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Transfer Type</label>
                    <select name="transfer_type" class="form-select select2-simple" required>
                        <option value="Transfer" {{ $transfer->transfer_type == 'Transfer' ? 'selected' : '' }}>Permanent Transfer</option>
                        <option value="Loan" {{ $transfer->transfer_type == 'Loan' ? 'selected' : '' }}>Loan</option>
                        <option value="Free Agent" {{ $transfer->transfer_type == 'Free Agent' ? 'selected' : '' }}>Free Agent Signing</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Transfer Fee (€)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" name="transfer_fee_eur" class="form-control" value="{{ $transfer->transfer_fee_eur }}" min="0" step="0.01" required>
                    </div>
                </div>
            </div>


            <div class="text-end">
                <a href="{{ route('transfer-histories.index') }}" class="btn btn-secondary me-2">Cancel</a>
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
                url: '{{ route("api.transfers.players.search") }}',
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

        // 2. AJAX EQUIPOS
        $('.team-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search team name...',
            minimumInputLength: 2,
            allowClear: true,
            ajax: {
                url: '{{ route("api.transfers.teams.search") }}',
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