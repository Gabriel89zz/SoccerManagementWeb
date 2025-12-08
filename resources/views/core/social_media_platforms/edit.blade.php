@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Platform: {{ $platform->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('social-media-platforms.update', $platform->social_media_platform_id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Platform Name</label>
                <input type="text" name="name" class="form-control" value="{{ $platform->name }}" required>
            </div>
            <div class="text-end">
                <a href="{{ route('social-media-platforms.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection