@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Competition Type</h5></div>
    <div class="card-body">
        <form action="{{ route('competition-types.store') }}" method="POST">
            @csrf
            
            <!-- SECCIÓN 1: INFORMACIÓN -->
            <h6 class="text-primary mb-3"><i class="fas fa-tags me-2"></i>Type Information</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Type Name</label>
                    <input type="text" name="type_name" class="form-control" required placeholder="Ex: Domestic League, International Cup">
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('competition-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Type</button>
            </div>
        </form>
    </div>
</div>
@endsection