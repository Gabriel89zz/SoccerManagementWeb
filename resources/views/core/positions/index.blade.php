@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Positions Management</h2>
    <a href="{{ route('positions.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Position</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Acronym</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($positions as $pos)
                <tr>
                    <td class="fw-bold">{{ $pos->name }}</td>
                    <td><span class="badge bg-secondary">{{ $pos->acronym }}</span></td>
                    <td>{{ $pos->category }}</td>
                    <td>
                        <!-- BOTÓN SHOW -->
                        <a href="{{ route('positions.show', $pos->position_id) }}" class="btn btn-sm btn-info text-white" title="View Details"><i class="fas fa-eye"></i></a>
                        
                        <a href="{{ route('positions.edit', $pos->position_id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        
                        <form action="{{ route('positions.destroy', $pos->position_id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this position?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">{{ $positions->links('partials.pagination') }}</div>
    </div>
</div>
@endsection