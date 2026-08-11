@extends('layouts.app')
@section('title', 'Messages — BodaLink')
@section('page-title', 'Messages')

@section('content')
<div class="mb-4">
    <p class="text-muted mb-0 small">
        @if(auth()->user()->isAdmin())
            Chat with vehicle owners and drivers
        @elseif(auth()->user()->isOwner())
            Chat with admin and your drivers
        @else
            Chat with admin and vehicle owners
        @endif
    </p>
</div>

@php
    $loanConvs = $conversations->where('type', 'loan');
    $directConvs = $conversations->where('type', 'direct');
    $directUserIds = $directConvs->pluck('conversation_id')->map(fn($id) => (int) str_replace('direct-', '', $id))->toArray();
@endphp

@if(auth()->user()->isAdmin())
    {{-- ADMIN: show owners and drivers --}}
    @if(isset($availableContacts['owners']) && $availableContacts['owners']->count())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-person-workspace me-1" style="color:var(--navy-700);"></i> Vehicle Owners</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($availableContacts['owners'] as $contact)
                @php
                    $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id);
                    $convId = 'direct-' . $contact->id;
                @endphp
                <a href="{{ route('chat.start.direct', $contact->id) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid {{ $existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent' }};">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--navy-700);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);">{{ $contact->name }}</h6>
                            @if($existingConv && $existingConv['last_message'])
                                <small class="text-muted" style="font-size:0.75rem;">{{ $existingConv['last_message']->created_at->diffForHumans() }}</small>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                @if($existingConv && $existingConv['last_message'])
                                    {{ Str::limit($existingConv['last_message']->body, 50) }}
                                @else
                                    Start a conversation
                                @endif
                            </small>
                            @if($existingConv && $existingConv['unread_count'] > 0)
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;">{{ $existingConv['unread_count'] }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($availableContacts['drivers']) && $availableContacts['drivers']->count())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-person-badge me-1" style="color:#C9962C;"></i> Drivers</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($availableContacts['drivers'] as $contact)
                @php
                    $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id);
                @endphp
                <a href="{{ route('chat.start.direct', $contact->id) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid {{ $existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent' }};">
                    <div style="width:44px;height:44px;border-radius:50%;background:#C9962C;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);">{{ $contact->name }}</h6>
                            @if($existingConv && $existingConv['last_message'])
                                <small class="text-muted" style="font-size:0.75rem;">{{ $existingConv['last_message']->created_at->diffForHumans() }}</small>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                @if($existingConv && $existingConv['last_message'])
                                    {{ Str::limit($existingConv['last_message']->body, 50) }}
                                @else
                                    Start a conversation
                                @endif
                            </small>
                            @if($existingConv && $existingConv['unread_count'] > 0)
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;">{{ $existingConv['unread_count'] }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

@elseif(auth()->user()->isOwner())
    {{-- OWNER: show admin and drivers --}}
    @if(isset($availableContacts['admin']) && $availableContacts['admin']->count())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-headset me-1" style="color:var(--emerald-600,#059669);"></i> Platform Support</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($availableContacts['admin'] as $contact)
                @php $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id); @endphp
                <a href="{{ route('chat.start.direct', $contact->id) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid {{ $existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent' }};">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--emerald-600,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);">{{ $contact->name }}</h6>
                            @if($existingConv && $existingConv['last_message'])
                                <small class="text-muted" style="font-size:0.75rem;">{{ $existingConv['last_message']->created_at->diffForHumans() }}</small>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                @if($existingConv && $existingConv['last_message'])
                                    {{ Str::limit($existingConv['last_message']->body, 50) }}
                                @else
                                    Get help from platform support
                                @endif
                            </small>
                            @if($existingConv && $existingConv['unread_count'] > 0)
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;">{{ $existingConv['unread_count'] }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($availableContacts['drivers']) && $availableContacts['drivers']->count())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-person-badge me-1" style="color:#C9962C;"></i> My Drivers</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($availableContacts['drivers'] as $contact)
                @php $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id); @endphp
                <a href="{{ route('chat.start.direct', $contact->id) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid {{ $existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent' }};">
                    <div style="width:44px;height:44px;border-radius:50%;background:#C9962C;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);">{{ $contact->name }}</h6>
                            @if($existingConv && $existingConv['last_message'])
                                <small class="text-muted" style="font-size:0.75rem;">{{ $existingConv['last_message']->created_at->diffForHumans() }}</small>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                @if($existingConv && $existingConv['last_message'])
                                    {{ Str::limit($existingConv['last_message']->body, 50) }}
                                @else
                                    Start a conversation
                                @endif
                            </small>
                            @if($existingConv && $existingConv['unread_count'] > 0)
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;">{{ $existingConv['unread_count'] }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

