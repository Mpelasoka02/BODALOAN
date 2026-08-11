<?php $__env->startSection('title', 'Bodaboda Details'); ?>
<?php $__env->startSection('page-title', $motorcycle->make . ' ' . $motorcycle->model); ?>

<?php $__env->startSection('content'); ?>

<div class="mb-3">
    <a href="<?php echo e(route('owner.vehicles')); ?>" class="btn btn-sm btn-outline" style="border-radius:8px;">
        <i class="bi bi-arrow-left me-1"></i> Back to My Bodabodas
    </a>
</div>

<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge-status <?php echo e($motorcycle->verification_status); ?>" style="font-size:0.8rem;padding:5px 14px;"><?php echo e(ucfirst(str_replace('_', ' ', $motorcycle->verification_status))); ?></span>
                <span class="badge-status <?php echo e($motorcycle->status); ?>" style="font-size:0.8rem;padding:5px 14px;"><?php echo e(ucfirst($motorcycle->status)); ?></span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('owner.vehicles.edit', $motorcycle)); ?>" class="btn btn-sm btn-outline-navy"><i class="bi bi-pencil me-1"></i> Edit</a>
                <?php if($motorcycle->status !== 'assigned' && !$motorcycle->loan): ?>
                <form action="<?php echo e(route('owner.vehicles.destroy', $motorcycle)); ?>" method="POST" onsubmit="return confirm('Remove this bodaboda?');" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i> Remove</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div style="padding:24px;">
                <?php if($motorcycle->photo): ?>
                    <div style="width:100%;border-radius:12px;overflow:hidden;background:var(--page-bg);margin-bottom:24px;">
                        <img src="<?php echo e(asset('storage/' . $motorcycle->photo)); ?>" alt="<?php echo e($motorcycle->plate_number); ?>" style="width:100%;height:auto;display:block;max-height:400px;object-fit:cover;">
                    </div>
                <?php endif; ?>

                <h6 style="font-size:0.82rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);">
                    <i class="bi bi-bicycle me-1" style="color:var(--navy-700);"></i> Vehicle Information
                </h6>

                <div class="row g-4">
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Plate Number</div>
                        <div class="detail-value"><?php echo e($motorcycle->plate_number); ?></div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Make</div>
                        <div class="detail-value"><?php echo e($motorcycle->make); ?></div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Model</div>
                        <div class="detail-value"><?php echo e($motorcycle->model); ?></div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Year</div>
                        <div class="detail-value"><?php echo e($motorcycle->year); ?></div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Color</div>
                        <div class="detail-value"><?php echo e($motorcycle->color ?: '—'); ?></div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Engine CC</div>
                        <div class="detail-value"><?php echo e($motorcycle->engine_cc ?: '—'); ?></div>
                    </div>
                    <?php if($motorcycle->engine_number): ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Engine Number</div>
                        <div class="detail-value"><?php echo e($motorcycle->engine_number); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($motorcycle->chassis_number): ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Chassis Number</div>
                        <div class="detail-value"><?php echo e($motorcycle->chassis_number); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($motorcycle->location_name || ($motorcycle->latitude && $motorcycle->longitude)): ?>
                    <div class="col-sm-6 col-md-4">
                        <div class="detail-label">Pickup Location</div>
                        <div class="detail-value"><?php echo e($motorcycle->location_name ?: 'Set'); ?></div>
                        <?php if($motorcycle->latitude && $motorcycle->longitude): ?>
                            <a href="https://www.google.com/maps?q=<?php echo e($motorcycle->latitude); ?>,<?php echo e($motorcycle->longitude); ?>" target="_blank" style="font-size:0.78rem;color:var(--navy-700);font-weight:600;text-decoration:none;">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open in Maps
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4">
        <?php if($motorcycle->loan): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div style="padding:24px;">
                    <h6 style="font-size:0.82rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);">
                        <i class="bi bi-wallet2 me-1" style="color:var(--navy-700);"></i> Loan & Driver
                    </h6>

                    <div style="margin-bottom:20px;">
                        <div class="detail-label">Assigned Driver</div>
                        <?php if($motorcycle->driver): ?>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div style="width:40px;height:40px;border-radius:10px;background:var(--navy-900);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;"><?php echo e(substr($motorcycle->driver->name, 0, 1)); ?></div>
                                <div>
                                    <div style="font-size:0.9rem;font-weight:600;color:var(--text);"><?php echo e($motorcycle->driver->name); ?></div>
                                    <div style="font-size:0.78rem;color:var(--text-secondary);"><?php echo e($motorcycle->driver->phone ?? 'No phone'); ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="font-size:0.88rem;color:var(--text-muted);margin-top:4px;">No driver assigned</div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-bottom:16px;">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="detail-label">Loan Amount</div>
                                <div class="detail-value" style="font-size:0.95rem;">TZS <?php echo e(number_format($motorcycle->loan->total_amount)); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Paid</div>
                                <div class="detail-value" style="font-size:0.95rem;color:var(--emerald-600);">TZS <?php echo e(number_format($motorcycle->loan->amount_paid)); ?></div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:0.78rem;color:var(--text-secondary);">Progress</span>
                            <span style="font-size:0.78rem;font-weight:700;color:var(--text);"><?php echo e($motorcycle->loan->progress); ?>%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width:<?php echo e($motorcycle->loan->progress); ?>%;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span style="font-size:0.72rem;color:var(--text-muted);">TZS <?php echo e(number_format($motorcycle->loan->amount_paid)); ?></span>
                            <span style="font-size:0.72rem;color:var(--text-muted);">TZS <?php echo e(number_format($motorcycle->loan->total_amount)); ?></span>
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <span class="badge-status <?php echo e($motorcycle->loan->status); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $motorcycle->loan->status))); ?></span>
                    </div>

                    <a href="<?php echo e(route('loans.show', $motorcycle->loan)); ?>" class="btn btn-navy w-100" style="padding:10px;">
                        <i class="bi bi-calendar-week me-1"></i> View Loan Details
                    </a>
                    <?php if($motorcycle->driver): ?>
                    <a href="<?php echo e(route('owner.vehicles.track', $motorcycle)); ?>" class="btn btn-gold w-100 mt-2" style="padding:10px;">
                        <i class="bi bi-geo-alt-fill me-1"></i> Track GPS
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm mb-4">
                <div style="padding:24px;text-align:center;">
                    <h6 style="font-size:0.82rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border);text-align:left;">
                        <i class="bi bi-wallet2 me-1" style="color:var(--navy-700);"></i> Loan Status
                    </h6>
                    <div style="padding:20px 0;">
                        <div style="width:56px;height:56px;border-radius:14px;background:var(--page-bg);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="bi bi-wallet2" style="font-size:1.4rem;color:var(--text-secondary);"></i>
                        </div>
                        <div style="font-size:0.9rem;color:var(--text-secondary);font-weight:500;">No active loan</div>
                        <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">Apply or assign a driver to start</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php if($applications->count() && !$motorcycle->loan): ?>
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div style="padding:20px 24px;border-bottom:1px solid var(--border);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--navy-900);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-people-fill" style="font-size:1.1rem;color:#fff;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="color:var(--text);font-size:1rem;">Driver Applications</h6>
                            <div style="font-size:0.78rem;color:var(--text-secondary);"><?php echo e($applications->where('status','pending')->count()); ?> pending · <?php echo e($applications->where('status','approved')->count()); ?> accepted · <?php echo e($applications->where('status','rejected')->count()); ?> rejected</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding:20px 24px;">
                <?php $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $driver = $app->driver;
                        $isPending = $app->status === 'pending';
                        $isAccepted = $app->status === 'approved';
                        $isRejected = $app->status === 'rejected';
                    ?>

                    <div style="border:1px solid <?php echo e($isPending ? '#F59E0B' : ($isAccepted ? '#10B981' : '#D1D5DB')); ?>;border-radius:12px;margin-bottom:<?php echo e($loop->last ? '0' : '16px'); ?>;overflow:hidden;">
                        <div style="background:<?php echo e($isPending ? '#FFFBEB' : ($isAccepted ? '#ECFDF5' : '#F9FAFB')); ?>;padding:16px 20px;border-bottom:1px solid <?php echo e($isPending ? '#FDE68A' : ($isAccepted ? '#A7F3D0' : '#D1D5DB')); ?>;">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:48px;height:48px;border-radius:50%;background:<?php echo e($isPending ? '#F59E0B' : ($isAccepted ? '#10B981' : '#6B7280')); ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0;"><?php echo e(substr($driver->name ?? 'D', 0, 1)); ?></div>
                                    <div>
                                        <div style="font-weight:700;font-size:1rem;color:var(--text);"><?php echo e($driver->name ?? 'Unknown'); ?></div>
                                        <div style="font-size:0.82rem;color:var(--text-secondary);">Applied <?php echo e($app->created_at->format('d M Y \a\t g:i A')); ?></div>
                                    </div>
                                </div>
                                <div>
                                    <?php if($isPending): ?>
                                        <span style="background:#FEF3C7;color:#92400E;padding:6px 14px;border-radius:20px;font-size:0.78rem;font-weight:700;">Pending Review</span>
                                    <?php elseif($isAccepted): ?>
                                        <span style="background:#D1FAE5;color:#065F46;padding:6px 14px;border-radius:20px;font-size:0.78rem;font-weight:700;">Accepted</span>
                                    <?php else: ?>
                                        <span style="background:#FEE2E2;color:#991B1B;padding:6px 14px;border-radius:20px;font-size:0.78rem;font-weight:700;">Rejected</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div style="padding:20px;">
                            <div style="font-size:0.78rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;">Applicant Details</div>
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Full Name</div>
                                    <div class="detail-value"><?php echo e($driver->name ?? '—'); ?></div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value" style="word-break:break-all;"><?php echo e($driver->email ?? '—'); ?></div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Phone</div>
                                    <div class="detail-value"><?php echo e($driver->phone ?? '—'); ?></div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">NIDA Number</div>
                                    <div class="detail-value"><?php echo e($driver->nida ?? '—'); ?></div>
                                </div>
                                <?php if($driver->address): ?>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Address</div>
                                    <div class="detail-value"><?php echo e($driver->address); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if($driver->birthdate): ?>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Date of Birth</div>
                                    <div class="detail-value"><?php echo e($driver->birthdate->format('d M Y')); ?></div>
                                </div>
                                <?php endif; ?>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Account Status</div>
                                    <div class="detail-value">
                                        <?php if($driver->approval_status === 'approved'): ?>
                                            <span style="color:#059669;">Approved</span>
                                        <?php elseif($driver->approval_status === 'pending'): ?>
                                            <span style="color:#D97706;">Pending</span>
                                        <?php else: ?>
                                            <span style="color:#DC2626;"><?php echo e(ucfirst($driver->approval_status)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($app->license_number): ?>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">License Number</div>
                                    <div class="detail-value"><?php echo e($app->license_number); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if($app->guarantor_name): ?>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Guarantor</div>
                                    <div class="detail-value"><?php echo e($app->guarantor_name); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if($app->guarantor_phone): ?>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="detail-label">Guarantor Phone</div>
                                    <div class="detail-value"><?php echo e($app->guarantor_phone); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if($app->notes): ?>
                                <div class="col-12">
                                    <div class="detail-label">Driver's Note</div>
                                    <div style="font-size:0.88rem;color:var(--text);background:var(--page-bg);padding:12px 16px;border-radius:8px;margin-top:4px;"><?php echo e($app->notes); ?></div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if($isPending): ?>
                            <div style="border-top:1px solid var(--border);margin-top:20px;padding-top:16px;" class="d-flex flex-wrap gap-3">
                                <form method="POST" action="<?php echo e(route('owner.vehicles.accept', [$motorcycle, $app])); ?>" onsubmit="return confirm('Accept <?php echo e($driver->name); ?>? A contract and loan will be created.');">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn" style="background:#059669;color:#fff;padding:10px 28px;font-weight:700;font-size:0.88rem;border-radius:8px;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Accept Driver
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo e(route('owner.vehicles.reject', [$motorcycle, $app])); ?>" onsubmit="return confirm('Reject <?php echo e($driver->name); ?>?');">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn" style="background:#FEE2E2;color:#991B1B;padding:10px 28px;font-weight:700;font-size:0.88rem;border-radius:8px;">
                                        <i class="bi bi-x-circle-fill me-1"></i> Reject
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.detail-label { font-size:0.72rem; color:var(--text-secondary); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; }
.detail-value { font-size:0.92rem; color:var(--text); font-weight:600; line-height:1.4; }
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/owner/vehicle-show.blade.php ENDPATH**/ ?>