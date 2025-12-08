@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Formation: {{ $formation->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('formations.update', $formation->formation_id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Formation Name</label>
                <input type="text" name="name" class="form-control" value="{{ $formation->name }}" required>
            </div>
            <div class="text-end">
                <a href="{{ route('formations.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection