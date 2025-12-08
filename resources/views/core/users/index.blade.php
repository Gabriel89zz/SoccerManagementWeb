@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User Management</h2>
    <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-user-plus"></i> New User</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- BÚSQUEDA -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search name, email or username..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div id="search-spinner" class="d-none position-absolute end-0 top-50 translate-middle-y me-2">
                <div class="spinner-border spinner-border-sm text-primary"></div>
            </div>
        </div>

        <div id="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($users as $user)
                    <tr>
                        <td class="fw-bold">
                            {{ $user->full_name }}
                            <br><small class="text-muted">@ {{ $user->username }}</small>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge bg-primary">Admin</span>
                            @else
                                <span class="badge bg-secondary">User</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active) <span class="badge bg-success">Active</span>
                            @else <span class="badge bg-danger">Inactive</span> @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('users.show', $user->user_id) }}" class="btn btn-sm btn-info text-white" title="Details"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('users.edit', $user->user_id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            
                            @if(Auth::id() !== $user->user_id)
                            <form action="{{ route('users.destroy', $user->user_id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Deactivate this user?')"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $users->links('partials.pagination') }}
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT AJAX (Mismo de siempre) -->
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
                fetch(`{{ route('users.index') }}?search=${query}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        document.getElementById('table-body').innerHTML = doc.getElementById('table-body').innerHTML;
                        document.querySelector('.pagination-wrapper').innerHTML = doc.querySelector('.pagination-wrapper').innerHTML;
                        window.history.pushState({}, '', `?search=${query}`);
                    })
                    .finally(() => spinner.classList.add('d-none'));
            }, 400);
        });
    });
</script>
@endsection