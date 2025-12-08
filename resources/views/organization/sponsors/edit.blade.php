@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Sponsor: {{ $sponsor->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('sponsors.update', $sponsor->sponsor_id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Sponsor Name</label>
                <input type="text" name="name" class="form-control" value="{{ $sponsor->name }}" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Industry</label>
                    <input type="text" name="industry" class="form-control" value="{{ $sponsor->industry }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select name="country_id" class="form-select select2-search" required>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}" 
                                {{ $sponsor->country_id == $country->country_id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('sponsors.index') }}" class="btn btn-secondary me-2">Cancel</a>
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