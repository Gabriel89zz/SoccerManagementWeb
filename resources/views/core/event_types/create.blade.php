@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Event Type</h5></div>
    <div class="card-body">
        <form action="{{ route('event-types.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Event Type Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Ex: Goal, Corner, Foul">
            </div>
            <div class="text-end">
                <a href="{{ route('event-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection