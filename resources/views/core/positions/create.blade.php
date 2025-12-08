@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Position</h5></div>
    <div class="card-body">
        <form action="{{ route('positions.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Position Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Ex: Goalkeeper">
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Acronym</label>
                    <input type="text" name="acronym" class="form-control" required placeholder="Ex: GK">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="Goalkeeper">Goalkeeper</option>
                        <option value="Defender">Defender</option>
                        <option value="Midfielder">Midfielder</option>
                        <option value="Forward">Forward</option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <a href="{{ route('positions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection