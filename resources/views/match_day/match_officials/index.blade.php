@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Match Officials Management</h2>
    <a href="{{ route('match-officials.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Assign Official</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Search Bar -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search referee, role or match..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div id="search-spinner" class="position-absolute end-0 top-50 translate-middle-y me-2 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
        </div>

        <div id="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Match Context</th>
                        <th>Official Name</th>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($officials as $off)
                    <tr>
                        <td>
                            @if($off->match)
                                <div class="small text-muted">
                                    {{ $off->match->homeTeam->name ?? '?' }} vs {{ $off->match->awayTeam->name ?? '?' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $off->match->match_date ? $off->match->match_date->format('d/m/Y') : 'TBD' }}
                                </div>
                            @else
                                <span class="text-muted">Unknown Match</span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $off->referee->full_name ?? 'Unknown Referee' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $off->role }}</span>
                        </td>
                        <td class="text-end">
                            <!-- BOTÓN SHOW -->
                            <a href="{{ route('match-officials.show', $off->match_official_id) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('match-officials.edit', $off->match_official_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('match-officials.destroy', $off->match_official_id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Remove this assignment?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $officials->links('partials.pagination') }}
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
                fetch(`{{ route('match-officials.index') }}?search=${query}`)
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