@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Award</h5></div>
    <div class="card-body">
        <form action="{{ route('awards.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Award Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Ex: Ballon d'Or">
            </div>
            <div class="mb-3">
                <label class="form-label">Scope (Category)</label>
                <select name="scope" class="form-select" required>
                    <option value="">-- Select Scope --</option>
                    <option value="Player">Player</option>
                    <option value="Team">Team</option>
                    <option value="Coach">Coach</option>
                    <option value="Referee">Referee</option>
                </select>
            </div>
            <div class="text-end">
                <a href="{{ route('awards.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection