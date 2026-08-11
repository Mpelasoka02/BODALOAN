<?php $__env->startSection('title', 'Relationships'); ?>
<?php $__env->startSection('page-title', 'Relationships'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">Relationship View</h5>
    <a href="<?php echo e(route('admin.relationships', ['export' => 'csv'])); ?>" class="btn btn-outline btn-sm">
        <i class="bi bi-download"></i> Export
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Plate number, driver, owner..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Loan Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                    <option value="overdue" <?php echo e(request('status') === 'overdue' ? 'selected' : ''); ?>>Overdue</option>
                    <option value="defaulted" <?php echo e(request('status') === 'defaulted' ? 'selected' : ''); ?>>Defaulted</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo e(route('admin.relationships')); ?>" class="btn btn-outline w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Driver</th>
                    <th>Bodaboda</th>
                    <th>Owner</th>
                    <th>Loan Status</th>
                    <th>Progress</th>
                    <th>Balance</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $relationships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width:32px;height:32px;font-size:0.7rem;"><?php echo e(substr($m->driver->name ?? '?', 0, 1)); ?></div>
                                <div>
                                    <div class="fw-semibold"><?php echo e($m->driver->name ?? 'Unassigned'); ?></div>
                                    <small class="text-muted"><?php echo e($m->driver->phone ?? ''); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><span class="fw-semibold"><?php echo e($m->plate_number); ?></span><br><small class="text-muted"><?php echo e($m->make); ?> <?php echo e($m->model); ?></small></td>
                        <td><?php echo e($m->owner->name ?? 'N/A'); ?><br><small class="text-muted"><?php echo e($m->owner->phone ?? ''); ?></small></td>
                        <td>
                            <?php if($m->loan): ?>
                                <span class="badge-status <?php echo e($m->loan->status); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $m->loan->status))); ?></span>
                            <?php else: ?>
                                <span class="badge-status disabled">No Loan</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($m->loan): ?>
                                <div class="d-flex align-items-center gap-2" style="min-width:100px;">
                                    <div class="flex-grow-1" style="height:6px;background:var(--body-bg);border-radius:4px;overflow:hidden;">
                                        <div style="width:<?php echo e($m->loan->progress); ?>%;height:100%;background:var(--primary);border-radius:4px;"></div>
                                    </div>
                                    <small class="fw-semibold" style="font-size:0.75rem;"><?php echo e($m->loan->progress); ?>%</small>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($m->loan): ?>
                                <span class="fw-semibold" style="font-size:0.85rem;">TZS <?php echo e(number_format($m->loan->balance)); ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?php echo e(route('motorcycles.show', $m)); ?>" class="btn btn-sm btn-outline" title="View"><i class="bi bi-eye"></i></a>
                            <?php if($m->loan): ?>
                                <a href="<?php echo e(route('loans.show', $m->loan)); ?>" class="btn btn-sm btn-outline" title="Loan"><i class="bi bi-wallet2"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center py-4"><div style="color:var(--text-muted);"><i class="bi bi-link-45deg" style="font-size:2rem;"></i><br><span class="mt-2 d-block">No relationships found.</span></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span class="pagination-info">Showing <?php echo e($relationships->firstItem() ?? 0); ?> to <?php echo e($relationships->lastItem() ?? 0); ?> of <?php echo e($relationships->total()); ?> entries</span>
        <?php echo e($relationships->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/admin/relationships.blade.php ENDPATH**/ ?>