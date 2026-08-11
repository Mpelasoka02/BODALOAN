@extends('layouts.app')
@section('title', 'Edit Motorcycle')
@section('page-title', 'Edit Motorcycle')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('motorcycles.index') }}" style="color:var(--text-secondary);text-decoration:none;">Motorcycles</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">Edit {{ $motorcycle->plate_number }}</li>
    </ol>
</nav>

<form method="POST" action="{{ route('motorcycles.update', $motorcycle) }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="card mb-4">
        <div class="card-header">
            <strong style="font-size:0.85rem;"><i class="bi bi-bicycle me-2" style="color:var(--gold-500);"></i>Motorcycle Details</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Plate Number</label>
                    <input type="text" class="form-control" value="{{ $motorcycle->plate_number }}" readonly style="background:var(--page-bg);">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Make <span class="text-danger">*</span></label>
                    <input type="text" name="make" class="form-control" value="{{ old('make', $motorcycle->make) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Model <span class="text-danger">*</span></label>
                    <input type="text" name="model" class="form-control" value="{{ old('model', $motorcycle->model) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Year <span class="text-danger">*</span></label>
                    <input type="number" name="year" class="form-control" min="2000" max="{{ date('Y') }}" value="{{ old('year', $motorcycle->year) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Color <span class="text-danger">*</span></label>
                    <input type="text" name="color" class="form-control" value="{{ old('color', $motorcycle->color) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Engine CC <span class="text-danger">*</span></label>
                    <input type="number" name="engine_cc" class="form-control" value="{{ old('engine_cc', $motorcycle->engine_cc) }}" min="0" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Engine Number</label>
                    <input type="text" name="engine_number" class="form-control" value="{{ old('engine_number', $motorcycle->engine_number) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Chassis Number</label>
                    <input type="text" name="chassis_number" class="form-control" value="{{ old('chassis_number', $motorcycle->chassis_number) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <strong style="font-size:0.85rem;"><i class="bi bi-cash me-2" style="color:var(--emerald-600);"></i>Loan Information</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Weekly Amount (TZS)</label>
                    <input type="number" name="weekly_amount" class="form-control" value="{{ old('weekly_amount', $motorcycle->weekly_amount) }}" min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Loan Amount (TZS)</label>
                    <input type="number" name="loan_amount" class="form-control" value="{{ old('loan_amount', $motorcycle->loan_amount) }}" min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Loan Duration (weeks)</label>
                    <input type="number" name="loan_duration_weeks" class="form-control" value="{{ old('loan_duration_weeks', $motorcycle->loan_duration_weeks) }}" min="1">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <strong style="font-size:0.85rem;"><i class="bi bi-upload me-2" style="color:var(--status-assigned-text);"></i>Documents</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Registration Card</label>
                    <input type="file" name="registration_card" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    @if($motorcycle->registration_card)
                        <div class="mt-1">
                            <small style="color:var(--text-secondary);"><i class="bi bi-file-earmark me-1"></i>Current: <a href="{{ asset('storage/' . $motorcycle->registration_card) }}" target="_blank" class="text-decoration-none" style="font-size:0.8rem;">{{ basename($motorcycle->registration_card) }}</a></small>
                        </div>
                    @endif
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Insurance</label>
                    <input type="file" name="insurance" class="form-control" accept=".jpg,.pdf">
                    @if($motorcycle->insurance)
                        <div class="mt-1">
                            <small style="color:var(--text-secondary);"><i class="bi bi-file-earmark me-1"></i>Current: <a href="{{ asset('storage/' . $motorcycle->insurance) }}" target="_blank" class="text-decoration-none" style="font-size:0.8rem;">{{ basename($motorcycle->insurance) }}</a></small>
                        </div>
                    @endif
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png">
                    @if($motorcycle->photo)
                        <div class="mt-1">
                            <small style="color:var(--text-secondary);"><i class="bi bi-image me-1"></i>Current: <a href="{{ asset('storage/' . $motorcycle->photo) }}" target="_blank" class="text-decoration-none" style="font-size:0.8rem;">{{ basename($motorcycle->photo) }}</a></small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
        <a href="{{ route('motorcycles.show', $motorcycle) }}" class="btn btn-outline">Cancel</a>
    </div>
</form>
@endsection
