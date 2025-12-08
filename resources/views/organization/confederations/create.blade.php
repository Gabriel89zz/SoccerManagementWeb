@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">New Confederation</h5></div>
    <div class="card-body">
        <form action="{{ route('confederations.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Confederation Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Ex: Union of European Football Associations">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Acronym</label>
                    <input type="text" name="acronym" class="form-control" placeholder="Ex: UEFA">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foundation Year</label>
                    <input type="number" name="foundation_year" class="form-control" placeholder="Ex: 1954">
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('confederations.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success">Save Confederation</button>
            </div>
        </form>
    </div>
</div>
@endsection