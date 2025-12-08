@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Stadium: {{ $stadium->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('stadiums.update', $stadium->stadium_id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Stadium Name</label>
                <input type="text" name="name" class="form-control" value="{{ $stadium->name }}" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">City (Type to search)</label>
                    <select name="city_id" class="form-select city-ajax" required>
                        <!-- TRUCO: Pre-cargamos solo la opción actual -->
                        @if($stadium->city)
                            <option value="{{ $stadium->city_id }}" selected>
                                {{ $stadium->city->name }} ({{ $stadium->city->country->name ?? '' }})
                            </option>
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" value="{{ $stadium->capacity }}">
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('stadiums.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.city-ajax').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Search for a city...',
            minimumInputLength: 3,
            ajax: {
                url: '{{ route("api.cities") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        });
    });
</script>
@endsection