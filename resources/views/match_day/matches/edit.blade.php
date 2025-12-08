@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Match</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('matches.update', $match->match_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: DETALLES DEL EVENTO -->
            <h6 class="text-primary mb-3"><i class="fas fa-calendar-check me-2"></i>Event Details</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Competition Stage</label>
                    <select name="stage_id" class="form-select select2-search" required>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->stage_id }}"
                                {{ $match->stage_id == $stage->stage_id ? 'selected' : '' }}>
                                @if($stage->competitionSeason && $stage->competitionSeason->competition)
                                    {{ $stage->competitionSeason->competition->name }} - 
                                @endif
                                {{ $stage->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Match Date & Time</label>
                    <input type="datetime-local" name="match_date" class="form-control" 
                           value="{{ $match->match_date ? $match->match_date->format('Y-m-d\TH:i') : '' }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="match_status" class="form-select">
                        @foreach(['Scheduled', 'Live', 'Finished', 'Postponed', 'Cancelled'] as $status)
                            <option value="{{ $status }}" {{ $match->match_status == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: EQUIPOS Y MARCADOR -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-users me-2"></i>Teams & Score</h6>
            <div class="row mb-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-success">Home Team</label>
                    <select name="home_team_id" class="form-select select2-search" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $match->home_team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-center w-100">Score</label>
                    <input type="number" name="home_score" class="form-control text-center fw-bold fs-5" value="{{ $match->home_score }}" min="0">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label text-center w-100">Score</label>
                    <input type="number" name="away_score" class="form-control text-center fw-bold fs-5" value="{{ $match->away_score }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-danger">Away Team</label>
                    <select name="away_team_id" class="form-select select2-search" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $match->away_team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 3: ESTADIO Y ASISTENCIA -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Stadium</label>
                    <select name="stadium_id" class="form-select select2-search">
                        <option value="">-- TBD --</option>
                        @foreach($stadiums as $stadium)
                            <option value="{{ $stadium->stadium_id }}" {{ $match->stadium_id == $stadium->stadium_id ? 'selected' : '' }}>
                                {{ $stadium->name }} 
                                @if($stadium->city) ({{ $stadium->city->name }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Attendance</label>
                    <input type="number" name="attendance" class="form-control" value="{{ $match->attendance }}" min="0">
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('matches.index') }}" class="btn btn-secondary me-2">Cancel</a>
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