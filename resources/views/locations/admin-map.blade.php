@extends('layouts.app')

@section('title', 'Fleet GPS — BodaLink')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-geo-alt-fill text-warning"></i> Fleet GPS</h4>
        <p class="text-muted mb-0 small">Track all assigned bodabodas across the platform</p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-success"><i class="bi bi-broadcast"></i> {{ $vehicles->where('signal_status', 'live')->count() }} Live</span>
        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> {{ $vehicles->where('signal_status', 'stale')->count() }} Stale</span>
        <span class="badge bg-secondary"><i class="bi bi-off"></i> {{ $vehicles->where('signal_status', 'none')->count() }} No Signal</span>
    </div>
</div>

@if($vehicles->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Status</th>
                    <th>Plate Number</th>
                    <th>Bodaboda</th>
                    <th>Driver</th>
                    <th>Owner</th>
                    <th>Last Seen</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicles as $v)
                <tr>
                    <td>
                        @if($v->signal_status === 'live')
                            <span class="badge bg-success-subtle text-success"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Live</span>
                        @elseif($v->signal_status === 'stale')
                            <span class="badge bg-warning-subtle text-warning"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Stale</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>No Signal</span>
                        @endif
                    </td>
                    <td class="fw-bold text-primary">{{ $v->plate_number }}</td>
                    <td>{{ $v->make }} {{ $v->model }} {{ $v->year ? '(' . $v->year . ')' : '' }}</td>
                    <td>
                        @if($v->driver)
                            <span>{{ $v->driver->name }}</span>
                            @if($v->driver->phone)
                                <br><small class="text-muted">{{ $v->driver->phone }}</small>
                            @endif
                        @else
                            <span class="text-muted">Unassigned</span>
                        @endif
                    </td>
                    <td>{{ $v->owner->name ?? '—' }}</td>
                    <td class="text-muted small">
                        @if($v->last_location_at)
                            {{ $v->last_location_at->diffForHumans() }}
                            @if($v->latestLocation)
                                <br><small>{{ $v->latestLocation->latitude }}, {{ $v->latestLocation->longitude }}</small>
                            @endif
                        @else
                            Never
                        @endif
                    </td>
                    <td class="text-end">
                        @if($v->latestLocation)
                            <a href="https://www.google.com/maps?q={{ $v->latestLocation->latitude }},{{ $v->latestLocation->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open in Maps
                            </a>
                        @else
                            <span class="text-muted small">No location</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card border-0 shadow-sm text-center py-5">
    <i class="bi bi-off display-4 text-muted"></i>
    <h5 class="mt-3">No Bodabodas Tracked</h5>
    <p class="text-muted">No verified bodabodas with GPS data yet.</p>
</div>
@endif
@endsection
