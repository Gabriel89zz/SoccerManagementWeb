@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Assignment</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('match-officials.update', $official->match_official_id) }}" method="POST">
            @csrf @method('PUT')
            
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match Selection</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Match</label>
                    <select name="match_id" class="form-select match-ajax" required>
                        @if($official->match)
                            <option value="{{ $official->match_id }}" selected>
                                {{ $official->match->homeTeam->name ?? '?' }} vs {{ $official->match->awayTeam->name ?? '?' }} 
                                ({{ $official->match->match_date ? $official->match->match_date->format('d/m/Y') : 'TBD' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-user-shield me-2"></i>Official Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Referee</label>
                    <select name="referee_id" class="form-select select2-simple" required>
                        @foreach($referees as $ref)
                            <option value="{{ $ref->referee_id }}" {{ $official->referee_id == $ref->referee_id ? 'selected' : '' }}>
                                {{ $ref->last_name }}, {{ $ref->first_name }} ({{ $ref->certification_level }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select select2-simple" required>
                        @foreach(['Referee', 'Assistant Referee 1', 'Assistant Referee 2', 'Fourth Official', 'VAR', 'AVAR'] as $role)
                            <option value="{{ $role }}" {{ $official->role == $role ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('match-officials.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-simple').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. AJAX PARA PARTIDOS EN EDIT
        $('.match-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search match...',
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