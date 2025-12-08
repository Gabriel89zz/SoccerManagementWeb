@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Injury Type: {{ $injuryType->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('injury-types.update', $injuryType->injury_type_id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Injury Name</label>
                <input type="text" name="name" class="form-control" value="{{ $injuryType->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Severity Level</label>
                <select name="severity_level" class="form-select" required>
                    @foreach(['Low', 'Medium', 'High', 'Critical'] as $level)
                        <option value="{{ $level }}" {{ $injuryType->severity_level == $level ? 'selected' : '' }}>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-end">
                <a href="{{ route('injury-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection