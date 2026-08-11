<?php $__env->startSection('title', 'Overdue Loans'); ?>
<?php $__env->startSection('page-title', 'Overdue & Defaulted Loans'); ?>
<?php $__env->startSection('content'); ?>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Overdue</div>
            <div class="stat-value" style="color:var(--status-overdue-text);"><?php echo e(\App\Models\Loan::where('status','overdue')->count()); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Defaulted</div>
            <div class="stat-value" style="color:var(--status-overdue-text);"><?php echo e(\App\Models\Loan::where('status','defaulted')->count()); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Total Outstanding</div>
            <div class="stat-value">TZS <?php echo e(number_format(\App\Models\Loan::whereIn('status',['overdue','defaulted'])->sum('total_amount') - \App\Models\Loan::whereIn('status',['overdue','defaulted'])->sum('amount_paid'))); ?></div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mb-3">
    <a href="<?php echo e(route('admin.overdue')); ?>" class="btn btn-sm <?php echo e(!request('status') ? 'btn-danger' : 'btn-outline'); ?>">All</a>
    <a href="<?php echo e(route('admin.overdue', ['status' => 'overdue'])); ?>" class="btn btn-sm <?php echo e(request('status') == 'overdue' ? 'btn-gold' : 'btn-outline'); ?>">Overdue</a>
    <a href="<?php echo e(route('admin.overdue', ['status' => 'defaulted'])); ?>" class="btn btn-sm <?php echo e(request('status') == 'defaulted' ? 'btn-danger' : 'btn-outline'); ?>">Defaulted</a>
</div>

<div class="card" style="border-radius:var(--radius-lg);">
    <?php if($loans->count()): ?>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Driver</th><th>Bodaboda</th><th>Loan Amount</th><th>Paid</th><th>Balance</th><th>Weeks Overdue</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="font-weight:600;"><?php echo e($loan->driver->name ?? '—'); ?></td>
                    <td><?php echo e($loan->motorcycle->plate_number ?? '—'); ?></td>
                    <td>TZS <?php echo e(number_format($loan->total_amount)); ?></td>
                    <td style="color:var(--emerald-600);">TZS <?php echo e(number_format($loan->amount_paid)); ?></td>
                    <td style="font-weight:700;color:var(--status-overdue-text);">TZS <?php echo e(number_format($loan->balance)); ?></td>
                    <td style="font-weight:600;"><?php echo e($loan->next_payment_date ? now()->diffInWeeks($loan->next_payment_date) : '—'); ?></td>
                    <td>
                        <?php if($loan->status === 'overdue'): ?>
                            <span class="badge-status overdue">Overdue</span>
                        <?php else: ?>
                            <span class="badge-status overdue">Defaulted</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-check-circle"></i></div>
        <h5>All clear!</h5>
        <p>No overdue or defaulted loans.</p>
    </div>
    <?php endif; ?>
</div>
<div class="mt-3"><?php echo e($loans->withQueryString()->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/admin/overdue.blade.php ENDPATH**/ ?>