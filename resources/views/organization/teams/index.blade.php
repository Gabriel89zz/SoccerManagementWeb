@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Teams Management</h2>
    <a href="{{ route('teams.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Team</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- BARRA DE BÚSQUEDA AJAX -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search team, code, country or stadium..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <!-- Spinner de carga -->
            <div id="search-spinner" class="position-absolute end-0 top-50 translate-middle-y me-2 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
        </div>

        <div id="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Abbreviation</th>
                        <th>Country</th>
                        <th>Stadium</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($teams as $team)
                    <tr>
                        <td class="fw-bold">{{ $team->name }}</td>
                        <td><span class="badge bg-secondary">{{ $team->short_name }}</span></td>
                        <td>{{ $team->country->name ?? 'N/A' }}</td>
                        <td>{{ $team->stadium->name ?? 'No Stadium' }}</td>
                        <td class="text-end">
                            <!-- BOTÓN SHOW -->
                            <a href="{{ route('teams.show', $team->team_id) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('teams.edit', $team->team_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('teams.destroy', $team->team_id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this team?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Paginación dinámica -->
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $teams->links('partials.pagination') }}
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

            spinner.classList.remove('d-none');

            // Esperar 400ms después de que el usuario deje de escribir
            debounceTimer = setTimeout(() => {
                fetch(`{{ route('teams.index') }}?search=${query}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Extraer y reemplazar solo la tabla y la paginación
                        const newBody = doc.getElementById('table-body').innerHTML;
                        const newPagination = doc.querySelector('.pagination-wrapper').innerHTML;

                        tableBody.innerHTML = newBody;
                        paginationWrapper.innerHTML = newPagination;
                        
                        // Actualizar URL sin recargar
                        const newUrl = new URL(window.location);
                        newUrl.searchParams.set('search', query);
                        window.history.pushState({}, '', newUrl);
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        spinner.classList.add('d-none');
                    });
            }, 400);
        });
    });
</script>
@endsection