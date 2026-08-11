@extends('layouts.app')
@section('title', 'Contract ' . ($contract->contract_number ?? ''))
@section('page-title', 'Contract Details')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.show', $loan) }}" style="color:var(--text-muted);text-decoration:none;">Loan #{{ $loan->id }}</a></li>
        <li class="breadcrumb-item active">Contract</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Contract {{ $contract->contract_number }}</h5>
        <small class="text-muted">{{ $loan->motorcycle->plate_number }} &mdash; {{ $loan->driver->name }} &harr; {{ $loan->owner->name }}</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('contracts.print', $loan) }}" class="btn btn-primary btn-sm" target="_blank"><i class="bi bi-printer me-1"></i> Print Contract</a>
        <a href="{{ route('contracts.download', $loan) }}" class="btn btn-outline btn-sm"><i class="bi bi-download me-1"></i> Download PDF</a>
        @if(!$contract->isFullySigned())
            <a href="{{ route('contracts.upload.form', $loan) }}" class="btn btn-emerald btn-sm"><i class="bi bi-upload me-1"></i> Upload Signed</a>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><strong><i class="bi bi-file-earmark-text me-2"></i>Agreement Preview</strong></div>
            <div class="card-body" style="font-size:0.85rem;line-height:1.7;">
                @include('contracts.pdf', ['contract' => $contract, 'loan' => $loan, 'motorcycle' => $loan->motorcycle, 'owner' => $loan->owner, 'driver' => $loan->driver])
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><strong><i class="bi bi-info-circle me-2"></i>Status</strong></div>
            <div class="card-body">
                @php
                    $badge = match($contract->status) {
                        'pending' => ['warning', 'Pending'],
                        'partially_signed' => ['info', 'Partially Signed'],
                        'fully_signed' => ['success', 'Active'],
                        'approved' => ['success', 'Active'],
                        default => ['secondary', $contract->status],
                    };
                @endphp
                <span class="badge bg-{{ $badge[0] }} mb-3" style="font-size:0.8rem;padding:6px 14px;">{{ $badge[1] }}</span>

                <ul class="list-unstyled" style="font-size:0.85rem;">
                    <li class="mb-2">
                        <i class="bi {{ $contract->owner_signed_at ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }} me-2"></i>
                        Owner Signed
                        @if($contract->owner_signed_at)<br><span class="text-muted ms-4" style="font-size:0.75rem;">{{ $contract->owner_signed_at->format('d M Y H:i') }}</span>@endif
                    </li>
                    <li class="mb-2">
                        <i class="bi {{ $contract->driver_signed_at ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }} me-2"></i>
                        Driver Signed
                        @if($contract->driver_signed_at)<br><span class="text-muted ms-4" style="font-size:0.75rem;">{{ $contract->driver_signed_at->format('d M Y H:i') }}</span>@endif
                    </li>
                </ul>

                @if($contract->isFullySigned())
                    <div class="alert alert-success mt-2" style="border-radius:8px;font-size:0.82rem;">
                        <i class="bi bi-check-circle me-1"></i> Both parties signed. Contract is active and loan has started.
                    </div>
                @endif

                <hr>
                <a href="{{ route('contracts.print', $loan) }}" class="btn btn-gold w-100 mb-2" target="_blank"><i class="bi bi-printer me-1"></i> Print Contract</a>
                <a href="{{ route('loans.show', $loan) }}" class="btn btn-outline w-100"><i class="bi bi-arrow-left me-1"></i> Back to Loan</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><strong><i class="bi bi-person me-2"></i>Owner</strong></div>
            <div class="card-body" style="font-size:0.85rem;">
                <div class="mb-1"><strong>{{ $loan->owner->name }}</strong></div>
                <div class="text-muted mb-1"><i class="bi bi-envelope me-1"></i>{{ $loan->owner->email }}</div>
                @if($loan->owner->phone)
                    <div class="text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $loan->owner->phone }}</div>
                @endif
                @if($loan->owner->nida)
                    <div class="text-muted"><i class="bi bi-card-heading me-1"></i>NIDA: {{ $loan->owner->nida }}</div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><strong><i class="bi bi-person me-2"></i>Driver</strong></div>
            <div class="card-body" style="font-size:0.85rem;">
                <div class="mb-1"><strong>{{ $loan->driver->name }}</strong></div>
                <div class="text-muted mb-1"><i class="bi bi-envelope me-1"></i>{{ $loan->driver->email }}</div>
                @if($loan->driver->phone)
                    <div class="text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $loan->driver->phone }}</div>
                @endif
                @if($loan->driver->nida)
                    <div class="text-muted"><i class="bi bi-card-heading me-1"></i>NIDA: {{ $loan->driver->nida }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
