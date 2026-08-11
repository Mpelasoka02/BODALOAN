<?php $__env->startSection('title', 'Add Bodaboda'); ?>
<?php $__env->startSection('page-title', 'Register a New Bodaboda'); ?>

<?php $__env->startSection('content'); ?>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('owner.vehicles')); ?>" style="color:var(--text-secondary);text-decoration:none;">My Bodabodas</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">Register</li>
    </ol>
</nav>

<?php if($errors->any()): ?>
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
        <div style="font-weight:700;color:#991b1b;margin-bottom:6px;font-size:0.88rem;"><i class="bi bi-exclamation-triangle me-1"></i> Please fix the following:</div>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="color:#991b1b;font-size:0.82rem;padding:2px 0;"><?php echo e($error); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

<div class="card" style="padding:24px;">
    <form method="POST" action="<?php echo e(route('owner.vehicles.store')); ?>" enctype="multipart/form-data" id="createForm">
        <?php echo csrf_field(); ?>

        <div style="background:var(--page-bg);border-radius:12px;padding:20px;margin-bottom:20px;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-bicycle" style="color:var(--navy-700);"></i> Bodaboda Details
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Plate Number <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="plate_number" class="form-control" placeholder="e.g. T 452 ABC" value="<?php echo e(old('plate_number')); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Make &amp; Model <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="make" class="form-control" placeholder="e.g. TVS HLX 125" value="<?php echo e(old('make')); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Year <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="year" class="form-control" min="2000" max="<?php echo e(date('Y')); ?>" placeholder="e.g. 2023" value="<?php echo e(old('year')); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Color</label>
                    <input type="text" name="color" class="form-control" placeholder="e.g. Black" value="<?php echo e(old('color')); ?>">
                </div>
            </div>
        </div>

        <div style="background:var(--page-bg);border-radius:12px;padding:20px;margin-bottom:20px;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-geo-alt" style="color:var(--navy-700);"></i> Pickup Location <span style="color:var(--danger);">*</span>
            </div>
            <p style="font-size:0.82rem;color:var(--text-secondary);margin:0 0 14px;">The driver needs this location to find and pick up the bodaboda.</p>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Location Name <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="location_name" id="locationName" class="form-control" placeholder="e.g. Kariakoo Market, Dar es Salaam" value="<?php echo e(old('location_name')); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">&nbsp;</label>
                    <button type="button" class="btn btn-outline-navy w-100" onclick="useMyLocation()"><i class="bi bi-crosshair me-1"></i> Use My Current Location</button>
                </div>
                <input type="hidden" name="latitude" id="latitude" value="<?php echo e(old('latitude')); ?>">
                <input type="hidden" name="longitude" id="longitude" value="<?php echo e(old('longitude')); ?>">
                <div class="col-12">
                    <div id="locationStatus" style="display:none;background:var(--emerald-100,#E3F9EF);border:1px solid #A7F3D0;border-radius:8px;padding:10px 14px;font-size:0.82rem;color:#065f46;">
                        <i class="bi bi-check-circle-fill me-1"></i> Location detected: <span id="coordsText"></span>
                    </div>
                    <div id="locationError" style="display:none;background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:10px 14px;font-size:0.82rem;color:#991B1B;margin-top:8px;">
                        <i class="bi bi-exclamation-circle me-1"></i> Please set the pickup location before registering.
                    </div>
                </div>
            </div>
        </div>

        <div style="background:var(--page-bg);border-radius:12px;padding:20px;margin-bottom:20px;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-cash-stack" style="color:var(--navy-700);"></i> Loan Terms
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Loan Price (TZS) <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="loan_amount" id="loanAmount" class="form-control" placeholder="e.g. 2400000" value="<?php echo e(old('loan_amount')); ?>" min="0" required oninput="calcWeeks()">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Pay Per Week (TZS) <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="weekly_amount" id="weeklyAmount" class="form-control" placeholder="e.g. 46000" value="<?php echo e(old('weekly_amount')); ?>" min="1" required oninput="calcWeeks()">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Weeks to Complete</label>
                    <div id="weeksResult" style="background:var(--navy-50,#eef2ff);border:2px solid var(--navy-200,#c7d2fe);border-radius:10px;padding:12px 16px;font-size:1.6rem;font-weight:800;color:var(--navy-900);min-height:52px;display:flex;align-items:center;justify-content:center;text-align:center;cursor:default;">
                        --
                    </div>
                    <input type="hidden" name="loan_duration_weeks" id="loanDurationWeeks" value="<?php echo e(old('loan_duration_weeks')); ?>">
                </div>
            </div>
            <div id="weeksBreakdown" style="display:none;background:#fff;border-radius:8px;padding:12px 16px;margin-top:4px;font-size:0.82rem;color:var(--text-secondary);border:1px solid var(--border);">
                <span id="breakdownText"></span>
            </div>
        </div>

        <div style="background:var(--page-bg);border-radius:12px;padding:20px;margin-bottom:20px;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-cloud-upload" style="color:var(--navy-700);"></i> Photo <span style="color:var(--danger);">*</span>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Bodaboda Photo <small>(jpg/png, max 10MB)</small></label>
                    <input type="file" name="photo" class="form-control" accept="image/*" required>
                    <small style="color:var(--text-muted);font-size:0.72rem;">Take a clear photo of the bodaboda. Accepted: JPG, PNG, WEBP, HEIC.</small>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-gold" onclick="return validateLocation()"><i class="bi bi-check-lg me-1"></i> Register Bodaboda</button>
            <a href="<?php echo e(route('owner.vehicles')); ?>" class="btn btn-outline"><i class="bi bi-x-lg me-1"></i>Cancel</a>
        </div>
    </form>
