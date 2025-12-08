@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Player Season Stats</h2>
    <a href="{{ route('player-season-stats.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Record</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Search Bar -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search player, team or competition..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div id="search-spinner" class="position-absolute end-0 top-50 translate-middle-y me-2 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
        </div>

        <div id="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Player</th>
                        <th>Team / Competition</th>
                        <th class="text-center">Apps</th>
                        <th class="text-center">G / A</th>
                        <th class="text-center">Cards</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($stats as $stat)
                    <tr>
                        <td class="fw-bold">{{ $stat->player->full_name ?? 'Unknown Player' }}</td>
                        <td>
                            <div class="fw-bold">{{ $stat->team->name ?? 'Unknown Team' }}</div>
                            @if($stat->competitionSeason && $stat->competitionSeason->competition)
                                <small class="text-muted">
                                    {{ $stat->competitionSeason->competition->name }} 
                                    ({{ $stat->competitionSeason->season->name ?? '-' }})
                                </small>
                            @else
                                <span class="text-muted small">Unknown Comp</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $stat->matches_played }} <small class="text-muted">({{ number_format($stat->minutes_played) }}')</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-75 text-white me-1">{{ $stat->goals }}</span>
                            <span class="badge bg-info bg-opacity-75 text-dark">{{ $stat->assists }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning text-dark border me-1">{{ $stat->yellow_cards }}</span>
                            <span class="badge bg-danger text-white border">{{ $stat->red_cards }}</span>
                        </td>
                        <td class="text-end">
                            <!-- BOTÓN SHOW -->
                            <a href="{{ route('player-season-stats.show', $stat->player_season_stat_id) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('player-season-stats.edit', $stat->player_season_stat_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('player-season-stats.destroy', $stat->player_season_stat_id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this record?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $stats->links('partials.pagination') }}
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
                fetch(`{{ route('player-season-stats.index') }}?search=${query}`)
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