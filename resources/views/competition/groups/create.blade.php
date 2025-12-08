@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Group</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('groups.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: DETALLES -->
            <h6 class="text-primary mb-3"><i class="fas fa-object-group me-2"></i>Group Details</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Competition Stage</label>
                    <select name="stage_id" class="form-select select2-search" required data-placeholder="Select Stage">
                        <option value=""></option>
                        @foreach($stages as $st)
                            <option value="{{ $st->stage_id }}">
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
                    <input type="text" name="group_name" class="form-control" required placeholder="Ex: Group A">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Qualification Slots</label>
                    <input type="number" name="qualification_slots" class="form-control" required placeholder="Ex: 2" min="0">
                    <div class="form-text text-muted">Number of teams advancing.</div>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('groups.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Group</button>
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