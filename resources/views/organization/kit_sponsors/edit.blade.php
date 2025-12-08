@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Assignment</h5></div>
    <div class="card-body">
        <form action="{{ route('kit-sponsors.update', $kitSponsor->id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Sponsor</label>
                    <select name="sponsor_id" class="form-select select2-search" required>
                        @foreach($sponsors as $sponsor)
                            <option value="{{ $sponsor->sponsor_id }}" 
                                {{ $kitSponsor->sponsor_id == $sponsor->sponsor_id ? 'selected' : '' }}>
                                {{ $sponsor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team Kit</label>
                    <select name="kit_id" class="form-select select2-search" required>
                        @foreach($kits as $kit)
                            <option value="{{ $kit->kit_id }}"
                                {{ $kitSponsor->kit_id == $kit->kit_id ? 'selected' : '' }}>
                                {{ $kit->team->name ?? 'Unknown' }} - {{ $kit->kit_type }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Placement</label>
                    <input type="text" name="placement" class="form-control" value="{{ $kitSponsor->placement }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Is Primary Sponsor?</label>
                    <select name="is_primary" class="form-select">
                        <option value="0" {{ !$kitSponsor->is_primary ? 'selected' : '' }}>No</option>
                        <option value="1" {{ $kitSponsor->is_primary ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('kit-sponsors.index') }}" class="btn btn-secondary me-2">Cancel</a>
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