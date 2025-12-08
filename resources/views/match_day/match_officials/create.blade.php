@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Assign Match Official</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('match-officials.store') }}" method="POST">
            @csrf
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match Selection</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required data-placeholder="Search Match...">
                        <option value="">-- Search Match --</option>
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-user-shield me-2"></i>Official Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Referee</label>
                    <select name="referee_id" class="form-select select2-simple" required data-placeholder="Select Referee">
                        <option value=""></option>
                        @foreach($referees as $ref)
                            <option value="{{ $ref->referee_id }}">
                                {{ $ref->last_name }}, {{ $ref->first_name }} ({{ $ref->certification_level }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select select2-simple" required>
                        <option value="">-- Select Role --</option>
                        <option value="Referee">Main Referee</option>
                        <option value="Assistant Referee 1">Assistant Referee 1</option>
                        <option value="Assistant Referee 2">Assistant Referee 2</option>
                        <option value="Fourth Official">Fourth Official</option>
                        <option value="VAR">VAR</option>
                        <option value="AVAR">AVAR</option>
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('match-officials.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Assignment</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select2 Simple (Referee y Roles)
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARA PARTIDOS
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search match by team name...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.match-officials.matches.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; },
                cache: true
            }
        });
    });
</script>
@endsection