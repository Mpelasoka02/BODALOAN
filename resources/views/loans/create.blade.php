@extends('layouts.app')
@section('title', 'Create Loan')
@section('page-title', 'Create Loan')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.index') }}" style="color:var(--text-secondary);text-decoration:none;">Loans</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">Create</li>
    </ol>
</nav>

<form method="POST" action="{{ route('loans.store') }}">
    @csrf
    <div class="card mb-4">
        <div class="card-header"><strong style="font-size:0.85rem;"><i class="bi bi-wallet2 me-2" style="color:var(--gold-500);"></i>Loan Details</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Motorcycle <span class="text-danger">*</span></label>
                    <select name="motorcycle_id" class="form-select" required>
                        <option value="">Select motorcycle...</option>
                        @foreach($motorcycles as $m)
                            <option value="{{ $m->id }}" {{ old('motorcycle_id') == $m->id ? 'selected' : '' }} data-driver="{{ $m->driver->name ?? 'No driver' }}">{{ $m->plate_number }} ({{ $m->make }} {{ $m->model }})</option>
                        @endforeach
                    </select>
                    <div class="form-text" id="driverInfo">Select a motorcycle to see the assigned driver</div>
                </div>
            </div>
            @push('scripts')
            <script>
            document.querySelector('select[name="motorcycle_id"]')?.addEventListener('change', function() {
                var opt = this.options[this.selectedIndex];
                document.getElementById('driverInfo').textContent = opt && this.value ? 'Driver: ' + opt.getAttribute('data-driver') : 'Select a motorcycle to see the assigned driver';
            });
            </script>
            @endpush
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Total Amount (TZS) <span class="text-danger">*</span></label>
                    <input type="number" name="total_amount" class="form-control" value="{{ old('total_amount') }}" required min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Weekly Installment (TZS) <span class="text-danger">*</span></label>
                    <input type="number" name="weekly_installment" class="form-control" value="{{ old('weekly_installment') }}" required min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Duration (weeks) <span class="text-danger">*</span></label>
                    <input type="number" name="duration_weeks" class="form-control" value="{{ old('duration_weeks') }}" required min="1">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>Create Loan</button>
        <a href="{{ route('loans.index') }}" class="btn btn-outline">Cancel</a>
    </div>
</form>
@endsection
