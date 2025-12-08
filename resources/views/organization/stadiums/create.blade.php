@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Stadium</h5></div>
    <div class="card-body">
        <form action="{{ route('stadiums.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Stadium Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Ex: Camp Nou">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">City (Type to search)</label>
                    <!-- SELECT VACÍO PARA AJAX -->
                    <select name="city_id" class="form-select city-ajax" required>
                        <option value="">-- Search City --</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" placeholder="Ex: 99000">
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('stadiums.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Stadium</button>
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
            minimumInputLength: 3, // Espera 3 letras antes de buscar
            ajax: {
                url: '{{ route("api.cities") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term }; // Envía lo que escribes como variable 'q'
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