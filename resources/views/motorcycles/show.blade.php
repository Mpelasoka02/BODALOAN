@extends('layouts.app')
@section('title', $motorcycle->plate_number . ' - Motorcycle Details')
@section('page-title', 'Motorcycle Details')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('motorcycles.index') }}" style="color:var(--text-secondary);text-decoration:none;">Motorcycles</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">{{ $motorcycle->plate_number }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--text);"><i class="bi bi-bicycle me-2" style="color:var(--gold-500);"></i>{{ $motorcycle->plate_number }}</h4>
        <div class="d-flex align-items-center gap-2 mt-2">
            <span class="badge-status {{ $motorcycle->status }}">{{ ucfirst($motorcycle->status) }}</span>
            <small style="color:var(--text-secondary);">{{ $motorcycle->make }} {{ $motorcycle->model }} ({{ $motorcycle->year }})</small>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('motorcycles.edit', $motorcycle) }}" class="btn btn-outline btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        @if(!auth()->user()->isDriver() && $motorcycle->status === 'available' && !$motorcycle->driver)
            <button class="btn btn-gold btn-sm" data-bs-toggle="collapse" data-bs-target="#assignSection"><i class="bi bi-person-plus me-1"></i>Assign</button>
        @endif
        @if(!auth()->user()->isDriver())
            <form action="{{ route('motorcycles.destroy', $motorcycle) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this motorcycle?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
            </form>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <strong style="font-size:0.85rem;"><i class="bi bi-info-circle me-2" style="color:var(--status-assigned-text);"></i>Motorcycle Details</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Plate Number</small>
                        <span class="fw-semibold">{{ $motorcycle->plate_number }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Make</small>
                        <span class="fw-semibold">{{ $motorcycle->make }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Model</small>
                        <span class="fw-semibold">{{ $motorcycle->model }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Year</small>
                        <span class="fw-semibold">{{ $motorcycle->year }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Color</small>
                        <span class="fw-semibold">{{ $motorcycle->color }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Engine CC</small>
                        <span class="fw-semibold">{{ $motorcycle->engine_cc }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Engine Number</small>
                        <span class="fw-semibold">{{ $motorcycle->engine_number ?: '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Chassis Number</small>
                        <span class="fw-semibold">{{ $motorcycle->chassis_number ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($motorcycle->weekly_amount || $motorcycle->loan_amount || $motorcycle->loan_duration_weeks)
        <div class="card mb-3">
            <div class="card-header">
                <strong style="font-size:0.85rem;"><i class="bi bi-cash me-2" style="color:var(--emerald-600);"></i>Financial Information</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Weekly Amount</small>
                        <span class="fw-semibold">TZS {{ number_format($motorcycle->weekly_amount ?? 0) }}</span>
                    </div>
                    <div class="col-sm-4">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Loan Amount</small>
                        <span class="fw-semibold">TZS {{ number_format($motorcycle->loan_amount ?? 0) }}</span>
                    </div>
                    <div class="col-sm-4">
                        <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Loan Duration</small>
                        <span class="fw-semibold">{{ $motorcycle->loan_duration_weeks ?? '-' }} weeks</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($motorcycle->registration_card || $motorcycle->insurance || $motorcycle->photo)
        <div class="card mb-3">
            <div class="card-header">
                <strong style="font-size:0.85rem;"><i class="bi bi-file-earmark me-2" style="color:var(--status-assigned-text);"></i>Documents</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($motorcycle->registration_card)
                        <div class="col-sm-4">
                            <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Registration Card</small>
                            <a href="{{ asset('storage/' . $motorcycle->registration_card) }}" target="_blank" class="btn btn-outline btn-sm text-decoration-none">
                                <i class="bi bi-file-earmark me-1"></i>View File
                            </a>
                        </div>
                    @endif
                    @if($motorcycle->insurance)
                        <div class="col-sm-4">
                            <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Insurance</small>
                            <a href="{{ asset('storage/' . $motorcycle->insurance) }}" target="_blank" class="btn btn-outline btn-sm text-decoration-none">
                                <i class="bi bi-file-earmark me-1"></i>View File
                            </a>
                        </div>
                    @endif
                    @if($motorcycle->photo)
                        <div class="col-sm-4">
                            <small class="d-block mb-1" style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-secondary);">Photo</small>
                            <a href="{{ asset('storage/' . $motorcycle->photo) }}" target="_blank" class="btn btn-outline btn-sm text-decoration-none">
                                <i class="bi bi-image me-1"></i>View Photo
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        @if($motorcycle->driver)
        <div class="card mb-3">
            <div class="card-header">
                <strong style="font-size:0.85rem;"><i class="bi bi-person-badge me-2" style="color:var(--gold-500);"></i>Assigned Driver</strong>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="user-avatar me-3" style="width: 40px; height: 40px; font-size: 0.9rem;">{{ substr($motorcycle->driver->name, 0, 1) }}</div>
                    <div>
                        <div class="fw-semibold">{{ $motorcycle->driver->name }}</div>
                        <small style="color:var(--text-secondary);">{{ $motorcycle->driver->email }}</small>
                    </div>
                </div>
                @if($motorcycle->driver->phone)
                    <div class="small mt-1"><i class="bi bi-telephone me-2" style="color:var(--text-secondary);"></i>{{ $motorcycle->driver->phone }}</div>
                @endif
            </div>
        </div>
        @endif

        @if(!auth()->user()->isDriver() && $motorcycle->status === 'available' && !$motorcycle->driver)
        <div class="collapse {{ old('driver_id') ? 'show' : '' }}" id="assignSection">
            <div class="card mb-3">
                <div class="card-header">
                    <strong style="font-size:0.85rem;"><i class="bi bi-person-plus me-2" style="color:var(--gold-500);"></i>Assign Driver</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('motorcycles.assign', $motorcycle) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Driver</label>
                            <select name="driver_id" class="form-select" required>
                                <option value="">Choose a driver...</option>
                                @foreach(\App\Models\User::where('role', 'driver')->where('approval_status', 'approved')->get() as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }} ({{ $driver->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-navy w-100"><i class="bi bi-link me-1"></i>Assign & Generate Code</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @php $contract = $motorcycle->contract; @endphp
        @if($contract)
        <div class="card mb-3">
            <div class="card-header">
                <strong style="font-size:0.85rem;"><i class="bi bi-file-text me-2" style="color:var(--status-assigned-text);"></i>Contract</strong>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1">
                    <small style="color:var(--text-secondary);">Number</small>
                    <span class="fw-semibold small">{{ $contract->contract_number }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <small style="color:var(--text-secondary);">Status</small>
                    <span class="badge-status {{ $contract->status }}">{{ ucfirst(str_replace('_', ' ', $contract->status)) }}</span>
                </div>
                @if($contract->loan)
                    <a href="{{ route('contracts.show', $contract->loan) }}" class="btn btn-navy btn-sm w-100"><i class="bi bi-eye me-1"></i>View Contract</a>
                @else
                    <small class="d-block mt-1 text-center" style="color:var(--text-secondary);">Contract ready — will be linked when a loan is assigned.</small>
                @endif
            </div>
        </div>
        @endif

        @if($motorcycle->loan)
        <div class="card mb-3">
            <div class="card-header">
                <strong style="font-size:0.85rem;"><i class="bi bi-wallet2 me-2" style="color:var(--status-assigned-text);"></i>Loan Progress</strong>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1">
                    <small style="color:var(--text-secondary);">Paid</small>
                    <small class="fw-semibold">{{ $motorcycle->loan->progress }}%</small>
                </div>
                <div class="progress-track mb-3">
                    <div class="progress-fill emerald" style="width: {{ $motorcycle->loan->progress }}%;"></div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <small class="d-block" style="color:var(--text-secondary);">Total Amount</small>
                        <span class="fw-semibold" style="font-size: 0.9rem;">TZS {{ number_format($motorcycle->loan->total_amount) }}</span>
                    </div>
                    <div class="col-6">
                        <small class="d-block" style="color:var(--text-secondary);">Amount Paid</small>
                        <span class="fw-semibold" style="font-size: 0.9rem;">TZS {{ number_format($motorcycle->loan->amount_paid) }}</span>
                    </div>
                </div>
                <span class="badge-status {{ $motorcycle->loan->status }}">{{ ucfirst(str_replace('_', ' ', $motorcycle->loan->status)) }}</span>
                <a href="{{ route('loans.show', $motorcycle->loan) }}" class="btn btn-navy btn-sm w-100 mt-3"><i class="bi bi-eye me-1"></i>View Loan</a>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('motorcycles.index') }}" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to List</a>
</div>
@endsection
