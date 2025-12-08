@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Team Kit</h5></div>
    <div class="card-body">
        <form action="{{ route('team-kits.store') }}" method="POST">
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
                    <label class="form-label">Kit Type</label>
                    <input type="text" name="kit_type" class="form-control" required placeholder="Ex: Home, Away, Third">
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('team-kits.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Kit</button>
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