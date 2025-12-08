@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Sponsorship Deal</h5></div>
    <div class="card-body">
        <form action="{{ route('team-sponsorships.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-search" required data-placeholder="Select a Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sponsor</label>
                    <select name="sponsor_id" class="form-select select2-search" required data-placeholder="Select a Sponsor">
                        <option value=""></option>
                        @foreach($sponsors as $sponsor)
                            <option value="{{ $sponsor->sponsor_id }}">{{ $sponsor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Sponsorship Type</label>
                    <select name="sponsorship_type_id" class="form-select select2-search" required data-placeholder="Select Type">
                        <option value=""></option>
                        @foreach($types as $type)
                            <option value="{{ $type->sponsorship_type_id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deal Value (EUR)</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" name="deal_value_eur" class="form-control" required step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('team-sponsorships.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Deal</button>
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