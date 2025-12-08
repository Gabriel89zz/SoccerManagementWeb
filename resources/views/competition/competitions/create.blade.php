@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Competition</h5></div>
    <div class="card-body">
        <form action="{{ route('competitions.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
            <h6 class="text-primary mb-3"><i class="fas fa-trophy me-2"></i>Competition Info</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Competition Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Ex: Premier League">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Competition Type</label>
                    <select name="competition_type_id" class="form-select select2-search" required data-placeholder="Select Type">
                        <option value=""></option>
                        @foreach($types as $type)
                            {{-- CORRECCIÓN: $type->type_id en lugar de $type->competition_type_id --}}
                            <option value="{{ $type->type_id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: ALCANCE GEOGRÁFICO -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-globe-americas me-2"></i>Scope / Region</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Country (National)</label>
                    <select name="country_id" class="form-select select2-search" data-placeholder="Select Country">
                        <option value="">-- None --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted">Select if it's a domestic league/cup.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confederation (Continental)</label>
                    <select name="confederation_id" class="form-select select2-search" data-placeholder="Select Confederation">
                        <option value="">-- None --</option>
                        @foreach($confederations as $confed)
                            <option value="{{ $confed->confederation_id }}">{{ $confed->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted">Select if it's an international tournament (e.g. UCL).</div>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('competitions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Competition</button>
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