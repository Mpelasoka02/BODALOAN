
<?php $__env->startSection('title', 'My Applications'); ?>
<?php $__env->startSection('page-title', 'My Applications'); ?>

<?php $__env->startSection('content'); ?>
<?php if($applications->count()): ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Motorcycle</th><th>Owner</th><th>Amount</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($app->motorcycle->plate_number ?? '-'); ?><br><small style="color:var(--text-secondary);"><?php echo e($app->motorcycle->make ?? ''); ?> <?php echo e($app->motorcycle->model ?? ''); ?></small></td>
                            <td><?php echo e($app->motorcycle->owner->name ?? '-'); ?></td>
                            <td>TZS <?php echo e(number_format($app->motorcycle->loan_amount ?? 0)); ?></td>
                            <td><?php echo e($app->created_at->format('M d, Y')); ?></td>
                            <td>
                                <span class="badge-status <?php echo e($app->status); ?>"><?php echo e(ucfirst($app->status)); ?></span>
                                <?php if($app->isRejected() && $app->admin_notes): ?>
                                    <br><small style="color:var(--text-secondary);"><?php echo e($app->admin_notes); ?></small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3"><?php echo e($applications->links()); ?></div>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-file-earmark-text"></i></div>
        <h5>No applications yet</h5>
        <p>You haven't applied for any motorcycle yet.</p>
        <a href="<?php echo e(route('home')); ?>" class="btn btn-gold"><i class="bi bi-search me-1"></i>Browse Marketplace</a>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/driver/apps.blade.php ENDPATH**/ ?>