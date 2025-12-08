@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Match</h5></div>
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

        <form action="{{ route('matches.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: DETALLES DEL EVENTO -->
            <h6 class="text-primary mb-3"><i class="fas fa-calendar-check me-2"></i>Event Details</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Competition Stage</label>
                    <select name="stage_id" class="form-select select2-search" required data-placeholder="Select Stage">
                        <option value=""></option>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->stage_id }}">
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
                    <input type="datetime-local" name="match_date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="match_status" class="form-select">
                        <option value="Scheduled" selected>Scheduled</option>
                        <option value="Live">Live</option>
                        <option value="Finished">Finished</option>
                        <option value="Postponed">Postponed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: EQUIPOS Y MARCADOR (MODIFICADO) -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-users me-2"></i>Teams & Score</h6>
            <div class="row mb-3 align-items-end">
                <!-- EQUIPO LOCAL -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-success">Home Team</label>
                    <select name="home_team_id" class="form-select select2-search" required data-placeholder="Select Home Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- MARCADOR LOCAL -->
                <div class="col-md-2">
                    <label class="form-label text-center w-100 small text-muted">Home Score</label>
                    <input type="number" name="home_score" class="form-control text-center fw-bold fs-5" min="0" placeholder="0">
                </div>

                <!-- MARCADOR VISITANTE -->
                <div class="col-md-2">
                    <label class="form-label text-center w-100 small text-muted">Away Score</label>
                    <input type="number" name="away_score" class="form-control text-center fw-bold fs-5" min="0" placeholder="0">
                </div>

                <!-- EQUIPO VISITANTE -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-danger">Away Team</label>
                    <select name="away_team_id" class="form-select select2-search" required data-placeholder="Select Away Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Stadium</label>
                    <select name="stadium_id" class="form-select select2-search" data-placeholder="Select Stadium (Optional)">
                        <option value="">-- TBD --</option>
                        @foreach($stadiums as $stadium)
                            <option value="{{ $stadium->stadium_id }}">
                                {{ $stadium->name }} 
                                @if($stadium->city) ({{ $stadium->city->name }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- CAMPO DE ASISTENCIA AGREGADO -->
                <div class="col-md-4">
                    <label class="form-label">Attendance</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                        <input type="number" name="attendance" class="form-control" placeholder="Ex: 50000" min="0">
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('matches.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Match</button>
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