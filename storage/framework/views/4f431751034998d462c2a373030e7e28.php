<?php $__env->startSection('title', 'Messages — BodaLink'); ?>
<?php $__env->startSection('page-title', 'Messages'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <p class="text-muted mb-0 small">
        <?php if(auth()->user()->isAdmin()): ?>
            Chat with vehicle owners and drivers
        <?php elseif(auth()->user()->isOwner()): ?>
            Chat with admin and your drivers
        <?php else: ?>
            Chat with admin and vehicle owners
        <?php endif; ?>
    </p>
</div>

<?php
    $loanConvs = $conversations->where('type', 'loan');
    $directConvs = $conversations->where('type', 'direct');
    $directUserIds = $directConvs->pluck('conversation_id')->map(fn($id) => (int) str_replace('direct-', '', $id))->toArray();
?>

<?php if(auth()->user()->isAdmin()): ?>
    
    <?php if(isset($availableContacts['owners']) && $availableContacts['owners']->count()): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-person-workspace me-1" style="color:var(--navy-700);"></i> Vehicle Owners</h6>
        </div>
        <div class="list-group list-group-flush">
            <?php $__currentLoopData = $availableContacts['owners']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id);
                    $convId = 'direct-' . $contact->id;
                ?>
                <a href="<?php echo e(route('chat.start.direct', $contact->id)); ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid <?php echo e($existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent'); ?>;">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--navy-700);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                        <?php echo e(strtoupper(substr($contact->name, 0, 1))); ?>

                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);"><?php echo e($contact->name); ?></h6>
                            <?php if($existingConv && $existingConv['last_message']): ?>
                                <small class="text-muted" style="font-size:0.75rem;"><?php echo e($existingConv['last_message']->created_at->diffForHumans()); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                <?php if($existingConv && $existingConv['last_message']): ?>
                                    <?php echo e(Str::limit($existingConv['last_message']->body, 50)); ?>

                                <?php else: ?>
                                    Start a conversation
                                <?php endif; ?>
                            </small>
                            <?php if($existingConv && $existingConv['unread_count'] > 0): ?>
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;"><?php echo e($existingConv['unread_count']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(isset($availableContacts['drivers']) && $availableContacts['drivers']->count()): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-person-badge me-1" style="color:#C9962C;"></i> Drivers</h6>
        </div>
        <div class="list-group list-group-flush">
            <?php $__currentLoopData = $availableContacts['drivers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id);
                ?>
                <a href="<?php echo e(route('chat.start.direct', $contact->id)); ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid <?php echo e($existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent'); ?>;">
                    <div style="width:44px;height:44px;border-radius:50%;background:#C9962C;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                        <?php echo e(strtoupper(substr($contact->name, 0, 1))); ?>

                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);"><?php echo e($contact->name); ?></h6>
                            <?php if($existingConv && $existingConv['last_message']): ?>
                                <small class="text-muted" style="font-size:0.75rem;"><?php echo e($existingConv['last_message']->created_at->diffForHumans()); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                <?php if($existingConv && $existingConv['last_message']): ?>
                                    <?php echo e(Str::limit($existingConv['last_message']->body, 50)); ?>

                                <?php else: ?>
                                    Start a conversation
                                <?php endif; ?>
                            </small>
                            <?php if($existingConv && $existingConv['unread_count'] > 0): ?>
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;"><?php echo e($existingConv['unread_count']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

