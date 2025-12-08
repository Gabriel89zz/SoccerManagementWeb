@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Edit Team: {{ $team->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('teams.update', $team->team_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Team Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $team->name }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Abbreviation</label>
                    <input type="text" name="short_name" class="form-control" value="{{ $team->short_name }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select name="country_id" class="form-select select2-search" required>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}" 
                                {{ $team->country_id == $country->country_id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Home Stadium</label>
                    <select name="home_stadium_id" class="form-select select2-search">
                        <option value="">-- No Stadium --</option>
                        @foreach($stadiums as $stadium)
                            <option value="{{ $stadium->stadium_id }}"
                                {{ $team->home_stadium_id == $stadium->stadium_id ? 'selected' : '' }}>
                                {{ $stadium->name }} ({{ $stadium->city->name ?? 'Unknown City' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Foundation Date</label>
                <input type="date" name="foundation_date" class="form-control" value="{{ $team->foundation_date }}">
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('teams.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update Team</button>
            </div>
        </form>
    </div>
</div>
@endsection