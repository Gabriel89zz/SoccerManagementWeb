@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Competition: {{ $competition->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('competitions.update', $competition->competition_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
            <h6 class="text-primary mb-3"><i class="fas fa-trophy me-2"></i>Competition Info</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Competition Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $competition->name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Competition Type</label>
                    <select name="competition_type_id" class="form-select select2-search" required>
                        @foreach($types as $type)
                            {{-- CORRECCIÓN: $type->type_id y $type->type_name --}}
                            <option value="{{ $type->type_id }}"
                                {{ $competition->competition_type_id == $type->type_id ? 'selected' : '' }}>
                                {{ $type->type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: ALCANCE GEOGRÁFICO -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-globe-americas me-2"></i>Scope / Region</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Country (National)</label>
                    <select name="country_id" class="form-select select2-search">
                        <option value="">-- None --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}" 
                                {{ $competition->country_id == $country->country_id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confederation (Continental)</label>
                    <select name="confederation_id" class="form-select select2-search">
                        <option value="">-- None --</option>
                        @foreach($confederations as $confed)
                            <option value="{{ $confed->confederation_id }}"
                                {{ $competition->confederation_id == $confed->confederation_id ? 'selected' : '' }}>
                                {{ $confed->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('competitions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
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