@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Academy</h5></div>
    <div class="card-body">
        <form action="{{ route('academies.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Academy Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Ex: La Masia">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Team</label>
                    <!-- Select2 normal (cargado desde controlador) -->
                    <select name="team_id" class="form-select select2-search" required data-placeholder="Select a Team">
                        <option value=""></option>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">City (Type to search)</label>
                    <!-- Select2 AJAX (Vacío al inicio) -->
                    <select name="city_id" class="form-select city-ajax" required>
                        <option value="">-- Search City --</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('academies.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Academy</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializar búsqueda de equipos (local)
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $( this ).data( 'placeholder' )
        });

        // Inicializar búsqueda de ciudades (AJAX remoto)
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