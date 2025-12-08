@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Record Transfer</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('transfer-histories.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-user-tag me-2"></i>Player & Date</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Player</label>
                    <select name="player_id" class="form-select player-ajax" required data-placeholder="Search Player...">
                        <option value="">-- Search Player --</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Transfer Date</label>
                    <input type="date" name="transfer_date" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-exchange-alt me-2"></i>Movement Details</h6>
            <div class="row mb-3">
                <div class="col-md-5">
                    <label class="form-label text-danger">From Team</label>
                    <select name="from_team_id" class="form-select team-ajax" data-placeholder="Select Source Team...">
                        <option value="">-- Free Agent / None --</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-arrow-right fs-3 text-muted mt-4"></i>
                </div>
                <div class="col-md-5">
                    <label class="form-label text-success">To Team</label>
                    <select name="to_team_id" class="form-select team-ajax" required data-placeholder="Select Destination Team...">
                        <option value="">-- Search Destination Team --</option>
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-file-contract me-2"></i>Contract & Fee</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Transfer Type</label>
                    <select name="transfer_type" class="form-select select2-simple" required>
                        <option value="Transfer">Permanent Transfer</option>
                        <option value="Loan">Loan</option>
                        <option value="Free Agent">Free Agent Signing</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Transfer Fee (€)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" name="transfer_fee_eur" class="form-control" placeholder="0.00" min="0" step="0.01" required>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('transfer-histories.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Record</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Tipos)
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARA JUGADORES
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

        // 2. AJAX PARA EQUIPOS
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