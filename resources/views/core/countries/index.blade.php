@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Countries Management</h2>
    <a href="{{ route('countries.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Country</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- BARRA DE BÚSQUEDA (Server Side AJAX) -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search in database..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <!-- Spinner pequeño para indicar carga -->
            <div id="search-spinner" class="position-absolute end-0 top-50 translate-middle-y me-2 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
        </div>

        <div id="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>ISO Code</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($countries as $country)
                    <tr>
                        <td class="fw-bold">{{ $country->name }}</td>
                        <td><span class="badge bg-secondary">{{ $country->iso_code }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('countries.show', $country->country_id) }}" class="btn btn-sm btn-info text-white" title="View Details"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('countries.edit', $country->country_id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('countries.destroy', $country->country_id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this country?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Wrapper para actualizar paginación vía Ajax -->
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $countries->links('partials.pagination') }}
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT DE BÚSQUEDA AJAX INTELIGENTE -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('search-input');
        const spinner = document.getElementById('search-spinner');
        const tableBody = document.getElementById('table-body');
        const paginationWrapper = document.querySelector('.pagination-wrapper');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;

            // Mostrar spinner
            spinner.classList.remove('d-none');

            debounceTimer = setTimeout(() => {
                // Hacemos la petición al servidor con el término de búsqueda
                // Esto busca en TODA la base de datos, no solo en la página actual
                fetch(`{{ route('countries.index') }}?search=${query}`)
                    .then(response => response.text())
                    .then(html => {
                        // Parseamos el HTML que devuelve el servidor
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Extraemos la nueva tabla y paginación
                        const newBody = doc.getElementById('table-body').innerHTML;
                        const newPagination = doc.querySelector('.pagination-wrapper').innerHTML;

                        // Reemplazamos en la página actual
                        tableBody.innerHTML = newBody;
                        paginationWrapper.innerHTML = newPagination;
                        
                        // Actualizar URL del navegador sin recargar (opcional, para UX)
                        const newUrl = new URL(window.location);
                        newUrl.searchParams.set('search', query);
                        window.history.pushState({}, '', newUrl);
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        spinner.classList.add('d-none');
                    });
            }, 400); // Espera 400ms a que dejes de escribir
        });
    });
</script>
@endsection