@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit City: {{ $city->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('cities.update', $city->city_id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">City Name</label>
                <input type="text" name="name" class="form-control" value="{{ $city->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Country</label>
                <!-- Select2 Activado -->
                <select name="country_id" class="form-select select2-search" required>
                    @foreach($countries as $country)
                        <option value="{{ $country->country_id }}" 
                            {{ $city->country_id == $country->country_id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-end">
                <a href="{{ route('cities.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update City</button>
            </div>
        </form>
    </div>
</div>
@endsection