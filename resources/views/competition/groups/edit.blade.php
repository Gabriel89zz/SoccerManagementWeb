@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Group: {{ $group->group_name }}</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('groups.update', $group->group_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: DETALLES -->
            <h6 class="text-primary mb-3"><i class="fas fa-object-group me-2"></i>Group Details</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Competition Stage</label>
                    <select name="stage_id" class="form-select select2-search" required>
                        @foreach($stages as $st)
                            <option value="{{ $st->stage_id }}"
                                {{ $group->stage_id == $st->stage_id ? 'selected' : '' }}>
                                @if($st->competitionSeason && $st->competitionSeason->competition)
                                    {{ $st->competitionSeason->competition->name }} ({{ $st->competitionSeason->season->name ?? '-' }}) - 
                                @endif
                                {{ $st->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Group Name</label>
                    <input type="text" name="group_name" class="form-control" value="{{ $group->group_name }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Qualification Slots</label>
                    <input type="number" name="qualification_slots" class="form-control" value="{{ $group->qualification_slots }}" required min="0">
                </div>
            </div>

            <!-- SECCIÓN 2: INFORMACIÓN DEL SISTEMA - ELIMINADA -->
            <!-- Se ha removido completamente la sección System Information -->

            <div class="text-end">
                <a href="{{ route('groups.index') }}" class="btn btn-secondary me-2">Cancel</a>
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