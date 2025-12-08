@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Academy: {{ $academy->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('academies.update', $academy->academy_id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Academy Name</label>
                <input type="text" name="name" class="form-control" value="{{ $academy->name }}" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <select name="team_id" class="form-select select2-search" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" 
                                {{ $academy->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">City (Type to search)</label>
                    <select name="city_id" class="form-select city-ajax" required>
                        <!-- Pre-cargamos solo la ciudad actual -->
                        @if($academy->city)
                            <option value="{{ $academy->city_id }}" selected>
                                {{ $academy->city->name }} ({{ $academy->city->country->name ?? '' }})
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('academies.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-search').select2({ theme: 'bootstrap-5', width: '100%' });

        $('.city-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search for a city...',
            minimumInputLength: 3,
            ajax: {
                url: '{{ route("api.cities") }}',
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