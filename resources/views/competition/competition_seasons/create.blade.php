@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Competition-Season Association</h5></div>
    <div class="card-body">
        
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('competition-seasons.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: VINCULACIÓN -->
            <h6 class="text-primary mb-3"><i class="fas fa-link me-2"></i>Association Details</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Competition</label>
                    <select name="competition_id" class="form-select select2-search" required data-placeholder="Select Competition">
                        <option value=""></option>
                        @foreach($competitions as $comp)
                            <option value="{{ $comp->competition_id }}">{{ $comp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Season</label>
                    <select name="season_id" class="form-select select2-search" required data-placeholder="Select Season">
                        <option value=""></option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->season_id }}">{{ $season->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('competition-seasons.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Association</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.select2-search').select2({ theme: 'bootstrap-5', width: '100%' });
    });
</script>
@endsection