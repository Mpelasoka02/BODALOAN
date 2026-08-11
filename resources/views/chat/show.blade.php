@extends('layouts.app')
@if($chatType === 'loan')
    @section('title', 'Chat — ' . ($loan->motorcycle->plate_number ?? 'Loan'))
@else
    @section('title', 'Chat — ' . $otherUser->name)
@endif

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('chat.index') }}" class="btn btn-sm btn-outline" style="border-radius:8px;"><i class="bi bi-arrow-left"></i></a>
        @if($chatType === 'loan')
            <div style="width:44px;height:44px;border-radius:14px;background:var(--navy-900);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                <i class="bi bi-bicycle"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">{{ $loan->motorcycle->make }} {{ $loan->motorcycle->model }} <span style="font-weight:500;color:var(--text-secondary);font-size:0.82rem;">({{ $loan->motorcycle->plate_number }})</span></h6>
                <small class="text-muted">
                    @foreach($participants as $i => $p)
                        <span style="color:{{ $p->id === Auth::id() ? 'var(--navy-900)' : 'var(--text-secondary)' }};font-weight:{{ $p->id === Auth::id() ? '600' : '400' }};">
                            {{ $p->name }}<span style="font-size:0.7rem;opacity:0.6;"> ({{ ucfirst($p->role) }})</span>
                        </span>{{ $i < $participants->count() - 1 ? ' · ' : '' }}
                    @endforeach
                </small>
            </div>
        @else
            <div style="width:44px;height:44px;border-radius:14px;background:{{ $otherUser->isAdmin() ? 'var(--emerald-600,#059669)' : ($otherUser->isOwner() ? 'var(--navy-700)' : '#C9962C') }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0;">
                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
            </div>
            <div>
                <h6 class="mb-0 fw-bold">{{ $otherUser->name }}</h6>
                <small class="text-muted">{{ ucfirst($otherUser->role) }} {{ $otherUser->phone ? '· ' . $otherUser->phone : '' }}</small>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm" id="chatCard" style="height:calc(100vh - 260px);min-height:400px;display:flex;flex-direction:column;">
    <div class="flex-grow-1 overflow-auto p-3" id="chatMessages" style="display:flex;flex-direction:column;gap:8px;">
        @forelse($messages as $msg)
            <div class="d-flex {{ $msg->sender_id === Auth::id() ? 'justify-content-end' : 'justify-content-start' }}">
                <div style="max-width:70%;">
                    @if($msg->sender_id !== Auth::id())
                        <div style="font-size:0.68rem;font-weight:600;color:{{ $msg->sender->isAdmin() ? 'var(--emerald-600,#059669)' : 'var(--navy-700)' }};margin-bottom:2px;padding:0 4px;">
                            {{ $msg->sender->name }} <span style="opacity:0.6;font-weight:400;">{{ ucfirst($msg->sender->role) }}</span>
                        </div>
                    @endif
                    <div style="padding:10px 14px;border-radius:14px;font-size:0.88rem;word-wrap:break-word;
                        @if($msg->sender_id === Auth::id())
                            background:var(--navy-900);color:#fff;border-bottom-right-radius:4px;
                        @elseif($msg->sender->isAdmin())
                            background:#E3F9EF;color:#065f46;border-bottom-left-radius:4px;
                        @else
                            background:#f1f5f9;color:var(--text);border-bottom-left-radius:4px;
                        @endif
                    ">
                        {{ $msg->body }}
                    </div>
                    <div style="font-size:0.68rem;color:var(--text-muted);margin-top:2px;padding:0 4px;
                        {{ $msg->sender_id === Auth::id() ? 'text-align:right;' : '' }}
                    ">{{ $msg->created_at->format('g:i A') }}</div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 my-auto">
                <i class="bi bi-chat-square-dots" style="font-size:2rem;color:var(--text-muted);"></i>
                <p class="mt-2 text-muted">Start the conversation</p>
            </div>
        @endforelse
    </div>

    <div class="border-top p-3">
        <form id="chatForm" class="d-flex gap-2">
            @csrf
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
    const conversationId = '{{ $conversationId }}';
    let lastId = {{ $messages->last()?->id ?? 0 }};

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

        fetch('{{ route("chat.send.conversation", $conversationId) }}', {
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
        fetch('{{ route("chat.fetch.conversation", $conversationId) }}?last_id=' + lastId, {
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
@endsection
