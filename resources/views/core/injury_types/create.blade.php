@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Injury Type</h5></div>
    <div class="card-body">
        <form action="{{ route('injury-types.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Injury Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Ex: Hamstring Strain, ACL Tear">
            </div>
            <div class="mb-3">
                <label class="form-label">Severity Level</label>
                <select name="severity_level" class="form-select" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="Critical">Critical</option>
                </select>
            </div>
            <div class="text-end">
                <a href="{{ route('injury-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection