@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Register New Team</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('teams.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Team Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Ex: Real Madrid CF">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Abbreviation (3 letters)</label>
                    <input type="text" name="short_name" class="form-control" required placeholder="Ex: RMA">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select name="country_id" class="form-select select2-search" required data-placeholder="Select a Country">
                        <option value=""></option>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Home Stadium</label>
                    <select name="home_stadium_id" class="form-select select2-search" data-placeholder="Select a Stadium">
                        <option value=""></option>
                        @foreach($stadiums as $stadium)
                            <option value="{{ $stadium->stadium_id }}">
                                {{ $stadium->name }} ({{ $stadium->city->name ?? 'Unknown City' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Foundation Date</label>
                <input type="date" name="foundation_date" class="form-control">
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('teams.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Team</button>
            </div>
        </form>
    </div>
</div>
@endsection