@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Confederation: {{ $confederation->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('confederations.update', $confederation->confederation_id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Confederation Name</label>
                <input type="text" name="name" class="form-control" value="{{ $confederation->name }}" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Acronym</label>
                    <input type="text" name="acronym" class="form-control" value="{{ $confederation->acronym }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foundation Year</label>
                    <input type="number" name="foundation_year" class="form-control" value="{{ $confederation->foundation_year }}">
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('confederations.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection