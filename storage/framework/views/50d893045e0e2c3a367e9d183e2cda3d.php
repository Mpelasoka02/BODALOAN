<?php $__env->startSection('title', 'Loans'); ?>
<?php $__env->startSection('page-title', 'Loans'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--text);">Loans</h4>
        <p class="mb-0 mt-1" style="font-size:0.82rem;color:var(--text-secondary);">Manage and track all loan agreements</p>
    </div>
    <div></div>
</div>

<?php if(session('success')): ?>
    <div class="alert-banner green">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                    <option value="overdue" <?php echo e(request('status') === 'overdue' ? 'selected' : ''); ?>>Overdue</option>
                    <option value="defaulted" <?php echo e(request('status') === 'defaulted' ? 'selected' : ''); ?>>Defaulted</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-navy w-100"><i class="bi bi-search"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Driver</th>
                    <th>Motorcycle</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold">#<?php echo e($loan->id); ?></td>
                        <td><?php echo e($loan->driver->name ?? '-'); ?></td>
                        <td><small class="text-muted"><?php echo e($loan->motorcycle->plate_number ?? '-'); ?></small></td>
                        <td>TZS <?php echo e(number_format($loan->total_amount)); ?></td>
                        <td style="color:var(--emerald-600);">TZS <?php echo e(number_format($loan->amount_paid)); ?></td>
                        <td>TZS <?php echo e(number_format($loan->balance)); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-track flex-grow-1">
                                    <div class="progress-fill emerald" style="width:<?php echo e($loan->progress); ?>%"></div>
                                </div>
                                <small style="color:var(--text-secondary);"><?php echo e($loan->progress); ?>%</small>
                            </div>
                        </td>
                        <td><span class="badge-status <?php echo e($loan->status); ?>"><?php echo e(ucfirst($loan->status)); ?></span></td>
                        <td><a href="<?php echo e(route('loans.show', $loan)); ?>" class="btn btn-icon"><i class="bi bi-eye"></i></a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-wallet2"></i></div>
                                <h5>No loans found</h5>
                                <p>No loan agreements match your current filters.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <span style="font-size:0.82rem;color:var(--text-secondary);"><?php echo e($loans->firstItem() ?? 0); ?> to <?php echo e($loans->lastItem() ?? 0); ?> of <?php echo e($loans->total()); ?></span>
    <?php echo e($loans->withQueryString()->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/loans/index.blade.php ENDPATH**/ ?>