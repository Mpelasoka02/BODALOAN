<?php $__env->startSection('title', 'Notifications'); ?>
<?php $__env->startSection('page-title', 'Notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0 fw-bold">Notifications</h5>
    </div>
    <div>
        <form method="POST" action="<?php echo e(route('notifications.markAllRead')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline btn-sm"><i class="bi bi-check-all me-1"></i>Mark All Read</button>
        </form>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success" style="border-radius:10px;font-size:0.85rem;"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr style="<?php echo e($n->read_at ? '' : 'background:var(--primary-light);'); ?>">
                        <td><?php echo e($n->created_at->format('M d, Y H:i')); ?></td>
                        <td>
                            <?php
                                $icon = match($n->type) {'payment' => 'bi-credit-card', 'loan' => 'bi-wallet2', 'contract' => 'bi-file-earmark-text', 'system' => 'bi-gear', default => 'bi-bell'};
                                $color = match($n->type) {'payment' => 'var(--success)', 'loan' => 'var(--info)', 'contract' => 'var(--accent)', 'system' => 'var(--warning)', default => 'var(--text-muted)'};
                            ?>
                            <i class="bi <?php echo e($icon); ?>" style="color:<?php echo e($color); ?>;"></i>
                            <span class="ms-1"><?php echo e(ucfirst($n->type)); ?></span>
                        </td>
                        <td><?php echo e($n->message); ?></td>
                        <td>
                            <?php if($n->read_at): ?>
                                <span class="badge-status disabled">Read</span>
                            <?php else: ?>
                                <span class="badge-status active">New</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(!$n->read_at): ?>
                                <form method="POST" action="<?php echo e(route('notifications.markAsRead', $n)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-icon"><i class="bi bi-check"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No notifications.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <span class="pagination-info"><?php echo e($notifications->firstItem() ?? 0); ?> to <?php echo e($notifications->lastItem() ?? 0); ?> of <?php echo e($notifications->total()); ?></span>
    <?php echo e($notifications->withQueryString()->links()); ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/notifications/index.blade.php ENDPATH**/ ?>