</div>

<script>
function validateLocation() {
    var lat = document.getElementById('latitude').value;
    var lng = document.getElementById('longitude').value;
    var err = document.getElementById('locationError');
    if (!lat || !lng) {
        err.style.display = 'block';
        return false;
    }
    err.style.display = 'none';
    return true;
}

function calcWeeks() {
    var price = parseFloat(document.getElementById('loanAmount').value) || 0;
    var weekly = parseFloat(document.getElementById('weeklyAmount').value) || 0;
    var result = document.getElementById('weeksResult');
    var hidden = document.getElementById('loanDurationWeeks');
    var breakdown = document.getElementById('weeksBreakdown');
    var breakdownText = document.getElementById('breakdownText');

    if (price <= 0 || weekly <= 0) {
        result.innerHTML = '--';
        result.style.color = 'var(--navy-900)';
        result.style.borderColor = 'var(--navy-200,#c7d2fe)';
        hidden.value = '';
        breakdown.style.display = 'none';
        return;
    }

    var weeks = Math.ceil(price / weekly);
    hidden.value = weeks;

    result.innerHTML = weeks + ' <span style="font-size:0.7rem;font-weight:600;color:var(--text-secondary);margin-left:4px;">weeks</span>';
    result.style.color = 'var(--navy-900)';
    result.style.borderColor = 'var(--gold,#d4a843)';
    result.style.background = '#fffbe6';

    var totalPaid = weeks * weekly;
    var surplus = totalPaid - price;
    breakdownText.innerHTML = '<i class="bi bi-calculator me-1"></i>' + 'TZS ' + price.toLocaleString() + ' &divide; TZS ' + weekly.toLocaleString() + ' = <strong>' + weeks + ' weeks</strong>'
        + (surplus > 0 ? ' &middot; Total paid: TZS ' + totalPaid.toLocaleString() + ' (TZS ' + surplus.toLocaleString() + ' surplus)' : ' &middot; Exact fit');
    breakdown.style.display = 'block';
}

calcWeeks();

function useMyLocation() {
    var btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Detecting...';
    navigator.geolocation.getCurrentPosition(function(pos) {
        document.getElementById('latitude').value = pos.coords.latitude.toFixed(7);
        document.getElementById('longitude').value = pos.coords.longitude.toFixed(7);
        var status = document.getElementById('locationStatus');
        document.getElementById('coordsText').textContent = pos.coords.latitude.toFixed(5) + ', ' + pos.coords.longitude.toFixed(5);
        status.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Location Detected';
        btn.classList.remove('btn-outline-navy');
        btn.classList.add('btn-outline-success');
    }, function(err) {
        alert('Could not detect location: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-crosshair me-1"></i> Use My Current Location';
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/owner/create-vehicle.blade.php ENDPATH**/ ?>