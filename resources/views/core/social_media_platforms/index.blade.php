@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Social Media Platforms</h2>
    <a href="{{ route('social-media-platforms.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Platform</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($platforms as $platform)
                <tr>
                    <td class="fw-bold">{{ $platform->name }}</td>
                    <td class="text-end">
                        <!-- BOTÓN SHOW -->
                        <a href="{{ route('social-media-platforms.show', $platform->social_media_platform_id) }}" class="btn btn-sm btn-info text-white" title="View Details"><i class="fas fa-eye"></i></a>
                        
                        <a href="{{ route('social-media-platforms.edit', $platform->social_media_platform_id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        
                        <form action="{{ route('social-media-platforms.destroy', $platform->social_media_platform_id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this platform?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">{{ $platforms->links('partials.pagination') }}</div>
    </div>
</div>
@endsection