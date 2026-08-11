@extends('layouts.app')
@section('title', 'My Bodabodas')
@section('page-title', 'My Bodabodas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="{{ route('owner.vehicles.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Bodaboda</a>
</div>

@if($motorcycles->count() > 0)
    @php
        $totalPending = 0;
        foreach($motorcycles as $m) { $totalPending += $m->applications->where('status','pending')->count(); }
    @endphp

    @if($totalPending > 0)
        <a href="#" onclick="document.getElementById('pendingSection').scrollIntoView({behavior:'smooth'});return false;" style="display:flex;align-items:center;gap:12px;background:#FFFBEB;border:2px solid #F59E0B;border-radius:12px;padding:14px 20px;margin-bottom:20px;text-decoration:none;transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(245,158,11,0.2)'" onmouseout="this.style.boxShadow='none'">
            <div style="width:44px;height:44px;border-radius:50%;background:#F59E0B;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:800;flex-shrink:0;">{{ $totalPending }}</div>
            <div>
                <div style="font-weight:700;color:#92400E;font-size:0.95rem;">Driver {{ Str::plural('Request', $totalPending) }} Waiting</div>
                <div style="font-size:0.82rem;color:#A16207;">Tap to review and accept drivers for your bodabodas</div>
            </div>
            <i class="bi bi-chevron-right" style="margin-left:auto;color:#D97706;font-size:1.1rem;"></i>
        </a>
    @endif

    @foreach($motorcycles as $m)
        @php $pendingCount = $m->applications->where('status', 'pending')->count(); @endphp

        <div class="card border-0 shadow-sm" style="margin-bottom:14px;{{ $pendingCount > 0 && !$m->loan ? 'border:2px solid #F59E0B !important;' : '' }}">
            {{-- PENDING BANNER --}}
            @if($pendingCount > 0 && !$m->loan)
                <div id="pendingSection" style="background:#FFFBEB;border-bottom:1px solid #FDE68A;padding:10px 20px;display:flex;align-items:center;gap:10px;">
                    <i class="bi bi-bell-fill" style="color:#F59E0B;"></i>
                    <span style="font-weight:700;color:#92400E;font-size:0.88rem;">{{ $pendingCount }} driver {{ Str::plural('request', $pendingCount) }} pending</span>
                    <a href="{{ route('owner.vehicles.show', $m) }}" style="margin-left:auto;background:#F59E0B;color:#fff;padding:5px 14px;border-radius:6px;font-size:0.78rem;font-weight:700;text-decoration:none;">Review Now</a>
                </div>
            @endif

            <div style="padding:16px 20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                {{-- PHOTO --}}
                <div style="width:60px;height:60px;border-radius:10px;overflow:hidden;background:var(--page-bg);flex-shrink:0;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);">
                    @if($m->photo)
                        <img src="{{ asset('storage/' . $m->photo) }}" alt="{{ $m->plate_number }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <i class="bi bi-bicycle" style="font-size:1.5rem;color:var(--text-secondary);"></i>
                    @endif
                </div>

                {{-- INFO --}}
                <div style="flex:1;min-width:200px;">
                    <div style="font-weight:700;color:var(--text);font-size:1rem;">{{ $m->make }} {{ $m->model }}</div>
                    <div style="font-size:0.82rem;color:var(--text-secondary);margin-top:2px;">
                        {{ $m->plate_number }} · {{ $m->year }} · {{ $m->color ?: '—' }}
                    </div>
                    <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;">
                        <span class="badge-status {{ $m->verification_status }}" style="font-size:0.72rem;padding:3px 8px;">{{ ucfirst(str_replace('_',' ',$m->verification_status)) }}</span>
                        <span class="badge-status {{ $m->status }}" style="font-size:0.72rem;padding:3px 8px;">{{ ucfirst($m->status) }}</span>
                    </div>
                </div>

                {{-- DRIVER --}}
                <div style="min-width:140px;">
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;letter-spacing:0.5px;">Driver</div>
                    @if($m->driver)
                        <div style="font-size:0.85rem;font-weight:600;color:var(--text);margin-top:2px;">{{ $m->driver->name }}</div>
                    @else
                        <div style="font-size:0.82rem;color:var(--text-muted);margin-top:2px;">Unassigned</div>
                    @endif
                </div>

                {{-- ACTIONS --}}
                <div class="d-flex gap-1" style="flex-shrink:0;">
                    @if($pendingCount > 0 && !$m->loan)
                        <a href="{{ route('owner.vehicles.show', $m) }}" class="btn btn-sm" style="background:#F59E0B;color:#fff;padding:6px 14px;font-weight:700;font-size:0.82rem;border-radius:8px;">
                            <i class="bi bi-person-plus-fill me-1"></i> {{ $pendingCount }} Request{{ $pendingCount > 1 ? 's' : '' }}
                        </a>
                    @endif
                    @if($m->driver)
                        <a href="{{ route('owner.vehicles.track', $m) }}" class="btn btn-sm" style="background:var(--gold-500);color:#fff;padding:6px 12px;font-weight:700;font-size:0.82rem;border-radius:8px;" title="Track GPS"><i class="bi bi-geo-alt-fill"></i></a>
                    @endif
                    <a href="{{ route('owner.vehicles.show', $m) }}" class="btn btn-sm btn-outline-navy" title="View"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('owner.vehicles.edit', $m) }}" class="btn btn-sm btn-outline" title="Edit"><i class="bi bi-pencil"></i></a>
                    @if($m->status !== 'assigned' && $m->status !== 'completed' && !$m->contract?->owner_signed_at)
                    <form action="{{ route('owner.vehicles.destroy', $m) }}" method="POST" onsubmit="return confirm('Remove this bodaboda?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Remove"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    <div class="d-flex justify-content-between align-items-center mt-3">
        <span style="font-size:0.8rem;color:var(--text-secondary);">{{ $motorcycles->firstItem() ?? 0 }} to {{ $motorcycles->lastItem() ?? 0 }} of {{ $motorcycles->total() }}</span>
        {{ $motorcycles->withQueryString()->links() }}
    </div>
@else
    <div class="empty-state">
        <div class="empty-state-icon"><i class="bi bi-bicycle"></i></div>
        <h5>No Bodabodas yet</h5>
        <p>Add your first bodaboda!</p>
        <a href="{{ route('owner.vehicles.create') }}" class="btn btn-gold"><i class="bi bi-plus-lg me-1"></i> Add Bodaboda</a>
    </div>
@endif
@endsection
