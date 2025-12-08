@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Assignment</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('squad-staff.update', $squadStaff->squad_staff_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: SELECCIÓN -->
            <h6 class="text-primary mb-3"><i class="fas fa-clipboard-user me-2"></i>Context & Person</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Squad (Team - Season)</label>
                    <select name="squad_id" class="form-select select2-search" required>
                        @foreach($squads as $sq)
                            <option value="{{ $sq->squad_id }}" {{ $squadStaff->squad_id == $sq->squad_id ? 'selected' : '' }}>
                                {{ $sq->team->name ?? '?' }} - {{ $sq->season->name ?? '?' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Staff Member</label>
                    <select name="staff_member_id" class="form-select select2-search" required>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->staff_member_id }}" {{ $squadStaff->staff_member_id == $staff->staff_member_id ? 'selected' : '' }}>
                                {{ $staff->full_name }} ({{ $staff->role->name ?? 'No Role' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- SECCIÓN 2: DETALLES -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-calendar-check me-2"></i>Contract Period</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $squadStaff->start_date ? \Carbon\Carbon::parse($squadStaff->start_date)->format('Y-m-d') : '' }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $squadStaff->end_date ? \Carbon\Carbon::parse($squadStaff->end_date)->format('Y-m-d') : '' }}">
                </div>
            </div>

       
            <div class="text-end">
                <a href="{{ route('squad-staff.index') }}" class="btn btn-secondary me-2">Cancel</a>
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