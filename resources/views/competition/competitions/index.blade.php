@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Competitions Management</h2>
    <a href="{{ route('competitions.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Competition</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Search Bar -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search competition, type, country or confederation..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div id="search-spinner" class="position-absolute end-0 top-50 translate-middle-y me-2 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
        </div>

        <div id="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Region / Country</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($competitions as $competition)
                    <tr>
                        <td class="fw-bold">
                            {{ $competition->name }}
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $competition->type->type_name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if($competition->country)
                                <i class="fas fa-flag me-1 text-muted"></i> {{ $competition->country->name }}
                            @elseif($competition->confederation)
                                <i class="fas fa-globe me-1 text-muted"></i> {{ $competition->confederation->name }}
                            @else
                                <span class="text-muted">Global / Other</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <!-- BOTÓN SHOW -->
                            <a href="{{ route('competitions.show', $competition->competition_id) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('competitions.edit', $competition->competition_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('competitions.destroy', $competition->competition_id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this competition?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $competitions->links('partials.pagination') }}
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT AJAX -->
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
                fetch(`{{ route('competitions.index') }}?search=${query}`)
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