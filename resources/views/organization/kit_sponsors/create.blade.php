@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Assign Sponsor to Kit</h5></div>
    <div class="card-body">
        <form action="{{ route('kit-sponsors.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Sponsor</label>
                    <select name="sponsor_id" class="form-select select2-search" required data-placeholder="Select a Sponsor">
                        <option value=""></option>
                        @foreach($sponsors as $sponsor)
                            <option value="{{ $sponsor->sponsor_id }}">{{ $sponsor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team Kit</label>
                    <select name="kit_id" class="form-select select2-search" required data-placeholder="Select a Kit">
                        <option value=""></option>
                        @foreach($kits as $kit)
                            <!-- Mostramos info útil: Equipo - Tipo -->
                            <option value="{{ $kit->kit_id }}">
                                {{ $kit->team->name ?? 'Unknown' }} - {{ $kit->kit_type }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Placement</label>
                    <input type="text" name="placement" class="form-control" placeholder="Ex: Chest, Left Sleeve, Back">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Is Primary Sponsor?</label>
                    <select name="is_primary" class="form-select">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('kit-sponsors.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Assignment</button>
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