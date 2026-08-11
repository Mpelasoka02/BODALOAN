
<?php $__env->startSection('title', 'Apply for Bodaboda'); ?>
<?php $__env->startSection('page-title', 'Apply for Bodaboda'); ?>

<?php $__env->startSection('content'); ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>" style="color:var(--text-secondary);text-decoration:none;">Marketplace</a></li>
        <li class="breadcrumb-item active">Apply</li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:36px;height:36px;border-radius:10px;background:var(--navy-900);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-motorcycle" style="font-size:1rem;color:#fff;"></i>
                </div>
                <h6 class="mb-0 fw-bold" style="color:var(--text);">Bodaboda Details</h6>
            </div>
            <table class="table table-sm mb-0">
                <tr><td style="width:45%;color:var(--text-secondary);">Plate Number</td><td class="fw-semibold"><?php echo e($motorcycle->plate_number); ?></td></tr>
                <tr><td style="color:var(--text-secondary);">Make / Model</td><td><?php echo e($motorcycle->make); ?> <?php echo e($motorcycle->model); ?></td></tr>
                <tr><td style="color:var(--text-secondary);">Loan Amount</td><td class="fw-bold" style="color:var(--navy-900);">TZS <?php echo e(number_format($motorcycle->loan_amount ?? 0)); ?></td></tr>
                <tr><td style="color:var(--text-secondary);">Weekly Payment</td><td>TZS <?php echo e(number_format($motorcycle->weekly_amount ?? 0)); ?></td></tr>
                <tr><td style="color:var(--text-secondary);">Duration</td><td><?php echo e($motorcycle->loan_duration_weeks ?? '-'); ?> weeks</td></tr>
                <tr><td style="color:var(--text-secondary);">Owner</td><td><?php echo e($motorcycle->owner->name ?? 'N/A'); ?></td></tr>
                <tr><td style="color:var(--text-secondary);">Owner Phone</td><td><?php echo e($motorcycle->owner->phone ?? 'N/A'); ?></td></tr>
            </table>
        </div>

        <div class="card mt-3" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                <div style="width:36px;height:36px;border-radius:10px;background:var(--navy-900);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-person" style="font-size:1rem;color:#fff;"></i>
                </div>
                <h6 class="mb-0 fw-bold" style="color:var(--text);">Your Profile</h6>
            </div>
            <table class="table table-sm mb-0">
                <tr><td style="width:40%;color:var(--text-secondary);">Name</td><td class="fw-semibold"><?php echo e(Auth::user()->name); ?></td></tr>
                <tr><td style="color:var(--text-secondary);">Email</td><td><?php echo e(Auth::user()->email); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="col-lg-7">
        <?php if($existingApplication): ?>
            <div class="card" style="padding:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--gold-500);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-hourglass-split" style="font-size:1rem;color:#fff;"></i>
                    </div>
                    <h6 class="mb-0 fw-bold" style="color:var(--text);">Application Pending</h6>
                </div>
                <p style="font-size:0.85rem;color:var(--text-secondary);">You already have a pending application for this bodaboda. The owner will review it and get back to you.</p>
                <div class="mt-2" style="font-size:0.82rem;">
                    <div><strong>Status:</strong> <span class="badge bg-warning text-dark"><?php echo e(ucfirst($existingApplication->status)); ?></span></div>
                    <div class="mt-1"><strong>Applied:</strong> <?php echo e($existingApplication->created_at->format('d M Y H:i')); ?></div>
                </div>
            </div>
        <?php else: ?>
            <div class="card" style="padding:24px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--gold-500);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-file-text" style="font-size:1rem;color:#fff;"></i>
                    </div>
                    <h6 class="mb-0 fw-bold" style="color:var(--text);">Submit Application</h6>
                </div>

                <div class="alert alert-info" style="border-radius:10px;font-size:0.82rem;margin-bottom:16px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Your name and email from your profile will be sent to the owner. Fill in your details below.
                </div>

                <form method="POST" action="<?php echo e(route('marketplace.apply.submit', $motorcycle)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. 0712345678" value="<?php echo e(old('phone', Auth::user()->phone)); ?>" required>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-secondary);">NIDA Number <span class="text-danger">*</span></label>
                            <input type="text" name="nida" class="form-control" placeholder="e.g. 123456789012" value="<?php echo e(old('nida', Auth::user()->nida)); ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gold w-100"><i class="bi bi-send me-2"></i>Submit Application</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3">
    <a href="<?php echo e(route('home')); ?>" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Marketplace</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/driver/apply.blade.php ENDPATH**/ ?>