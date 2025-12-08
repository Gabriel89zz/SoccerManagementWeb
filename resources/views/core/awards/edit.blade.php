@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Award: {{ $award->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('awards.update', $award->award_id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Award Name</label>
                <input type="text" name="name" class="form-control" value="{{ $award->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Scope (Category)</label>
                <select name="scope" class="form-select" required>
                    @foreach(['Player', 'Team', 'Coach', 'Referee'] as $scope)
                        <option value="{{ $scope }}" {{ $award->scope == $scope ? 'selected' : '' }}>{{ $scope }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-end">
                <a href="{{ route('awards.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection