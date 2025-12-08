@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Agency</h5></div>
    <div class="card-body">
        <form action="{{ route('agencies.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Agency Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Ex: Gestifute">
            </div>

            <div class="mb-3">
                <label class="form-label">Country</label>
                <!-- Select2 Local (Países son pocos) -->
                <select name="country_id" class="form-select select2-search" required data-placeholder="Select a Country">
                    <option value=""></option>
                    @foreach($countries as $country)
                        <option value="{{ $country->country_id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="text-end">
                <a href="{{ route('agencies.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Agency</button>
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