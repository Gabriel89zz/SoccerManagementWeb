@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Matches Management</h2>
    <a href="{{ route('matches.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Match</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Search Bar -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search teams, stadium or competition..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div id="search-spinner" class="position-absolute end-0 top-50 translate-middle-y me-2 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
        </div>

        <div id="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th class="text-center">Fixture</th>
                        <th>Competition</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($matches as $match)
                    <tr>
                        <td>
                            @if($match->match_date)
                                <div>{{ $match->match_date->format('d M Y') }}</div>
                                <small class="text-muted">{{ $match->match_date->format('H:i') }}</small>
                            @else
                                <span class="text-muted">TBD</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="fw-bold me-2">{{ $match->homeTeam->name ?? 'Home' }}</span>
                                <span class="badge bg-light text-dark border px-2 py-1 mx-1">
                                    {{ $match->home_score ?? '-' }} : {{ $match->away_score ?? '-' }}
                                </span>
                                <span class="fw-bold ms-2">{{ $match->awayTeam->name ?? 'Away' }}</span>
                            </div>
                            @if($match->stadium)
                                <small class="text-muted d-block mt-1"><i class="fas fa-map-marker-alt me-1"></i>{{ $match->stadium->name }}</small>
                            @endif
                        </td>
                        <td>
                            @if($match->stage && $match->stage->competitionSeason && $match->stage->competitionSeason->competition)
                                <div class="small fw-bold">{{ $match->stage->competitionSeason->competition->name }}</div>
                                <div class="small text-muted">{{ $match->stage->name }}</div>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($match->match_status == 'Finished')
                                <span class="badge bg-secondary">Finished</span>
                            @elseif($match->match_status == 'Live')
                                <span class="badge bg-danger animate__animated animate__pulse animate__infinite">Live</span>
                            @else
                                <span class="badge bg-success">{{ $match->match_status }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <!-- BOTÓN SHOW -->
                            <a href="{{ route('matches.show', $match->match_id) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('matches.edit', $match->match_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('matches.destroy', $match->match_id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this match?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $matches->links('partials.pagination') }}
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
                fetch(`{{ route('matches.index') }}?search=${query}`)
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