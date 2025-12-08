@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Social Media Link</h5></div>
    <div class="card-body">
        <form action="{{ route('team-social-medias.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-search" required data-placeholder="Select a Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Platform</label>
                    <select name="platform_id" class="form-select select2-search" required data-placeholder="Select Platform">
                        <option value=""></option>
                        @foreach($platforms as $platform)
                            <option value="{{ $platform->social_media_platform_id }}">{{ $platform->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Handle / URL</label>
                <div class="input-group">
                    <span class="input-group-text">@</span>
                    <input type="text" name="handle" class="form-control" required placeholder="Ex: realmadrid or https://twitter.com/realmadrid">
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('team-social-medias.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Link</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-search').select2({ theme: 'bootstrap-5', width: '100%' });
    });
</script>
@endsection