<?php elseif(auth()->user()->isOwner()): ?>
    
    <?php if(isset($availableContacts['admin']) && $availableContacts['admin']->count()): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-headset me-1" style="color:var(--emerald-600,#059669);"></i> Platform Support</h6>
        </div>
        <div class="list-group list-group-flush">
            <?php $__currentLoopData = $availableContacts['admin']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id); ?>
                <a href="<?php echo e(route('chat.start.direct', $contact->id)); ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid <?php echo e($existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent'); ?>;">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--emerald-600,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);"><?php echo e($contact->name); ?></h6>
                            <?php if($existingConv && $existingConv['last_message']): ?>
                                <small class="text-muted" style="font-size:0.75rem;"><?php echo e($existingConv['last_message']->created_at->diffForHumans()); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                <?php if($existingConv && $existingConv['last_message']): ?>
                                    <?php echo e(Str::limit($existingConv['last_message']->body, 50)); ?>

                                <?php else: ?>
                                    Get help from platform support
                                <?php endif; ?>
                            </small>
                            <?php if($existingConv && $existingConv['unread_count'] > 0): ?>
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;"><?php echo e($existingConv['unread_count']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(isset($availableContacts['drivers']) && $availableContacts['drivers']->count()): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-person-badge me-1" style="color:#C9962C;"></i> My Drivers</h6>
        </div>
        <div class="list-group list-group-flush">
            <?php $__currentLoopData = $availableContacts['drivers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id); ?>
                <a href="<?php echo e(route('chat.start.direct', $contact->id)); ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid <?php echo e($existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent'); ?>;">
                    <div style="width:44px;height:44px;border-radius:50%;background:#C9962C;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                        <?php echo e(strtoupper(substr($contact->name, 0, 1))); ?>

                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);"><?php echo e($contact->name); ?></h6>
                            <?php if($existingConv && $existingConv['last_message']): ?>
                                <small class="text-muted" style="font-size:0.75rem;"><?php echo e($existingConv['last_message']->created_at->diffForHumans()); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                <?php if($existingConv && $existingConv['last_message']): ?>
                                    <?php echo e(Str::limit($existingConv['last_message']->body, 50)); ?>

                                <?php else: ?>
                                    Start a conversation
                                <?php endif; ?>
                            </small>
                            <?php if($existingConv && $existingConv['unread_count'] > 0): ?>
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;"><?php echo e($existingConv['unread_count']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

<?php else: ?>
    
    <?php if(isset($availableContacts['admin']) && $availableContacts['admin']->count()): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-headset me-1" style="color:var(--emerald-600,#059669);"></i> Platform Support</h6>
        </div>
        <div class="list-group list-group-flush">
            <?php $__currentLoopData = $availableContacts['admin']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id); ?>
                <a href="<?php echo e(route('chat.start.direct', $contact->id)); ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid <?php echo e($existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent'); ?>;">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--emerald-600,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);"><?php echo e($contact->name); ?></h6>
                            <?php if($existingConv && $existingConv['last_message']): ?>
                                <small class="text-muted" style="font-size:0.75rem;"><?php echo e($existingConv['last_message']->created_at->diffForHumans()); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                <?php if($existingConv && $existingConv['last_message']): ?>
                                    <?php echo e(Str::limit($existingConv['last_message']->body, 50)); ?>

                                <?php else: ?>
                                    Get help from platform support
                                <?php endif; ?>
                            </small>
                            <?php if($existingConv && $existingConv['unread_count'] > 0): ?>
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;"><?php echo e($existingConv['unread_count']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(isset($availableContacts['owners']) && $availableContacts['owners']->count()): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-person-workspace me-1" style="color:var(--navy-700);"></i> Vehicle Owners</h6>
        </div>
        <div class="list-group list-group-flush">
            <?php $__currentLoopData = $availableContacts['owners']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id); ?>
                <a href="<?php echo e(route('chat.start.direct', $contact->id)); ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid <?php echo e($existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent'); ?>;">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--navy-700);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                        <?php echo e(strtoupper(substr($contact->name, 0, 1))); ?>

                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);"><?php echo e($contact->name); ?></h6>
                            <?php if($existingConv && $existingConv['last_message']): ?>
                                <small class="text-muted" style="font-size:0.75rem;"><?php echo e($existingConv['last_message']->created_at->diffForHumans()); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                <?php if($existingConv && $existingConv['last_message']): ?>
                                    <?php echo e(Str::limit($existingConv['last_message']->body, 50)); ?>

                                <?php else: ?>
                                    Start a conversation
                                <?php endif; ?>
                            </small>
                            <?php if($existingConv && $existingConv['unread_count'] > 0): ?>
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;"><?php echo e($existingConv['unread_count']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php if(!isset($availableContacts['owners']) || !$availableContacts['owners']->count()): ?>
    <?php if(!isset($availableContacts['drivers']) || !$availableContacts['drivers']->count()): ?>
        <?php if(!isset($availableContacts['admin']) || !$availableContacts['admin']->count()): ?>
<div class="card border-0 shadow-sm text-center py-5">
    <i class="bi bi-chat-square-dots display-4 text-muted"></i>
    <h5 class="mt-3">No Contacts Available</h5>
    <p class="text-muted mb-0">No one to chat with yet.</p>
</div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/chat/index.blade.php ENDPATH**/ ?>