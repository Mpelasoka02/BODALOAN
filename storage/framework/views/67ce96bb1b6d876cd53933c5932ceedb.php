<?php $__env->startSection('title', 'Driver Approval'); ?>
<?php $__env->startSection('page-title', 'Pending Drivers'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.drivers', ['approval_status' => 'pending'])); ?>" class="btn btn-sm <?php echo e(request('approval_status', 'pending') == 'pending' ? 'btn-gold' : 'btn-outline'); ?>">Pending</a>
        <a href="<?php echo e(route('admin.drivers', ['approval_status' => 'approved'])); ?>" class="btn btn-sm <?php echo e(request('approval_status') == 'approved' ? 'btn-emerald' : 'btn-outline'); ?>">Approved</a>
        <a href="<?php echo e(route('admin.drivers', ['approval_status' => 'suspended'])); ?>" class="btn btn-sm <?php echo e(request('approval_status') == 'suspended' ? 'btn-danger' : 'btn-outline'); ?>">Suspended</a>
    </div>
</div>

<div class="card" style="border-radius:var(--radius-lg);">
    <?php if($drivers->count()): ?>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Phone</th><th>Submitted</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="font-weight:600;"><?php echo e($driver->name); ?></td>
                    <td style="color:var(--text-secondary);"><?php echo e($driver->email); ?></td>
                    <td style="color:var(--text-secondary);"><?php echo e($driver->phone ?? '—'); ?></td>
                    <td style="color:var(--text-secondary);"><?php echo e($driver->created_at->format('M d, Y')); ?></td>
                    <td>
                        <?php if($driver->approval_status === 'pending'): ?>
                            <span class="badge-status pending">Pending</span>
                        <?php elseif($driver->approval_status === 'approved'): ?>
                            <span class="badge-status approved">Approved</span>
                        <?php else: ?>
                            <span class="badge-status suspended">Suspended</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-2 flex-wrap">
                            <form method="POST" action="<?php echo e(route('admin.drivers.approve', $driver)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-xs btn-emerald">Approve</button>
                            </form>
                            <form method="POST" action="<?php echo e(route('admin.drivers.reject', $driver)); ?>" class="d-flex gap-1">
                                <?php echo csrf_field(); ?>
                                <input type="text" name="rejection_reason" placeholder="Reason..." required class="form-control" style="padding:5px 10px;font-size:0.78rem;width:140px;">
                                <button type="submit" class="btn btn-xs btn-danger">Reject</button>
                            </form>
                            <a href="<?php echo e(route('chat.start.direct', $driver)); ?>" class="btn btn-xs btn-outline" title="Chat with driver"><i class="bi bi-chat-dots"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-people"></i></div>
        <h5>No drivers found</h5>
        <p>No drivers match the current filter.</p>
    </div>
    <?php endif; ?>
</div>

<div class="mt-3"><?php echo e($drivers->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/admin/drivers.blade.php ENDPATH**/ ?>