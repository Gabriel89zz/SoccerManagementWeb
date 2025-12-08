@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Group Standings</h2>
    <a href="{{ route('group-standings.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Entry</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Search Bar -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search team or group..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div id="search-spinner" class="position-absolute end-0 top-50 translate-middle-y me-2 d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
        </div>

        <div id="table-container">
            <table class="table table-hover align-middle table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Group</th>
                        <th class="text-center">Pos</th>
                        <th>Team</th>
                        <th class="text-center">P</th>
                        <th class="text-center">W</th>
                        <th class="text-center">D</th>
                        <th class="text-center">L</th>
                        <th class="text-center">GD</th>
                        <th class="text-center">Pts</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($standings as $st)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $st->group->group_name ?? 'Unknown' }}</div>
                            @if($st->group && $st->group->stage && $st->group->stage->competitionSeason && $st->group->stage->competitionSeason->competition)
                                <small class="text-muted">{{ $st->group->stage->competitionSeason->competition->name }}</small>
                            @endif
                        </td>
                        <td class="text-center fw-bold text-primary">
                            {{ $st->rank }}
                        </td>
                        <td>{{ $st->team->name ?? 'Unknown Team' }}</td>
                        <td class="text-center">{{ $st->played }}</td>
                        <td class="text-center text-success">{{ $st->won }}</td>
                        <td class="text-center text-muted">{{ $st->drawn }}</td>
                        <td class="text-center text-danger">{{ $st->lost }}</td>
                        <td class="text-center">{{ $st->goal_difference > 0 ? '+'.$st->goal_difference : $st->goal_difference }}</td>
                        <td class="text-center fw-bold">{{ $st->points }}</td>
                        <td class="text-end">
                            <!-- BOTÓN SHOW -->
                            <a href="{{ route('group-standings.show', $st->group_standing_id) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('group-standings.edit', $st->group_standing_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('group-standings.destroy', $st->group_standing_id) }}" method="POST" class="d-inline delete-form">
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
                {{ $standings->links('partials.pagination') }}
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
                fetch(`{{ route('group-standings.index') }}?search=${query}`)
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