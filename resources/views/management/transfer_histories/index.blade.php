@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Transfer History</h2>
    <a href="{{ route('transfer-histories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Record Transfer</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        
        <!-- Search Bar -->
        <div class="mb-3 position-relative">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="search-input" class="form-control" placeholder="Search player or teams..." value="{{ request('search') }}" autocomplete="off">
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
                        <th>Player</th>
                        <th>From <i class="fas fa-arrow-right mx-1 text-white-50"></i> To</th>
                        <th>Type / Fee</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($transfers as $transfer)
                    <tr>
                        <td>
                            @if($transfer->transfer_date)
                                {{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d M Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="fw-bold">{{ $transfer->player->full_name ?? 'Unknown' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="text-danger me-2">{{ $transfer->fromTeam->name ?? 'Free Agent' }}</span>
                                <i class="fas fa-long-arrow-alt-right text-muted mx-1"></i>
                                <span class="text-success ms-2">{{ $transfer->toTeam->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="badge bg-light text-dark border mb-1">{{ $transfer->transfer_type }}</div>
                            <div class="text-success small fw-bold">
                                € {{ number_format($transfer->transfer_fee_eur, 0) }}
                            </div>
                        </td>
                        <td class="text-end">
                            <!-- BOTÓN SHOW -->
                            <a href="{{ route('transfer-histories.show', $transfer->transfer_id) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('transfer-histories.edit', $transfer->transfer_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('transfer-histories.destroy', $transfer->transfer_id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this transfer record?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-3 pagination-wrapper">
                {{ $transfers->links('partials.pagination') }}
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
                fetch(`{{ route('transfer-histories.index') }}?search=${query}`)
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