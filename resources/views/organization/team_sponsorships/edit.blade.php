@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Deal: {{ $contract->team->name ?? 'Unknown' }}</h5></div>
    <div class="card-body">
        <form action="{{ route('team-sponsorships.update', $contract->team_sponsorship_id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-search" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" 
                                {{ $contract->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sponsor</label>
                    <select name="sponsor_id" class="form-select select2-search" required>
                        @foreach($sponsors as $sponsor)
                            <option value="{{ $sponsor->sponsor_id }}" 
                                {{ $contract->sponsor_id == $sponsor->sponsor_id ? 'selected' : '' }}>
                                {{ $sponsor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Sponsorship Type</label>
                    <select name="sponsorship_type_id" class="form-select select2-search" required>
                        @foreach($types as $type)
                            <option value="{{ $type->sponsorship_type_id }}" 
                                {{ $contract->sponsorship_type_id == $type->sponsorship_type_id ? 'selected' : '' }}>
                                {{ $type->type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deal Value (EUR)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" name="deal_value_eur" class="form-control" value="{{ $contract->deal_value_eur }}" required step="0.01" min="0">
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('team-sponsorships.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-search').select2({ theme: 'bootstrap-5', width: '100%' });
    });
</script>
@endsection