<?php if($chatType === 'loan'): ?>
    <?php $__env->startSection('title', 'Chat — ' . ($loan->motorcycle->plate_number ?? 'Loan')); ?>
<?php else: ?>
    <?php $__env->startSection('title', 'Chat — ' . $otherUser->name); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-3">
        <a href="<?php echo e(route('chat.index')); ?>" class="btn btn-sm btn-outline" style="border-radius:8px;"><i class="bi bi-arrow-left"></i></a>
        <?php if($chatType === 'loan'): ?>
            <div style="width:44px;height:44px;border-radius:14px;background:var(--navy-900);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                <i class="bi bi-bicycle"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold"><?php echo e($loan->motorcycle->make); ?> <?php echo e($loan->motorcycle->model); ?> <span style="font-weight:500;color:var(--text-secondary);font-size:0.82rem;">(<?php echo e($loan->motorcycle->plate_number); ?>)</span></h6>
                <small class="text-muted">
                    <?php $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span style="color:<?php echo e($p->id === Auth::id() ? 'var(--navy-900)' : 'var(--text-secondary)'); ?>;font-weight:<?php echo e($p->id === Auth::id() ? '600' : '400'); ?>;">
                            <?php echo e($p->name); ?><span style="font-size:0.7rem;opacity:0.6;"> (<?php echo e(ucfirst($p->role)); ?>)</span>
                        </span><?php echo e($i < $participants->count() - 1 ? ' · ' : ''); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </small>
            </div>
        <?php else: ?>
            <div style="width:44px;height:44px;border-radius:14px;background:<?php echo e($otherUser->isAdmin() ? 'var(--emerald-600,#059669)' : ($otherUser->isOwner() ? 'var(--navy-700)' : '#C9962C')); ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0;">
                <?php echo e(strtoupper(substr($otherUser->name, 0, 1))); ?>

            </div>
            <div>
                <h6 class="mb-0 fw-bold"><?php echo e($otherUser->name); ?></h6>
                <small class="text-muted"><?php echo e(ucfirst($otherUser->role)); ?> <?php echo e($otherUser->phone ? '· ' . $otherUser->phone : ''); ?></small>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm" id="chatCard" style="height:calc(100vh - 260px);min-height:400px;display:flex;flex-direction:column;">
    <div class="flex-grow-1 overflow-auto p-3" id="chatMessages" style="display:flex;flex-direction:column;gap:8px;">
        <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="d-flex <?php echo e($msg->sender_id === Auth::id() ? 'justify-content-end' : 'justify-content-start'); ?>">
                <div style="max-width:70%;">
                    <?php if($msg->sender_id !== Auth::id()): ?>
                        <div style="font-size:0.68rem;font-weight:600;color:<?php echo e($msg->sender->isAdmin() ? 'var(--emerald-600,#059669)' : 'var(--navy-700)'); ?>;margin-bottom:2px;padding:0 4px;">
                            <?php echo e($msg->sender->name); ?> <span style="opacity:0.6;font-weight:400;"><?php echo e(ucfirst($msg->sender->role)); ?></span>
                        </div>
                    <?php endif; ?>
                    <div style="padding:10px 14px;border-radius:14px;font-size:0.88rem;word-wrap:break-word;
                        <?php if($msg->sender_id === Auth::id()): ?>
                            background:var(--navy-900);color:#fff;border-bottom-right-radius:4px;
                        <?php elseif($msg->sender->isAdmin()): ?>
                            background:#E3F9EF;color:#065f46;border-bottom-left-radius:4px;
                        <?php else: ?>
                            background:#f1f5f9;color:var(--text);border-bottom-left-radius:4px;
                        <?php endif; ?>
                    ">
                        <?php echo e($msg->body); ?>

                    </div>
                    <div style="font-size:0.68rem;color:var(--text-muted);margin-top:2px;padding:0 4px;
                        <?php echo e($msg->sender_id === Auth::id() ? 'text-align:right;' : ''); ?>

                    "><?php echo e($msg->created_at->format('g:i A')); ?></div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-5 my-auto">
                <i class="bi bi-chat-square-dots" style="font-size:2rem;color:var(--text-muted);"></i>
                <p class="mt-2 text-muted">Start the conversation</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="border-top p-3">
        <form id="chatForm" class="d-flex gap-2">
            <?php echo csrf_field(); ?>
            <input type="text" id="chatInput" class="form-control" placeholder="Type a message..." maxlength="2000" autocomplete="off" style="border-radius:20px;padding:10px 18px;font-size:0.88rem;">
            <button type="submit" class="btn btn-gold" style="border-radius:50%;width:42px;height:42px;padding:0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </div>
</div>

<script>
(function(){
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const conversationId = '<?php echo e($conversationId); ?>';
    let lastId = <?php echo e($messages->last()?->id ?? 0); ?>;

    chatMessages.scrollTop = chatMessages.scrollHeight;

    function addMessage(msg) {
        const isMine = msg.is_mine;
        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex ' + (isMine ? 'justify-content-end' : 'justify-content-start');
        const nameHtml = !isMine ? '<div style="font-size:0.68rem;font-weight:600;color:var(--navy-700);margin-bottom:2px;padding:0 4px;">' + msg.sender_name + '</div>' : '';
        const bubbleStyle = isMine
            ? 'background:var(--navy-900);color:#fff;border-bottom-right-radius:4px;'
            : 'background:#f1f5f9;color:var(--text);border-bottom-left-radius:4px;';
        wrapper.innerHTML =
            '<div style="max-width:70%;">' +
                nameHtml +
                '<div style="padding:10px 14px;border-radius:14px;font-size:0.88rem;word-wrap:break-word;' + bubbleStyle + '">' + msg.body + '</div>' +
                '<div style="font-size:0.68rem;color:var(--text-muted);margin-top:2px;padding:0 4px;' +
                    (isMine ? 'text-align:right;' : '') +
                '">' + msg.created_at + '</div>' +
            '</div>';
        chatMessages.appendChild(wrapper);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const body = chatInput.value.trim();
        if (!body) return;

        fetch('<?php echo e(route("chat.send.conversation", $conversationId)); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ body: body }),
        })
        .then(r => r.json())
        .then(msg => {
            addMessage(msg);
            lastId = msg.id;
            chatInput.value = '';
        });

        chatInput.focus();
    });

    setInterval(function() {
        fetch('<?php echo e(route("chat.fetch.conversation", $conversationId)); ?>?last_id=' + lastId, {
            headers: { 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(messages => {
            messages.forEach(msg => {
                if (msg.id > lastId) {
                    addMessage(msg);
                    lastId = msg.id;
                }
            });
        });
    }, 5000);
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\user\Desktop\BODALOAN2\BODALOAN\resources\views/chat/show.blade.php ENDPATH**/ ?>