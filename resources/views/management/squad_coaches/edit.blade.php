@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Assignment</h5></div>
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

        <form action="{{ route('squad-coaches.update', $squadCoach->squad_coach_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: SELECCIÓN -->
            <h6 class="text-primary mb-3"><i class="fas fa-chalkboard-teacher me-2"></i>Context & Coach</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Squad (Team - Season)</label>
                    <select name="squad_id" class="form-select select2-search" required>
                        @foreach($squads as $sq)
                            <option value="{{ $sq->squad_id }}" {{ $squadCoach->squad_id == $sq->squad_id ? 'selected' : '' }}>
                                {{ $sq->team->name ?? '?' }} - {{ $sq->season->name ?? '?' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Coach</label>
                    <select name="coach_id" class="form-select select2-search" required>
                        {{-- FILTRO UNIQUE: Evita duplicados visuales por nombre completo --}}
                        @foreach($coaches->unique(fn($c) => $c->first_name . $c->last_name) as $coach)
                            <option value="{{ $coach->coach_id }}" {{ $squadCoach->coach_id == $coach->coach_id ? 'selected' : '' }}>
                                {{ $coach->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: DETALLES -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-calendar-check me-2"></i>Contract Period</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $squadCoach->start_date ? \Carbon\Carbon::parse($squadCoach->start_date)->format('Y-m-d') : '' }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $squadCoach->end_date ? \Carbon\Carbon::parse($squadCoach->end_date)->format('Y-m-d') : '' }}">
                </div>
            </div>

        

            <div class="text-end">
                <a href="{{ route('squad-coaches.index') }}" class="btn btn-secondary me-2">Cancel</a>
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