@else()
    {{-- DRIVER: show admin and owners --}}
    @if(isset($availableContacts['admin']) && $availableContacts['admin']->count())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-headset me-1" style="color:var(--emerald-600,#059669);"></i> Platform Support</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($availableContacts['admin'] as $contact)
                @php $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id); @endphp
                <a href="{{ route('chat.start.direct', $contact->id) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid {{ $existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent' }};">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--emerald-600,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);">{{ $contact->name }}</h6>
                            @if($existingConv && $existingConv['last_message'])
                                <small class="text-muted" style="font-size:0.75rem;">{{ $existingConv['last_message']->created_at->diffForHumans() }}</small>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                @if($existingConv && $existingConv['last_message'])
                                    {{ Str::limit($existingConv['last_message']->body, 50) }}
                                @else
                                    Get help from platform support
                                @endif
                            </small>
                            @if($existingConv && $existingConv['unread_count'] > 0)
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;">{{ $existingConv['unread_count'] }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($availableContacts['owners']) && $availableContacts['owners']->count())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background:var(--page-bg);border-bottom:1px solid var(--border);padding:14px 20px;">
            <h6 class="mb-0 fw-bold" style="font-size:0.88rem;"><i class="bi bi-person-workspace me-1" style="color:var(--navy-700);"></i> Vehicle Owners</h6>
        </div>
        <div class="list-group list-group-flush">
            @foreach($availableContacts['owners'] as $contact)
                @php $existingConv = $directConvs->first(fn($c) => isset($c['other_user']) && $c['other_user']->id === $contact->id); @endphp
                <a href="{{ route('chat.start.direct', $contact->id) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3" style="border-left:3px solid {{ $existingConv && $existingConv['unread_count'] > 0 ? 'var(--gold-500)' : 'transparent' }};">
                    <div style="width:44px;height:44px;border-radius:50%;background:var(--navy-700);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;flex-shrink:0;">
                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;color:var(--text);">{{ $contact->name }}</h6>
                            @if($existingConv && $existingConv['last_message'])
                                <small class="text-muted" style="font-size:0.75rem;">{{ $existingConv['last_message']->created_at->diffForHumans() }}</small>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small style="color:var(--text-secondary);font-size:0.8rem;">
                                @if($existingConv && $existingConv['last_message'])
                                    {{ Str::limit($existingConv['last_message']->body, 50) }}
                                @else
                                    Start a conversation
                                @endif
                            </small>
                            @if($existingConv && $existingConv['unread_count'] > 0)
                                <span class="badge rounded-pill" style="background:var(--gold-500);color:#fff;font-size:0.65rem;">{{ $existingConv['unread_count'] }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
@endif

@if(!isset($availableContacts['owners']) || !$availableContacts['owners']->count())
    @if(!isset($availableContacts['drivers']) || !$availableContacts['drivers']->count())
        @if(!isset($availableContacts['admin']) || !$availableContacts['admin']->count())
<div class="card border-0 shadow-sm text-center py-5">
    <i class="bi bi-chat-square-dots display-4 text-muted"></i>
    <h5 class="mt-3">No Contacts Available</h5>
    <p class="text-muted mb-0">No one to chat with yet.</p>
</div>
        @endif
    @endif
@endif
@endsection
