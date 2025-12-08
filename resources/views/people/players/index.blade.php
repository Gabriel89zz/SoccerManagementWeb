@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Players Management</h2>
    <a href="{{ route('players.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Player</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Search Bar -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search player, position or country..." value="{{ request('search') }}" autocomplete="off">
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
                        <th>Position</th>
                        <th>Nationality</th>
                        <th>Age</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($players as $player)
                    <tr>
                        <td class="fw-bold">
                            {{ $player->last_name }}, {{ $player->first_name }}
                            <br>
                            <small class="text-muted">{{ $player->preferred_foot }} foot</small>
                        </td>
                        <td>
                            @if($player->position)
                                <span class="badge bg-secondary">{{ $player->position->acronym }}</span>
                                <small class="text-muted d-block">{{ $player->position->name }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($player->country)
                                {{ $player->country->name }} <small class="text-muted">({{ $player->country->iso_code }})</small>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($player->date_of_birth)->age }} years
                        </td>
                        <td class="text-end">
                            <!-- BOTÓN VER DETALLE (NUEVO) -->
                            <a href="{{ route('players.show', $player->player_id) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('players.edit', $player->player_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('players.destroy', $player->player_id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this player?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $players->links('partials.pagination') }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('search-input');
        const spinner = document.getElementById('search-spinner');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;
            spinner.classList.remove('d-none');

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('players.index') }}?search=${query}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        const newBody = doc.getElementById('table-body').innerHTML;
                        const newPagination = doc.querySelector('.pagination-wrapper').innerHTML;

                        document.getElementById('table-body').innerHTML = newBody;
                        document.querySelector('.pagination-wrapper').innerHTML = newPagination;
                        
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