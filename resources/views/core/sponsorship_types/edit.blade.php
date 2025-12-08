@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Type: {{ $type->type_name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('sponsorship-types.update', $type->sponsorship_type_id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Type Name</label>
                <!-- OJO: name="type_name" -->
                <input type="text" name="type_name" class="form-control" value="{{ $type->type_name }}" required>
            </div>
            <div class="text-end">
                <a href="{{ route('sponsorship-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection