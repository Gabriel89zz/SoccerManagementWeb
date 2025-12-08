@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Position: {{ $position->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('positions.update', $position->position_id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Position Name</label>
                <input type="text" name="name" class="form-control" value="{{ $position->name }}" required>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Acronym</label>
                    <input type="text" name="acronym" class="form-control" value="{{ $position->acronym }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        @foreach(['Goalkeeper', 'Defender', 'Midfielder', 'Forward'] as $cat)
                            <option value="{{ $cat }}" {{ $position->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="text-end">
                <a href="{{ route('positions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection