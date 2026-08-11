@extends('layouts.app')
@section('title', 'Track Driver')
@section('page-title', 'Track Driver')

@section('styles')
<style>
    .track-map-wrap {
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--border);
        height: 500px;
    }
    .track-info-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 20px;
    }
    .track-info-card h5 {
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .track-info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
        font-size: 0.85rem;
    }
    .track-info-row:last-child { border-bottom: none; }
    .track-info-row .k { color: var(--text-muted); }
    .track-info-row .v { font-weight: 600; color: var(--text); }
    .track-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .track-status.live { background: var(--emerald-100); color: var(--emerald-600); }
    .track-status.stale { background: var(--gold-100); color: var(--gold-500); }
    .track-status.none { background: var(--page-bg); color: var(--text-muted); }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <nav aria-label="breadcrumb" style="font-size:0.78rem;margin-bottom:4px;">
            <a href="{{ route('dashboard') }}" style="color:var(--text-muted);text-decoration:none;">Dashboard</a>
            <span style="color:var(--border);margin:0 6px;">/</span>
            <a href="{{ route('loans.show', $loan) }}" style="color:var(--text-muted);text-decoration:none;">Loan #{{ $loan->id }}</a>
            <span style="color:var(--border);margin:0 6px;">/</span>
            <span style="color:var(--text);">Track</span>
        </nav>
        <h5 class="mb-0" style="font-weight:700;">Track {{ $driver->name }}</h5>
    </div>
    <a href="{{ route('loans.show', $loan) }}" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div style="display:flex;gap:8px;margin-bottom:16px;">
    @if($latestLocation)
        <span class="track-status live"><i class="bi bi-broadcast"></i> Live — {{ $latestLocation->captured_at->diffForHumans() }}</span>
    @else
        <span class="track-status none"><i class="bi bi-off"></i> No recent data</span>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;">
    <div class="track-map-wrap">
        <div id="map" style="width:100%;height:100%;"></div>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="track-info-card">
            <h5><i class="bi bi-person-badge" style="color:var(--navy-700);"></i> Driver</h5>
            <div class="track-info-row">
                <span class="k">Name</span>
                <span class="v">{{ $driver->name }}</span>
            </div>
            <div class="track-info-row">
                <span class="k">Phone</span>
                <span class="v">{{ $driver->phone }}</span>
            </div>
            <div class="track-info-row">
                <span class="k">Bodaboda</span>
                <span class="v">{{ $loan->motorcycle->plate_number }}</span>
            </div>
        </div>

    @if($ownerLocation)
        <div class="track-info-card" style="border-left:3px solid var(--gold-500);">
            <h5><i class="bi bi-geo-alt-fill" style="color:var(--gold-500);"></i> Owner Location</h5>
            <div class="track-info-row">
                <span class="k">Name</span>
                <span class="v">{{ $ownerLocation->name }}</span>
            </div>
            <div class="track-info-row">
                <span class="k">Location</span>
                <span class="v">{{ $ownerLocation->location_name ?: 'Set location' }}</span>
            </div>
            <div class="track-info-row">
                <span class="k">Coordinates</span>
                <span class="v">{{ $ownerLocation->latitude }}, {{ $ownerLocation->longitude }}</span>
            </div>
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $ownerLocation->latitude }},{{ $ownerLocation->longitude }}&travelmode=driving" target="_blank" class="btn btn-gold btn-sm w-100 mt-2" style="font-size:0.82rem;">
                <i class="bi bi-sign-turn-right me-1"></i> Navigate to Owner
            </a>
        </div>
    @endif

        @if($latestLocation)
            <div class="track-info-card">
                <h5><i class="bi bi-geo-alt" style="color:var(--gold-500);"></i> Last Position</h5>
                <div class="track-info-row">
                    <span class="k">Time</span>
                    <span class="v">{{ $latestLocation->captured_at->format('d M H:i:s') }}</span>
                </div>
                <div class="track-info-row">
                    <span class="k">Latitude</span>
                    <span class="v">{{ $latestLocation->latitude }}</span>
                </div>
                <div class="track-info-row">
                    <span class="k">Longitude</span>
                    <span class="v">{{ $latestLocation->longitude }}</span>
                </div>
                @if($latestLocation->speed !== null)
                    <div class="track-info-row">
                        <span class="k">Speed</span>
                        <span class="v">{{ round($latestLocation->speed * 3.6) }} km/h</span>
                    </div>
                @endif
                <button class="btn btn-outline w-100 mt-2" style="font-size:0.82rem;" onclick="refreshLocation()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                </button>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
