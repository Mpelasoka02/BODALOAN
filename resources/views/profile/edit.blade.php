@extends('layouts.app')
@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item active" style="color:var(--text-primary);">Profile Settings</li>
    </ol>
</nav>

@if($user->needsProfileSetup() && !$user->isAdmin())
    <div class="alert alert-info d-flex align-items-center gap-2">
        <i class="bi bi-info-circle-fill"></i> Complete your profile with NIDA number and photos to proceed with contracts.
    </div>
@endif

<div class="card mb-4">
    <div class="card-header">
        <strong><i class="bi bi-person-circle me-2"></i>Personal Information</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIDA Number</label>
                    <input type="text" name="nida" class="form-control" value="{{ old('nida', $user->nida) }}" placeholder="National ID number">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="birthdate" class="form-control" value="{{ old('birthdate', $user->birthdate ? $user->birthdate->format('Y-m-d') : '') }}">
                </div>
                @if($user->isOwner())
                <div class="col-12">
                    <label class="form-label">Your Location</label>
                    <p style="font-size:0.8rem;color:var(--text-secondary);margin-bottom:8px;">Drivers need to find you to pick up the bodaboda.</p>
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <button type="button" class="btn btn-outline-navy btn-sm" onclick="getMyLocation()" style="white-space:nowrap;">
                            <i class="bi bi-geo-alt me-1"></i>Use My Location
                        </button>
                        <span id="locStatus" style="font-size:0.8rem;color:var(--emerald-600);display:none;"><i class="bi bi-check-circle-fill me-1"></i>Location set</span>
                        @if($user->latitude && $user->longitude)
                            <a href="https://www.google.com/maps?q={{ $user->latitude }},{{ $user->longitude }}" target="_blank" class="btn btn-outline btn-sm" style="white-space:nowrap;">
                                <i class="bi bi-map me-1"></i>View on Maps
                            </a>
                        @endif
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $user->latitude) }}" placeholder="Latitude">
                        </div>
                        <div class="col-md-4">
                            <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $user->longitude) }}" placeholder="Longitude">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="location_name" class="form-control" value="{{ old('location_name', $user->location_name) }}" placeholder="Location name (e.g. Kariakoo)">
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/jpeg,image/png">
                    @if($user->profile_photo_url)
                        <div class="mt-2"><img src="{{ $user->profile_photo_url }}" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid var(--border);"></div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">ID Photo (NIDA/Passport)</label>
                    <input type="file" name="id_photo" class="form-control" accept="image/jpeg,image/png">
                    @if($user->id_photo_url)
                        <div class="mt-2"><img src="{{ $user->id_photo_url }}" style="width:120px;height:auto;max-height:80px;object-fit:cover;border:2px solid var(--border);border-radius:4px;"></div>
                    @endif
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong><i class="bi bi-key me-2"></i>Change Password</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf @method('PATCH')
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                    <div class="form-text">Minimum 8 characters</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-shield-lock me-1"></i>Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection

@if($user->isOwner())
@push('scripts')
<script>
function getMyLocation() {
    var status = document.getElementById('locStatus');
    status.style.display = 'none';
    if (!navigator.geolocation) { alert('Geolocation not supported by your browser.'); return; }
    navigator.geolocation.getCurrentPosition(function(pos) {
        document.querySelector('input[name="latitude"]').value = pos.coords.latitude.toFixed(6);
        document.querySelector('input[name="longitude"]').value = pos.coords.longitude.toFixed(6);
        status.style.display = 'inline';
    }, function(err) {
        alert('Could not get location: ' + err.message);
    }, { enableHighAccuracy: true, timeout: 10000 });
}
</script>
@endpush
@endif