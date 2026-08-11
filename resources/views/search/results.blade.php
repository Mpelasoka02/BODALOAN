@extends('layouts.app')
@section('title', 'Search Results')
@section('page-title', 'Search Results')

@section('content')
<div class="mb-4">
    <h5 class="mb-0 fw-bold">Search Results</h5>
    @if($query)
        <small class="text-muted">Showing results for "<strong>{{ $query }}</strong>"</small>
    @else
        <small class="text-muted">Enter a search term to find motorcycles, users, or loans.</small>
    @endif
</div>

@if(!empty($results))
    @php $hasResults = $results['motorcycles']->count() > 0 || $results['users']->count() > 0 || $results['loans']->count() > 0; @endphp

    @if(!$hasResults)
        <div class="card">
            <div class="card-body text-center py-5">
                <h6 class="fw-bold mb-2">No Results Found</h6>
                <p class="text-muted mb-0">No items match "{{ $query }}".</p>
            </div>
        </div>
    @endif

    @if($results['motorcycles']->count() > 0)
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-bicycle" style="color:var(--info);"></i>
                <strong>Motorcycles</strong>
                <span class="badge" style="background:var(--info-bg);color:var(--info);font-size:0.7rem;">{{ $results['motorcycles']->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Plate</th><th>Make / Model</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($results['motorcycles'] as $moto)
                            <tr>
                                <td class="fw-bold">{{ $moto->plate_number }}</td>
                                <td>{{ $moto->make }} {{ $moto->model }}</td>
                                <td><span class="badge-status {{ $moto->status }}">{{ ucfirst($moto->status) }}</span></td>
                                <td><a href="{{ route('motorcycles.show', $moto) }}" class="btn btn-sm btn-icon"><i class="bi bi-arrow-right"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($results['users']->count() > 0)
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-people" style="color:var(--success);"></i>
                <strong>Users</strong>
                <span class="badge" style="background:var(--success-bg);color:var(--success);font-size:0.7rem;">{{ $results['users']->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th></th></tr></thead>
                    <tbody>
                        @foreach($results['users'] as $u)
                            <tr>
                                <td class="fw-semibold">{{ $u->name }}</td>
                                <td><small class="text-muted">{{ $u->email }}</small></td>
                                <td><span class="badge-status {{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
                                <td><a href="{{ route('admin.users') }}" class="btn btn-sm btn-icon"><i class="bi bi-arrow-right"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($results['loans']->count() > 0)
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-wallet2" style="color:var(--warning);"></i>
                <strong>Loans</strong>
                <span class="badge" style="background:var(--warning-bg);color:var(--warning);font-size:0.7rem;">{{ $results['loans']->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>ID</th><th>Motorcycle</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($results['loans'] as $loan)
                            <tr>
                                <td class="fw-bold">#{{ $loan->id }}</td>
                                <td>{{ $loan->motorcycle->plate_number ?? '-' }}</td>
                                <td><span class="badge-status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span></td>
                                <td><a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-icon"><i class="bi bi-arrow-right"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endif
@endsection