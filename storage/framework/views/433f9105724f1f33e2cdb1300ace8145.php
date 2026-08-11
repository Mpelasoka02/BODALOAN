<?php $__env->startSection('title', 'Record Payment'); ?>
<?php $__env->startSection('page-title', 'Record Payment'); ?>

<?php $__env->startSection('content'); ?>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="background:none;padding:0;margin:0;font-size:0.82rem;">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>" style="color:var(--text-secondary);text-decoration:none;">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('payments.index')); ?>" style="color:var(--text-secondary);text-decoration:none;">Payments</a></li>
        <li class="breadcrumb-item active" style="color:var(--text);">Record</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <strong style="font-size:0.85rem;"><i class="bi bi-credit-card-2-front me-2" style="color:var(--gold-500);"></i>Submit Payment</strong>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('payments.store')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <?php if(auth()->user()->isDriver()): ?>
                <input type="hidden" name="loan_id" value="<?php echo e($loan->id); ?>">
                <div class="alert-banner blue mb-4">
                    <div>
                        <p class="mb-1"><strong>Motorcycle:</strong> <?php echo e($loan->motorcycle->plate_number ?? '-'); ?></p>
                        <p class="mb-1"><strong>Weekly Installment:</strong> TZS <?php echo e(number_format($loan->weekly_installment)); ?></p>
                        <p class="mb-0"><strong>Outstanding Balance:</strong> TZS <?php echo e(number_format($loan->balance)); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Loan <span class="text-danger">*</span></label>
                    <select name="loan_id" class="form-select" required>
                        <option value="">Select driver's active loan</option>
                        <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($l->id); ?>" <?php echo e(old('loan_id', $selectedLoanId ?? '') == $l->id ? 'selected' : ''); ?>>
                                <?php echo e($l->motorcycle->plate_number ?? '-'); ?> — <?php echo e($l->driver->name ?? '-'); ?> (TZS <?php echo e(number_format($l->balance)); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" value="<?php echo e(old('amount', isset($loan) ? (int)$loan->weekly_installment : '')); ?>" required min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control" value="<?php echo e(old('payment_date', date('Y-m-d'))); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select name="method" class="form-select" required>
                        <option value="">Select method</option>
                        <option value="cash" <?php echo e(old('method') == 'cash' ? 'selected' : ''); ?>>Cash</option>
                        <option value="mpesa" <?php echo e(old('method') == 'mpesa' ? 'selected' : ''); ?>>M-Pesa</option>
                        <option value="tigo_pesa" <?php echo e(old('method') == 'tigo_pesa' ? 'selected' : ''); ?>>Tigo Pesa</option>
                        <option value="airmoney" <?php echo e(old('method') == 'airmoney' ? 'selected' : ''); ?>>Airtel Money</option>
                        <option value="halopesa" <?php echo e(old('method') == 'halopesa' ? 'selected' : ''); ?>>HaloPesa</option>
                        <option value="bank" <?php echo e(old('method') == 'bank' ? 'selected' : ''); ?>>Bank Transfer</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Reference Number</label>
                    <input type="text" name="reference_number" class="form-control" placeholder="Transaction ID / receipt number..." value="<?php echo e(old('reference_number')); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Receipt / Risit <span class="text-danger">*</span></label>
                    <input type="file" name="receipt" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    <small class="form-text">Upload a photo or PDF of your payment receipt. This will be reviewed by admin before your payment is confirmed.</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes..."><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="alert-banner blue mb-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle" style="color:var(--status-assigned-text);"></i>
                    <span>Payments are subject to verification before being applied to your loan balance.</span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-gold"><i class="bi bi-send me-1"></i>Submit Payment</button>
                <a href="<?php echo e(route('payments.index')); ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/payments/create.blade.php ENDPATH**/ ?>