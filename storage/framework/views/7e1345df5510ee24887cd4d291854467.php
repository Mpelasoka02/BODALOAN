<?php $__env->startSection('title', 'Contracts — BodaLink'); ?>
<?php $__env->startSection('page-title', 'My Contracts'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0 small">Review and manage your contracts</p>
    </div>
</div>

<?php if(session('success')): ?>
    <div style="background:#E3F9EF;border:1px solid #A7F3D0;border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#065f46;font-size:0.88rem;">
        <i class="bi bi-check-circle-fill me-1"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if($loans->count() > 0): ?>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:0.85rem;">
            <thead style="background:var(--page-bg);">
                <tr>
                    <th>Contract #</th>
                    <th>Motorcycle</th>
                    <th>Driver</th>
                    <th>Status</th>
                    <th>Owner Approved</th>
                    <th>Signatures</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $contract = $loan->contract; ?>
                <tr>
                    <td style="font-weight:700;color:var(--text);"><?php echo e($contract->contract_number ?? '—'); ?></td>
                    <td>
                        <div style="font-weight:600;"><?php echo e($loan->motorcycle->make); ?> <?php echo e($loan->motorcycle->model); ?></div>
                        <div style="color:var(--text-secondary);font-size:0.78rem;"><?php echo e($loan->motorcycle->plate_number); ?></div>
                    </td>
                    <td><?php echo e($loan->driver->name); ?></td>
                    <td>
                        <?php if($contract->status === 'pending'): ?>
                            <span style="background:#FEF3C7;color:#92400E;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">Pending Review</span>
                        <?php elseif($contract->status === 'approved'): ?>
                            <span style="background:#DBEAFE;color:#1E40AF;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">Approved — Awaiting Signatures</span>
                        <?php elseif($contract->status === 'partially_signed'): ?>
                            <span style="background:#FEF3C7;color:#92400E;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">Partially Signed</span>
                        <?php elseif($contract->status === 'fully_signed'): ?>
                            <span style="background:#D1FAE5;color:#065F46;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">Fully Signed</span>
                        <?php elseif($contract->status === 'rejected'): ?>
                            <span style="background:#FEE2E2;color:#991B1B;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($contract->owner_approved_at): ?>
                            <span style="color:var(--emerald-600,#059669);font-weight:600;"><i class="bi bi-check-circle-fill me-1"></i>Approved</span>
                        <?php else: ?>
                            <span style="color:var(--text-secondary);">Not yet</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.8rem;">
                        <?php if($contract->owner_signed_at): ?>
                            <span style="color:var(--emerald-600,#059669);"><i class="bi bi-check-circle-fill me-1"></i>You</span>
                        <?php else: ?>
                            <span style="color:var(--text-secondary);">You: Not signed</span>
                        <?php endif; ?>
                        <br>
                        <?php if($contract->driver_signed_at): ?>
                            <span style="color:var(--emerald-600,#059669);"><i class="bi bi-check-circle-fill me-1"></i>Driver</span>
                        <?php else: ?>
                            <span style="color:var(--text-secondary);">Driver: Not signed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <?php if($contract->status === 'pending'): ?>
                                <form method="POST" action="<?php echo e(route('owner.contracts.approve', $loan)); ?>" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm" style="background:var(--emerald-600,#059669);color:#fff;padding:5px 12px;font-size:0.78rem;font-weight:600;">
                                        <i class="bi bi-check-lg me-1"></i>Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;padding:5px 12px;font-size:0.78rem;font-weight:600;" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo e($loan->id); ?>">
                                    <i class="bi bi-x-lg me-1"></i>Reject
                                </button>
                                <div class="modal fade" id="rejectModal<?php echo e($loan->id); ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="border-radius:14px;">
                                            <form method="POST" action="<?php echo e(route('owner.contracts.reject', $loan)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                                                    <h6 class="modal-title fw-bold">Reject Contract</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label fw-semibold">Reason for rejection</label>
                                                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Explain why you're rejecting this contract..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn" style="background:#FEE2E2;color:#991B1B;">Reject Contract</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="<?php echo e(route('contracts.show', $loan)); ?>" class="btn btn-sm btn-outline-navy" style="padding:5px 12px;font-size:0.78rem;">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('contracts.print', $loan)); ?>" class="btn btn-sm btn-outline" style="padding:5px 12px;font-size:0.78rem;" target="_blank">
                                <i class="bi bi-printer me-1"></i>Print
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?php echo e($loans->links()); ?></div>
<?php else: ?>
<div class="card border-0 shadow-sm text-center py-5">
    <i class="bi bi-file-earmark-text display-4 text-muted"></i>
    <h5 class="mt-3">No Contracts Yet</h5>
    <p class="text-muted">Contracts will appear here once drivers apply to your bodabodas and you accept them.</p>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/owner/contracts.blade.php ENDPATH**/ ?>