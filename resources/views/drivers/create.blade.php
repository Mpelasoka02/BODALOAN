@extends('layouts.app')
@section('title', 'Add Driver')
@section('page-title', 'Add Driver')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}" style="color:var(--text-muted);text-decoration:none;">Drivers</a></li>
        <li class="breadcrumb-item active" style="color:var(--text-primary);">Add</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <strong><i class="bi bi-person-plus me-2"></i>Add New Driver</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('drivers.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="07XXXXXXXX" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent"><i class="bi bi-check-lg me-1"></i>Create Driver</button>
                <a href="{{ route('drivers.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection