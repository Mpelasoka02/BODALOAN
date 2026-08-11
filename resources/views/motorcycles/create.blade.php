@extends('layouts.app')
@section('title', 'Register Motorcycle')
@section('page-title', 'Register Motorcycle')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('motorcycles.index') }}" style="color:var(--text-secondary);text-decoration:none;">Motorcycles</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">Register</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <strong style="font-size:0.85rem;"><i class="bi bi-bicycle me-2" style="color:var(--gold-500);"></i>Motorcycle Details</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('motorcycles.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Plate Number <span class="text-danger">*</span></label>
                    <input type="text" name="plate_number" class="form-control" placeholder="e.g. T 452 ABC" value="{{ old('plate_number') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Make <span class="text-danger">*</span></label>
                    <input type="text" name="make" class="form-control" placeholder="e.g. TVS, Bajaj" value="{{ old('make') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Model <span class="text-danger">*</span></label>
                    <input type="text" name="model" class="form-control" placeholder="e.g. HLX 125" value="{{ old('model') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Year <span class="text-danger">*</span></label>
                    <input type="number" name="year" class="form-control" min="2000" max="{{ date('Y') }}" placeholder="e.g. 2023" value="{{ old('year') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Color <span class="text-danger">*</span></label>
                    <input type="text" name="color" class="form-control" placeholder="e.g. Black" value="{{ old('color') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Engine CC <span class="text-danger">*</span></label>
                    <input type="number" name="engine_cc" class="form-control" placeholder="e.g. 125" value="{{ old('engine_cc') }}" min="0" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Engine Number</label>
                    <input type="text" name="engine_number" class="form-control" placeholder="Engine number" value="{{ old('engine_number') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Chassis Number</label>
                    <input type="text" name="chassis_number" class="form-control" placeholder="Chassis number" value="{{ old('chassis_number') }}">
                </div>
            </div>

            <h6 class="fw-bold mt-4 mb-3" style="color:var(--emerald-600);"><i class="bi bi-cash me-2"></i>Loan Information</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Weekly Amount (TZS)</label>
                    <input type="number" name="weekly_amount" class="form-control" placeholder="e.g. 46000" value="{{ old('weekly_amount') }}" min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Loan Amount (TZS)</label>
                    <input type="number" name="loan_amount" class="form-control" placeholder="e.g. 2400000" value="{{ old('loan_amount') }}" min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Loan Duration (weeks)</label>
                    <input type="number" name="loan_duration_weeks" class="form-control" placeholder="e.g. 52" value="{{ old('loan_duration_weeks') }}" min="1">
                </div>
            </div>

            <h6 class="fw-bold mt-4 mb-3" style="color:var(--status-assigned-text);"><i class="bi bi-upload me-2"></i>Documents</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Registration Card</label>
                    <input type="file" name="registration_card" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Insurance</label>
                    <input type="file" name="insurance" class="form-control" accept=".jpg,.pdf">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png">
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>Register Motorcycle</button>
                <a href="{{ route('motorcycles.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
