@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Country: {{ $country->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('countries.update', $country->country_id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Country Name</label>
                <input type="text" name="name" class="form-control" value="{{ $country->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ISO Code</label>
                <input type="text" name="iso_code" class="form-control" value="{{ $country->iso_code }}" maxlength="3" required>
            </div>
            <div class="text-end">
                <a href="{{ route('countries.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update Country</button>
            </div>
        </form>
    </div>
</div>
@endsection