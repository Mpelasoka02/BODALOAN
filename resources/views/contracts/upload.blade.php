@extends('layouts.app')
@section('title', 'Upload Signed Contract')
@section('page-title', 'Upload Signed Contract')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('loans.show', $loan) }}" style="color:var(--text-muted);text-decoration:none;">Loan #{{ $loan->id }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('contracts.show', $loan) }}" style="color:var(--text-muted);text-decoration:none;">Contract</a></li>
        <li class="breadcrumb-item active">Upload</li>
    </ol>
</nav>

@if(session('error'))
    <div class="alert alert-danger" style="border-radius:10px;font-size:0.85rem;">{{ session('error') }}</div>
@endif

<div class="card" style="max-width:600px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('contracts.upload', $loan) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Signed Contract (PDF) <span class="text-danger">*</span></label>
                <div class="border rounded p-4 text-center" style="border:2px dashed var(--border);background:var(--body-bg);">
                    <i class="bi bi-filetype-pdf" style="font-size:2rem;color:var(--text-muted);"></i>
                    <p class="mt-2 mb-2" style="font-size:0.85rem;color:var(--text-muted);">Upload a scanned copy of the signed contract</p>
                    <input type="file" name="signed_pdf" accept=".pdf" required class="form-control @error('signed_pdf') is-invalid @enderror" style="max-width:300px;margin:0 auto;">
                    @error('signed_pdf')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <p class="mt-2 mb-0" style="font-size:0.75rem;color:var(--text-muted);">Max 10MB, PDF only</p>
                </div>
            </div>
            <p class="text-muted" style="font-size:0.82rem;">
                <i class="bi bi-info-circle me-1"></i>
                @if($role === 'owner')
                    Uploading as <strong>Owner</strong> ({{ $loan->owner->name }}).
                @else
                    Uploading as <strong>Driver</strong> ({{ $loan->driver->name }}).
                @endif
                Both parties must sign before uploading.
            </p>
            <div class="d-flex gap-2">
                <a href="{{ route('contracts.show', $loan) }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Upload</button>
            </div>
        </form>
    </div>
</div>
@endsection