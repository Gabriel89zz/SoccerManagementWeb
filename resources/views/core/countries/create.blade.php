@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Country</h5></div>
    <div class="card-body">
        <form action="{{ route('countries.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Country Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ISO Code (3 chars)</label>
                <input type="text" name="iso_code" class="form-control" maxlength="3" required placeholder="Ex: USA">
            </div>
            <div class="text-end">
                <a href="{{ route('countries.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Country</button>
            </div>
        </form>
    </div>
</div>
@endsection