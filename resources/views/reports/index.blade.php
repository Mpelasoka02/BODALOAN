@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', auth()->user()->isAdmin() ? 'Admin Reports' : 'Owner Reports')

@push('styles')
<style>
    .chart-container { position: relative; height: 260px; width: 100%; }
    .stat-mini { border-left: 3px solid var(--navy-700); }
    .stat-mini.gold { border-left-color: var(--gold-500); }
    .stat-mini.emerald { border-left-color: #059669; }
    .stat-mini.danger { border-left-color: #ef4444; }
    .stat-mini.purple { border-left-color: #7c3aed; }
    .stat-mini-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-muted); font-weight: 600; }
    .stat-mini-value { font-size: 1.3rem; font-weight: 800; color: var(--text); }
    .stat-mini-sub { font-size: 0.78rem; color: var(--text-secondary); margin-top: 2px; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0 fw-bold" style="color:var(--text);">Reports & Analytics</h5>
        <p class="mb-0 mt-1" style="font-size:0.82rem;color:var(--text-secondary);">Platform performance overview</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
</div>

@if(session('success'))
    <div class="alert-banner green mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill" style="color:var(--emerald-600);"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

{{-- ── SUMMARY CARDS ── --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-mini gold">
            <div class="card-body">
                <div class="stat-mini-label">Total Disbursed</div>
                <div class="stat-mini-value">TZS {{ number_format($totalDisbursed) }}</div>
                <div class="stat-mini-sub">Total loan value</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-mini emerald">
            <div class="card-body">
                <div class="stat-mini-label">Total Collected</div>
                <div class="stat-mini-value" style="color:#059669;">TZS {{ number_format($totalCollected) }}</div>
                @php $collectionRate = $totalDisbursed > 0 ? round(($totalCollected / $totalDisbursed) * 100, 1) : 0; @endphp
                <div class="stat-mini-sub">{{ $collectionRate }}% collection rate</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-mini">
            <div class="card-body">
                <div class="stat-mini-label">Active Loans</div>
                <div class="stat-mini-value">{{ $activeLoans }}</div>
                <div class="stat-mini-sub">{{ $completedLoans }} completed</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-mini danger">
            <div class="card-body">
                <div class="stat-mini-label">Overdue Loans</div>
                <div class="stat-mini-value" style="color:#ef4444;">{{ $overdueLoans }}</div>
                <div class="stat-mini-sub">{{ $pendingPayments }} pending payments</div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-mini purple">
            <div class="card-body">
                <div class="stat-mini-label">Total Users</div>
                <div class="stat-mini-value" style="color:#7c3aed;">{{ $usersByRole->sum() }}</div>
                <div class="stat-mini-sub">Owners + Drivers + Admin</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-mini">
            <div class="card-body">
                <div class="stat-mini-label">Motorcycles</div>
                <div class="stat-mini-value">{{ $registeredMotorcycles }}</div>
                <div class="stat-mini-sub">Registered on platform</div>
            </div>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->isOwner())
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card stat-mini gold">
            <div class="card-body">
                <div class="stat-mini-label">Outstanding Balance</div>
                <div class="stat-mini-value">TZS {{ number_format($outstandingBalance ?? 0) }}</div>
                <div class="stat-mini-sub">Across all active loans</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card stat-mini emerald">
            <div class="card-body">
                <div class="stat-mini-label">Weekly Collections</div>
                <div class="stat-mini-value" style="color:#059669;">TZS {{ number_format($weeklyCollections ?? 0) }}</div>
                <div class="stat-mini-sub">This week</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── CHARTS ── --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong style="color:var(--text);font-size:0.9rem;">Monthly Collections</strong>
        <span class="badge-status verified" style="font-size:0.7rem;">TZS {{ number_format($totalCollected) }} total</span>
    </div>
    <div class="card-body">
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <strong style="color:var(--text);font-size:0.9rem;">Loan Distribution</strong>
    </div>
    <div class="card-body">
        <div class="chart-container">
            <canvas id="loanStatusChart"></canvas>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="card mb-4">
    <div class="card-header">
        <strong style="color:var(--text);font-size:0.9rem;">Users by Role</strong>
    </div>
    <div class="card-body">
        <div class="chart-container" style="height:220px;">
            <canvas id="usersChart"></canvas>
        </div>
    </div>
</div>
@endif

<div class="card mb-4">
    <div class="card-header">
        <strong style="color:var(--text);font-size:0.9rem;">{{ auth()->user()->isAdmin() ? 'Motorcycles by Status' : 'My Motorcycles by Status' }}</strong>
    </div>
    <div class="card-body">
        <div class="chart-container" style="height:220px;">
            <canvas id="motorcycleChart"></canvas>
        </div>
    </div>
</div>

{{-- ── COLLECTIONS TABLE ── --}}
@if(isset($monthlyCollections) && $monthlyCollections->count())
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong style="color:var(--text);font-size:0.9rem;">Monthly Collections Detail</strong>
        <span class="badge-status active" style="font-size:0.7rem;">{{ $monthlyCollections->count() }} months</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Amount Collected</th>
                    <th>Payments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyCollections as $mc)
                    <tr>
                        <td class="fw-semibold" style="color:var(--text);">{{ is_array($mc) ? ($mc['month'] ?? '-') : ($mc->month ?? '-') }}</td>
                        <td class="fw-semibold" style="color:var(--emerald-600);">TZS {{ number_format(is_array($mc) ? $mc['total'] : $mc->total) }}</td>
                        <td>{{ is_array($mc) ? ($mc['count'] ?? 0) : ($mc->count ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── DEFAULTERS TABLE ── --}}
@if(isset($defaulters) && $defaulters->count())
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong style="color:var(--text);font-size:0.9rem;">Overdue Loans</strong>
        <span class="badge-status rejected" style="font-size:0.7rem;">{{ $defaulters->count() }} overdue</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Driver</th>
                    <th>Motorcycle</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($defaulters as $def)
                    <tr>
                        <td class="fw-semibold" style="color:var(--text);">{{ $def->driver->name ?? '-' }}</td>
                        <td>{{ $def->motorcycle->plate_number ?? '-' }}</td>
                        <td class="fw-semibold" style="color:#ef4444;">TZS {{ number_format($def->balance) }}</td>
                        <td><span class="badge-status overdue">Overdue</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── OWNER PERFORMANCE (Admin only) ── --}}
@if(auth()->user()->isAdmin() && isset($ownerPerformance) && $ownerPerformance->count())
<div class="card mb-4">
    <div class="card-header">
        <strong style="color:var(--text);font-size:0.9rem;">Owner Performance</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Owner</th>
                    <th>Total Loans</th>
                    <th>Active</th>
                    <th>Completed</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ownerPerformance as $owner)
                    <tr>
                        <td class="fw-semibold" style="color:var(--text);">{{ $owner->name }}</td>
                        <td>{{ $owner->total_loans_count }}</td>
                        <td><span class="badge-status active">{{ $owner->active_loans_count }}</span></td>
                        <td><span class="badge-status verified">{{ $owner->completed_loans_count }}</span></td>
                        <td class="fw-semibold" style="color:var(--emerald-600);">TZS {{ number_format($owner->total_revenue) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if(!isset($monthlyCollections) || !$monthlyCollections->count())
    @if(!isset($defaulters) || !$defaulters->count())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-bar-chart-line" style="font-size:2rem;color:var(--text-muted);display:block;margin-bottom:8px;"></i>
        <h6 class="fw-bold mb-1" style="color:var(--text);">No Data Yet</h6>
        <p style="font-size:0.85rem;color:var(--text-secondary);">Report data will appear here once loans and payments are recorded.</p>
    </div>
</div>
    @endif
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = {
        navy: '#1e293b',
        navyLight: '#334155',
        gold: '#d97706',
        goldLight: '#f59e0b',
        emerald: '#059669',
        emeraldLight: '#10b981',
        red: '#ef4444',
        redLight: '#f87171',
        purple: '#7c3aed',
        purpleLight: '#a78bfa',
        slate: '#64748b',
        blue: '#3b82f6',
    };

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#64748b';

    // ── Revenue Bar Chart ──
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueData = @json($monthlyCollections ?? collect());
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: revenueData.map(d => d.month),
                datasets: [{
                    label: 'Amount Collected (TZS)',
                    data: revenueData.map(d => d.total),
                    backgroundColor: revenueData.map((_, i) => i === revenueData.length - 1 ? colors.gold : colors.goldLight + '99'),
                    borderColor: colors.gold,
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: colors.navy,
                        titleColor: '#fff',
                        bodyColor: '#e2e8f0',
                        cornerRadius: 8,
                        padding: 12,
                        callbacks: {
                            label: function(ctx) {
                                return 'TZS ' + ctx.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 11 },
                            callback: function(val) {
                                if (val >= 1000000) return (val/1000000).toFixed(0) + 'M';
                                if (val >= 1000) return (val/1000).toFixed(0) + 'K';
                                return val;
                            }
                        }
                    }
                }
            }
        });
    }

    // ── Loan Status Doughnut ──
    const loanCtx = document.getElementById('loanStatusChart');
    if (loanCtx) {
        const loanData = @json($loansByStatus ?? collect());
        const loanLabels = Object.keys(loanData);
        const loanValues = Object.values(loanData);
        const statusColors = loanLabels.map(s => {
            if (s === 'active') return colors.gold;
            if (s === 'completed') return colors.emerald;
            if (s === 'overdue') return colors.red;
            if (s === 'defaulted') return colors.slate;
            return colors.blue;
        });
        new Chart(loanCtx, {
            type: 'doughnut',
            data: {
                labels: loanLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                datasets: [{
                    data: loanValues,
                    backgroundColor: statusColors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10, font: { size: 11 } }
                    },
                    tooltip: {
                        backgroundColor: colors.navy,
                        cornerRadius: 8,
                        padding: 12,
                    }
                }
            }
        });
    }

    // ── Users by Role Doughnut (admin only) ──
    const usersCtx = document.getElementById('usersChart');
    if (usersCtx) {
        const usersData = @json($usersByRole ?? collect());
        const userLabels = array_keys ? Object.keys(usersData) : [];
        const userValues = array_values ? Object.values(usersData) : [];
        const userColors = userLabels.map(r => {
            if (r === 'admin') return colors.purple;
            if (r === 'owner') return colors.gold;
            if (r === 'driver') return colors.emerald;
            return colors.slate;
        });
        new Chart(usersCtx, {
            type: 'doughnut',
            data: {
                labels: userLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                datasets: [{
                    data: userValues,
                    backgroundColor: userColors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // ── Motorcycle Status Doughnut ──
    const motoCtx = document.getElementById('motorcycleChart');
    if (motoCtx) {
        const motoData = @json($motorcyclesByStatus ?? collect());
        const motoLabels = Object.keys(motoData);
        const motoValues = Object.values(motoData);
        const motoColors = motoLabels.map(s => {
            if (s === 'available') return colors.emerald;
            if (s === 'assigned') return colors.gold;
            if (s === 'completed') return colors.blue;
            if (s === 'pending') return colors.slate;
            return colors.navyLight;
        });
        new Chart(motoCtx, {
            type: 'doughnut',
            data: {
                labels: motoLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                datasets: [{
                    data: motoValues,
                    backgroundColor: motoColors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10, font: { size: 11 } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
