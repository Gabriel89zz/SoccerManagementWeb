@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Agency: {{ $agency->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('agencies.update', $agency->agency_id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Agency Name</label>
                <input type="text" name="name" class="form-control" value="{{ $agency->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Country</label>
                <select name="country_id" class="form-select select2-search" required>
                    @foreach($countries as $country)
                        <option value="{{ $country->country_id }}" 
                            {{ $agency->country_id == $country->country_id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="text-end">
                <a href="{{ route('agencies.index') }}" class="btn btn-secondary me-2">Cancel</a>
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