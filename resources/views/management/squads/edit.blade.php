@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Squad</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('squads.update', $squad->squad_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: DEFINICIÓN DE PLANTILLA -->
            <h6 class="text-primary mb-3"><i class="fas fa-users-cog me-2"></i>Squad Definition</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-search" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $squad->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Season</label>
                    <select name="season_id" class="form-select select2-search" required>
                        @foreach($seasons as $season)
                            <option value="{{ $season->season_id }}" {{ $squad->season_id == $season->season_id ? 'selected' : '' }}>
                                {{ $season->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

         
            <div class="text-end">
                <a href="{{ route('squads.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
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