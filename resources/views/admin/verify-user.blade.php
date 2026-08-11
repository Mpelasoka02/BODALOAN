@extends('layouts.app')
@section('title', 'User Profile - ' . $user->name)
@section('page-title', 'User Profile')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('admin.users') }}" style="color:var(--text-secondary);text-decoration:none;">Users</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">{{ $user->name }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        @if($user->profile_photo_url)
            <img src="{{ $user->profile_photo_url }}" style="width:56px;height:56px;object-fit:cover;border-radius:50%;border:3px solid var(--border);">
        @else
            <div class="user-avatar" style="width:56px;height:56px;font-size:1.4rem;">{{ substr($user->name, 0, 1) }}</div>
        @endif
        <div>
            <h4 class="mb-0 fw-bold">{{ $user->name }}</h4>
            <div class="d-flex align-items-center gap-2 mt-1">
                @if($user->isAdmin())
                    <span class="badge" style="background:#eef2ff;color:#4f46e5;font-size:0.72rem;padding:3px 10px;border-radius:6px;font-weight:600;">Admin</span>
                @elseif($user->isOwner())
                    <span class="badge" style="background:var(--info-bg);color:var(--info);font-size:0.72rem;padding:3px 10px;border-radius:6px;font-weight:600;">Owner</span>
                @else
                    <span class="badge" style="background:var(--success-bg);color:var(--success);font-size:0.72rem;padding:3px 10px;border-radius:6px;font-weight:600;">Driver</span>
                @endif
                <span class="badge-status {{ $user->approval_status }}">{{ ucfirst($user->approval_status) }}</span>
                @if($user->is_active)
                    <span class="badge-status active">Active</span>
                @else
                    <span class="badge-status disabled">Disabled</span>
                @endif
                @if($user->hasSubmittedVerification())
                    <span class="badge-status pending">Verification Pending</span>
                @endif
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        @if($user->hasSubmittedVerification())
            <form method="POST" action="{{ route('admin.users.verify.approve', $user) }}">
                @csrf
                <button type="submit" class="btn btn-emerald btn-sm" onclick="return confirm('Approve this user and activate their account?')">
                    <i class="bi bi-check-circle me-1"></i> Approve & Activate
                </button>
            </form>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-1"></i> Reject
            </button>
        @endif
        <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <strong><i class="bi bi-person-lines-fill me-2"></i>Personal Information</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Full Name</div>
                        <div style="font-size:0.95rem;font-weight:600;margin-top:3px;">{{ $user->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Email Address</div>
                        <div style="font-size:0.95rem;margin-top:3px;">{{ $user->email }}</div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Phone Number</div>
                        <div style="font-size:0.95rem;margin-top:3px;">{{ $user->phone ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">NIDA Number</div>
                        <div style="font-size:0.95rem;font-weight:600;margin-top:3px;color:{{ $user->nida ? 'var(--text)' : 'var(--text-secondary)' }};">{{ $user->nida ?? 'Not provided' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Address</div>
                        <div style="font-size:0.95rem;margin-top:3px;">{{ $user->address ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Date of Birth</div>
                        <div style="font-size:0.95rem;margin-top:3px;">{{ $user->birthdate ? $user->birthdate->format('d F Y') : '—' }}</div>
                    </div>
                    @if($user->latitude && $user->longitude)
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;"><i class="bi bi-geo-alt-fill me-1"></i>Registered Location</div>
                            <div style="font-size:0.95rem;margin-top:3px;">
                                {{ $user->location_name ?? '' }}
                                <a href="https://www.google.com/maps?q={{ $user->latitude }},{{ $user->longitude }}" target="_blank" style="color:var(--emerald-600);font-weight:600;text-decoration:none;margin-left:4px;">
                                    <i class="bi bi-box-arrow-up-right"></i> Maps
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <strong><i class="bi bi-shield-lock me-2"></i>Verification Documents</strong>
                @if($user->verification_submitted_at)
                    <small class="text-muted ms-auto">Submitted {{ $user->verification_submitted_at->diffForHumans() }}</small>
                @endif
            </div>
            <div class="card-body">
                @if($user->hasVerificationDocuments() || $user->hasSubmittedVerification())
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:10px;">Profile Photo</div>
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" style="width:140px;height:140px;object-fit:cover;border-radius:50%;border:3px solid var(--border);cursor:pointer;" data-bs-toggle="modal" data-bs-target="#profilePhotoModal">
                                <div class="mt-1"><small class="text-muted">Click to enlarge</small></div>
                            @else
                                <div class="text-center py-4" style="background:var(--page-bg);border-radius:10px;">
                                    <i class="bi bi-person-x" style="font-size:2rem;color:var(--text-secondary);"></i>
                                    <div class="mt-1"><small class="text-danger">Not uploaded</small></div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;margin-bottom:10px;">ID Photo (NIDA/Passport)</div>
                            @if($user->id_photo_url)
                                <img src="{{ $user->id_photo_url }}" style="width:100%;max-height:220px;object-fit:cover;border-radius:10px;border:3px solid var(--border);cursor:pointer;" data-bs-toggle="modal" data-bs-target="#idPhotoModal">
                                <div class="mt-1"><small class="text-muted">Click to enlarge</small></div>
                            @else
                                <div class="text-center py-4" style="background:var(--page-bg);border-radius:10px;">
                                    <i class="bi bi-image" style="font-size:2rem;color:var(--text-secondary);"></i>
                                    <div class="mt-1"><small class="text-danger">Not uploaded</small></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-exclamation-triangle" style="font-size:2rem;color:var(--gold-500);"></i>
                        <div class="fw-bold mt-2">No Documents Submitted</div>
                        <div style="font-size:0.85rem;color:var(--text-secondary);">This user has not yet submitted verification documents.</div>
                    </div>
                @endif
            </div>
        </div>

        @if($user->rejection_reason)
            <div class="card mb-4" style="border:1px solid #FECACA;">
                <div class="card-body" style="background:#FEF2F2;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill" style="color:#DC2626;"></i>
                        <div>
                            <div class="fw-bold" style="color:#991B1B;font-size:0.9rem;">Previous Rejection</div>
                            <div style="color:#991B1B;font-size:0.85rem;">{{ $user->rejection_reason }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($user->isOwner() && $user->motorcycles->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <strong><i class="bi bi-bicycle me-2"></i>Motorcycles ({{ $user->motorcycles->count() }})</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:0.85rem;">
                        <thead><tr><th>Plate</th><th>Make/Model</th><th>Year</th><th>Loan Amount</th><th>Status</th><th>Driver</th></tr></thead>
                        <tbody>
                            @foreach($user->motorcycles as $m)
                                <tr>
                                    <td class="fw-semibold">{{ $m->plate_number }}</td>
                                    <td>{{ $m->make }} {{ $m->model }}</td>
                                    <td>{{ $m->year }}</td>
                                    <td>{{ $m->loan_amount ? 'TZS ' . number_format($m->loan_amount) : '—' }}</td>
                                    <td><span class="badge-status {{ $m->status }}">{{ ucfirst($m->status) }}</span></td>
                                    <td>{{ $m->driver->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if(($user->loans->count() > 0 || $user->ownerLoans->count() > 0))
            <div class="card mb-4">
                <div class="card-header">
                    <strong><i class="bi bi-wallet2 me-2"></i>Loans ({{ $user->loans->count() + $user->ownerLoans->count() }})</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:0.85rem;">
                        <thead><tr><th>Loan #</th><th>Bodaboda</th><th>Counterparty</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($user->loans as $loan)
                                <tr>
                                    <td class="fw-semibold">#{{ $loan->id }}</td>
                                    <td>{{ $loan->motorcycle->plate_number ?? '—' }}</td>
                                    <td>Owner: {{ $loan->owner->name ?? '—' }}</td>
                                    <td>TZS {{ number_format($loan->total_amount) }}</td>
                                    <td>TZS {{ number_format($loan->amount_paid) }}</td>
                                    <td><span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span></td>
                                </tr>
                            @endforeach
                            @foreach($user->ownerLoans as $loan)
                                <tr>
                                    <td class="fw-semibold">#{{ $loan->id }}</td>
                                    <td>{{ $loan->motorcycle->plate_number ?? '—' }}</td>
                                    <td>Driver: {{ $loan->driver->name ?? '—' }}</td>
                                    <td>TZS {{ number_format($loan->total_amount) }}</td>
                                    <td>TZS {{ number_format($loan->amount_paid) }}</td>
                                    <td><span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <strong><i class="bi bi-info-circle me-2"></i>Account Summary</strong>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3" style="font-size:0.85rem;">
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Role</span>
                        <span class="fw-semibold">{{ ucfirst($user->role) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Approval Status</span>
                        <span class="badge-status {{ $user->approval_status }}">{{ ucfirst($user->approval_status) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Active</span>
                        <span class="badge-status {{ $user->is_active ? 'active' : 'disabled' }}">{{ $user->is_active ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Verified</span>
                        @if($user->hasVerificationDocuments() && !$user->hasSubmittedVerification())
                            <span class="badge-status approved">Verified</span>
                        @elseif($user->hasSubmittedVerification())
                            <span class="badge-status pending">Pending</span>
                        @else
                            <span class="badge-status" style="background:#f1f5f9;color:#64748b;">Not Started</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Email Verified</span>
                        @if($user->hasVerifiedEmail())
                            <span class="badge-status approved">Verified</span>
                        @else
                            <span class="badge-status" style="background:#f1f5f9;color:#64748b;">Unverified</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Joined</span>
                        <span>{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Last Active</span>
                        <span>{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <strong><i class="bi bi-bar-chart-line me-2"></i>Activity Stats</strong>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3" style="font-size:0.85rem;">
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Motorcycles Listed</span>
                        <span class="fw-bold">{{ $user->motorcycles->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Loans as Driver</span>
                        <span class="fw-bold">{{ $user->loans->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Loans as Owner</span>
                        <span class="fw-bold">{{ $user->ownerLoans->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color:var(--text-secondary);">Total Payments</span>
                        <span class="fw-bold">{{ $totalPayments }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($user->hasSubmittedVerification())
            <div class="card mb-4" style="border:2px solid var(--emerald-600);">
                <div class="card-header" style="background:var(--emerald-100);border-bottom-color:var(--emerald-600);">
                    <strong style="color:var(--emerald-600);"><i class="bi bi-shield-check me-2"></i>Verification Actions</strong>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <form method="POST" action="{{ route('admin.users.verify.approve', $user) }}">
                        @csrf
                        <button type="submit" class="btn btn-emerald w-100" onclick="return confirm('Approve this user? They will have full access to all services.')">
                            <i class="bi bi-check-circle me-1"></i> Approve & Activate
                        </button>
                    </form>
                    <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle me-1"></i> Reject Documents
                    </button>
                </div>
            </div>
        @elseif(!$user->isAdmin() && $user->approval_status === 'pending')
            <div class="card mb-4" style="border:2px solid var(--gold-500);">
                <div class="card-header" style="background:var(--gold-100);border-bottom-color:var(--gold-500);">
                    <strong style="color:var(--gold-500);"><i class="bi bi-person-check me-2"></i>Approval Actions</strong>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                        @csrf
                        <button type="submit" class="btn btn-emerald w-100" onclick="return confirm('Approve this account?')">
                            <i class="bi bi-check-circle me-1"></i> Approve Account
                        </button>
                    </form>
                    <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle me-1"></i> Reject
                    </button>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <strong><i class="bi bi-gear me-2"></i>Admin Actions</strong>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                @if(!$u->isAdmin() ?? true)
                    @if(!$user->isAdmin() && $user->approval_status !== 'suspended')
                        <button class="btn btn-outline-gold btn-sm w-100" data-bs-toggle="modal" data-bs-target="#suspendModal">
                            <i class="bi bi-slash-circle me-1"></i> Suspend Account
                        </button>
                    @endif
                    @if(!$user->isAdmin())
                        <form method="POST" action="{{ route('admin.users.toggleActive', $user) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm w-100">
                                <i class="bi bi-toggle-{{ $user->is_active ? 'on' : 'off' }} me-1"></i>
                                {{ $user->is_active ? 'Disable' : 'Enable' }} Account
                            </button>
                        </form>
                    @endif
                    <button class="btn btn-outline btn-sm w-100" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="bi bi-key me-1"></i> Reset Password
                    </button>
                    @if(!$user->isAdmin())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-trash me-1"></i> Delete User</button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

@if($user->profile_photo_url)
<div class="modal fade" id="profilePhotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;background:transparent;border:none;">
            <div class="modal-body p-0 text-center">
                <img src="{{ $user->profile_photo_url }}" style="width:100%;max-height:80vh;object-fit:contain;border-radius:12px;">
            </div>
            <div class="text-center mb-2">
                <button type="button" class="btn btn-sm btn-outline" data-bs-dismiss="modal" style="border-radius:20px;">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

@if($user->id_photo_url)
<div class="modal fade" id="idPhotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:12px;background:transparent;border:none;">
            <div class="modal-body p-0 text-center">
                <img src="{{ $user->id_photo_url }}" style="width:100%;max-height:80vh;object-fit:contain;border-radius:12px;">
            </div>
            <div class="text-center mb-2">
                <button type="button" class="btn btn-sm btn-outline" data-bs-dismiss="modal" style="border-radius:20px;">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

@if(!$user->isAdmin() && $user->approval_status !== 'suspended')
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title fw-bold">Suspend {{ $user->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Reason for suspension</label>
                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Enter reason..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Suspend Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <form method="POST" action="{{ route('admin.users.resetPassword', $user) }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title fw-bold">Reset Password - {{ $user->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">New Password</label>
                    <input type="password" name="new_password" class="form-control" required minlength="8">
                    <label class="form-label fw-semibold mt-2">Confirm Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <form method="POST" action="{{ $user->hasSubmittedVerification() ? route('admin.users.verify.reject', $user) : route('admin.users.suspend', $user) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2 text-danger"></i>Reject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Reason <span style="color:#DC2626;">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Explain the reason..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