function initMap() {
    const centerLat = {{ $latestLocation ? $latestLocation->latitude : ($ownerLocation ? $ownerLocation->latitude : '-6.3690') }};
    const centerLng = {{ $latestLocation ? $latestLocation->longitude : ($ownerLocation ? $ownerLocation->longitude : '34.8888') }};
    const centerZoom = {{ $latestLocation ? 15 : ($ownerLocation ? 15 : 7) }};

    const map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: centerLat, lng: centerLng},
        zoom: centerZoom,
        disableDefaultUI: false,
        zoomControl: true,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true
    });

    @if($ownerLocation)
    const ownerPos = {lat: {{ $ownerLocation->latitude }}, lng: {{ $ownerLocation->longitude }}};
    const ownerMarker = new google.maps.Marker({
        position: ownerPos,
        map: map,
        icon: {
            path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
            scale: 8,
            fillColor: '#C9962C',
            fillOpacity: 1,
            strokeColor: '#fff',
            strokeWeight: 2
        },
        title: 'Owner - {{ addslashes($ownerLocation->name) }}'
    });
    const ownerInfo = new google.maps.InfoWindow({
        content: '<div style="font-family:Inter,sans-serif;padding:4px;"><strong style="color:#C9962C;">Owner Pickup</strong><br><small>{{ addslashes($ownerLocation->location_name ?: $ownerLocation->name) }}</small></div>'
    });
    ownerMarker.addListener('click', function() { ownerInfo.open(map, ownerMarker); });
    @endif

    @if($latestLocation)
    const pos = {lat: {{ $latestLocation->latitude }}, lng: {{ $latestLocation->longitude }}};
    map.setCenter(pos);
    map.setZoom(15);

    const marker = new google.maps.Marker({
        position: pos,
        map: map,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: '#0E9F6E',
            fillOpacity: 1,
            strokeColor: '#fff',
            strokeWeight: 3
        },
        title: '{{ addslashes($driver->name) }}'
    });

    const infoWindow = new google.maps.InfoWindow({
        content: '<div style="font-family:Inter,sans-serif;padding:4px;"><strong>{{ addslashes($driver->name) }}</strong><br><small>{{ $loan->motorcycle->plate_number }}</small></div>'
    });
    marker.addListener('click', function() { infoWindow.open(map, marker); });
    infoWindow.open(map, marker);

    @if($ownerLocation)
    const directionsService = new google.maps.DirectionsService();
    const directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: true,
        polylineOptions: {
            strokeColor: '#C9962C',
            strokeOpacity: 0.8,
            strokeWeight: 3
        }
    });
    directionsService.route({
        origin: pos,
        destination: ownerPos,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(response, status) {
        if (status === 'OK') {
            directionsRenderer.setDirections(response);
        }
    });
    @endif
    @endif

    @if(count($route) > 1)
    const routeCoords = [
        @foreach($route as $pt)
            {lat: {{ $pt->latitude }}, lng: {{ $pt->longitude }}},
        @endforeach
    ];
    new google.maps.Polyline({
        path: routeCoords,
        geodesic: true,
        strokeColor: '#0E9F6E',
        strokeOpacity: 0.7,
        strokeWeight: 3,
        map: map
    });
    @endif
}

function refreshLocation() {
    fetch('{{ route('locations.api.latest', $loan) }}')
        .then(r => r.json())
        .then(d => { if (d.found) location.reload(); });
}
setInterval(refreshLocation, 30000);
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', '') }}&callback=initMap"></script>
@endsection
