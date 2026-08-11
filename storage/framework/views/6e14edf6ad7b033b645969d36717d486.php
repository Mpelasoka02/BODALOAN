<?php $__env->startSection('title', 'Edit Bodaboda'); ?>
<?php $__env->startSection('page-title', 'Edit Bodaboda'); ?>

<?php $__env->startSection('content'); ?>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('owner.vehicles')); ?>" style="color:var(--text-secondary);text-decoration:none;">My Bodabodas</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);"><?php echo e($motorcycle->plate_number); ?></li>
    </ol>
</nav>

<div class="card" style="padding:24px;">
    <form method="POST" action="<?php echo e(route('owner.vehicles.update', $motorcycle)); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div style="background:var(--page-bg);border-radius:12px;padding:20px;margin-bottom:20px;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-bicycle" style="color:var(--navy-700);"></i> Bodaboda Info
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Plate Number <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="plate_number" class="form-control" value="<?php echo e(old('plate_number', $motorcycle->plate_number)); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Make <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="make" class="form-control" value="<?php echo e(old('make', $motorcycle->make)); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Model <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="model" class="form-control" value="<?php echo e(old('model', $motorcycle->model)); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Year <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="year" class="form-control" min="2000" max="<?php echo e(date('Y')); ?>" value="<?php echo e(old('year', $motorcycle->year)); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Color <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="color" class="form-control" value="<?php echo e(old('color', $motorcycle->color)); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Engine CC <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="engine_cc" class="form-control" value="<?php echo e(old('engine_cc', $motorcycle->engine_cc)); ?>" min="0" required>
                </div>
            </div>
        </div>

        <div style="background:var(--page-bg);border-radius:12px;padding:20px;margin-bottom:20px;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-gear" style="color:var(--navy-700);"></i> Technical
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Engine Number</label>
                    <input type="text" name="engine_number" class="form-control" value="<?php echo e(old('engine_number', $motorcycle->engine_number)); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Chassis Number</label>
                    <input type="text" name="chassis_number" class="form-control" value="<?php echo e(old('chassis_number', $motorcycle->chassis_number)); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">GPS Tracker ID</label>
                    <input type="text" name="gps_device_id" class="form-control" value="<?php echo e(old('gps_device_id', $motorcycle->gps_device_id)); ?>">
                </div>
            </div>
        </div>

        <div style="background:var(--page-bg);border-radius:12px;padding:20px;margin-bottom:20px;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-geo-alt" style="color:var(--navy-700);"></i> Pickup Location
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Location Name</label>
                    <input type="text" name="location_name" id="locationName" class="form-control" placeholder="e.g. Kariakoo Market, Dar es Salaam" value="<?php echo e(old('location_name', $motorcycle->location_name)); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">&nbsp;</label>
                    <button type="button" class="btn btn-outline-navy w-100" onclick="useMyLocation()"><i class="bi bi-crosshair me-1"></i> Use My Current Location</button>
                </div>
                <input type="hidden" name="latitude" id="latitude" value="<?php echo e(old('latitude', $motorcycle->latitude)); ?>">
                <input type="hidden" name="longitude" id="longitude" value="<?php echo e(old('longitude', $motorcycle->longitude)); ?>">
                <div class="col-12">
                    <div id="locationStatus" style="display:none;background:var(--emerald-100,#E3F9EF);border:1px solid #A7F3D0;border-radius:8px;padding:10px 14px;font-size:0.82rem;color:#065f46;">
                        <i class="bi bi-check-circle-fill me-1"></i> Location set: <span id="coordsText"></span>
                    </div>
                </div>
                <?php if($motorcycle->latitude && $motorcycle->longitude): ?>
                    <div class="col-12 mt-2">
                        <a href="https://www.google.com/maps?q=<?php echo e($motorcycle->latitude); ?>,<?php echo e($motorcycle->longitude); ?>" target="_blank" style="color:var(--navy-700);font-size:0.82rem;font-weight:600;text-decoration:none;"><i class="bi bi-box-arrow-up-right me-1"></i> View current location on Google Maps</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="background:var(--page-bg);border-radius:12px;padding:20px;margin-bottom:20px;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-cash-stack" style="color:var(--navy-700);"></i> Loan Terms
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Loan Amount TZS <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="loan_amount" class="form-control" value="<?php echo e(old('loan_amount', $motorcycle->loan_amount)); ?>" min="0" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Weekly Payment TZS</label>
                    <input type="number" name="weekly_amount" class="form-control" value="<?php echo e(old('weekly_amount', $motorcycle->weekly_amount)); ?>" min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Duration in Weeks <span style="color:var(--danger);">*</span></label>
                    <input type="number" name="loan_duration_weeks" class="form-control" value="<?php echo e(old('loan_duration_weeks', $motorcycle->loan_duration_weeks)); ?>" min="1" required>
                </div>
            </div>
        </div>

        <div style="background:var(--page-bg);border-radius:12px;padding:20px;margin-bottom:20px;">
            <div style="font-size:0.9rem;font-weight:700;color:var(--navy-900);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                <i class="bi bi-cloud-upload" style="color:var(--navy-700);"></i> Documents
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Photo <small>(jpg/png)</small></label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <?php if($motorcycle->photo): ?>
                        <small style="color:var(--text-muted);font-size:0.72rem;">Current: <?php echo e(basename($motorcycle->photo)); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Registration Card <small>(pdf/jpg)</small></label>
                    <input type="file" name="registration_card" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <?php if($motorcycle->registration_card): ?>
                        <small style="color:var(--text-muted);font-size:0.72rem;">Current: <?php echo e(basename($motorcycle->registration_card)); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-secondary);">Insurance <small>(pdf/jpg)</small></label>
                    <input type="file" name="insurance" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <?php if($motorcycle->insurance): ?>
                        <small style="color:var(--text-muted);font-size:0.72rem;">Current: <?php echo e(basename($motorcycle->insurance)); ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i> Save Changes</button>
            <a href="<?php echo e(route('owner.vehicles.show', $motorcycle)); ?>" class="btn btn-outline"><i class="bi bi-x-lg me-1"></i>Cancel</a>
        </div>
    </form>
</div>

<script>
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
        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Location Updated';
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/owner/edit-vehicle.blade.php ENDPATH**/ ?>