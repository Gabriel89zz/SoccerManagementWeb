@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Kit Sponsors Management</h2>
    <a href="{{ route('kit-sponsors.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Assign Sponsor</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- BARRA DE BÚSQUEDA AJAX -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search sponsor, team or placement..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div id="search-spinner" class="position-absolute end-0 top-50 translate-middle-y me-2 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
        </div>

        <div id="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Sponsor</th>
                        <th>Team Kit</th>
                        <th>Placement</th>
                        <th>Primary</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($kitSponsors as $item)
                    <tr>
                        <td class="fw-bold">{{ $item->sponsor->name ?? 'Unknown' }}</td>
                        <td>
                            <!-- Mostramos Equipo y Tipo de Kit -->
                            <strong>{{ $item->kit->team->name ?? 'N/A' }}</strong> 
                            <span class="text-muted">({{ $item->kit->kit_type ?? 'Kit' }})</span>
                        </td>
                        <td>{{ $item->placement ?? '-' }}</td>
                        <td>
                            @if($item->is_primary)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <!-- BOTÓN SHOW -->
                            <a href="{{ route('kit-sponsors.show', $item->id) }}" class="btn btn-sm btn-info text-white" title="View Details"><i class="fas fa-eye"></i></a>

                            <a href="{{ route('kit-sponsors.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            
                            <form action="{{ route('kit-sponsors.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this assignment?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $kitSponsors->links('partials.pagination') }}
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

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('kit-sponsors.index') }}?search=${query}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        const newBody = doc.getElementById('table-body').innerHTML;
                        const newPagination = doc.querySelector('.pagination-wrapper').innerHTML;

                        tableBody.innerHTML = newBody;
                        paginationWrapper.innerHTML = newPagination;
                        
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