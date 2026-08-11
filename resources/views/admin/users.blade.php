@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">System Users</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users', ['export' => 'csv']) }}" class="btn btn-outline btn-sm">
            <i class="bi bi-download"></i> Export
        </a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus"></i> Add User
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="border-radius:10px;font-size:0.85rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger" style="border-radius:10px;font-size:0.85rem;">{{ session('error') }}</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, phone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="driver" {{ request('role') == 'driver' ? 'selected' : '' }}>Driver</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="approval_status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="suspended" {{ request('approval_status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Contact</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Verification</th>
                    <th>Active</th>
                    <th>Joined</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($u->profile_photo_url)
                                    <img src="{{ $u->profile_photo_url }}" style="width:32px;height:32px;object-fit:cover;border-radius:50%;border:1px solid var(--border);">
                                @else
                                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.75rem;">{{ substr($u->name, 0, 1) }}</div>
                                @endif
                                <span class="fw-semibold">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted d-block">{{ $u->email }}</small>
                            <small class="text-muted">{{ $u->phone ?? '' }}</small>
                        </td>
                        <td>
                            @if($u->isAdmin())
                                <span class="badge" style="background:#eef2ff;color:#4f46e5;font-size:0.72rem;padding:3px 10px;border-radius:6px;font-weight:600;">Admin</span>
                            @elseif($u->isOwner())
                                <span class="badge" style="background:var(--info-bg);color:var(--info);font-size:0.72rem;padding:3px 10px;border-radius:6px;font-weight:600;">Owner</span>
                            @else
                                <span class="badge" style="background:var(--success-bg);color:var(--success);font-size:0.72rem;padding:3px 10px;border-radius:6px;font-weight:600;">Driver</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status {{ $u->approval_status }}">{{ ucfirst($u->approval_status) }}</span>
                            @if($u->rejection_reason)
                                <small class="text-danger d-block" style="font-size: 0.7rem;">{{ Str::limit($u->rejection_reason, 40) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($u->isAdmin())
                                <small class="text-muted">—</small>
                            @elseif($u->hasSubmittedVerification())
                                <span class="badge-status pending">Pending Review</span>
                                <small class="text-muted d-block" style="font-size:0.68rem;">{{ $u->verification_submitted_at->diffForHumans() }}</small>
                            @elseif($u->hasVerificationDocuments())
                                <span class="badge-status approved">Verified</span>
                            @elseif($u->rejection_reason)
                                <span class="badge-status rejected">Rejected</span>
                                <small class="text-danger d-block" style="font-size:0.68rem;">{{ Str::limit($u->rejection_reason, 30) }}</small>
                            @else
                                <span class="badge-status" style="background:#f1f5f9;color:#64748b;">Not Started</span>
                            @endif
                        </td>
                        <td>
                            @if($u->is_active)
                                <span class="badge-status active">Active</span>
                            @else
                                <span class="badge-status disabled">Disabled</span>
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $u->created_at->format('M d, Y') }}</small></td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end" style="border-radius: 10px; min-width: 220px;">
                                    @if($u->hasSubmittedVerification())
                                        <li>
                                            <a href="{{ route('admin.users.verify', $u) }}" class="dropdown-item text-primary">
                                                <i class="bi bi-shield-check me-2"></i>Review Documents
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                    @endif
                                    @if($u->approval_status === 'pending' && !$u->hasSubmittedVerification())
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.approve', $u) }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i>Approve</button>
                                            </form>
                                        </li>
                                    @endif
                                    @if(!$u->isAdmin() && $u->approval_status !== 'suspended')
                                        <li>
                                            <button class="dropdown-item text-warning" data-bs-toggle="modal" data-bs-target="#suspendModal{{ $u->id }}">
                                                <i class="bi bi-slash-circle me-2"></i>Suspend
                                            </button>
                                        </li>
                                    @endif
                                    @if(!$u->isAdmin())
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.toggleActive', $u) }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-toggle-{{ $u->is_active ? 'on' : 'off' }} me-2"></i>
                                                    {{ $u->is_active ? 'Disable' : 'Enable' }} Account
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $u->id }}">
                                            <i class="bi bi-key me-2"></i>Reset Password
                                        </button>
                                    </li>
                                    @if(!$u->isAdmin())
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete this user? This cannot be undone.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete User</button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries</small>
        {{ $users->withQueryString()->links() }}
    </div>
</div>

<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form method="POST" action="{{ route('admin.users.create') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control" required placeholder="+255...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="">Select role...</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Approval Status</label>
                        <select name="approval_status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                        <div class="form-text">Minimum 8 characters. User can change after first login.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($users as $u)
    @if(!$u->isAdmin() && $u->approval_status !== 'suspended')
    <div class="modal fade" id="suspendModal{{ $u->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 12px;">
                <form method="POST" action="{{ route('admin.users.suspend', $u) }}">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title fw-bold">Suspend {{ $u->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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

    <div class="modal fade" id="resetPasswordModal{{ $u->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 12px;">
                <form method="POST" action="{{ route('admin.users.resetPassword', $u) }}">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title fw-bold">Reset Password - {{ $u->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="8">
                        <div class="form-text">Minimum 8 characters</div>
                        <label class="form-label fw-semibold mt-2">Confirm Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
