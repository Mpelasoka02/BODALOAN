<?php

namespace App\Http\Controllers;

use App\Models\DriverLocation;
use App\Models\Loan;
use App\Models\Motorcycle;
use App\Models\User;
use App\Models\VehicleLocation;
use App\Services\TraccarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric|between:0,360',
            'captured_at' => 'required|date',
        ]);

        $location = DriverLocation::create([
            'user_id' => Auth::id(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed' => $request->speed,
            'heading' => $request->heading,
            'captured_at' => $request->captured_at,
        ]);

        return response()->json(['status' => 'ok', 'id' => $location->id]);
    }

    public function driverGps()
    {
        $user = Auth::user();

        $activeLoan = Loan::where('driver_id', $user->id)
            ->where('status', 'active')
            ->with(['motorcycle', 'owner'])
            ->latest()
            ->first();

        if (!$activeLoan) {
            return view('locations.driver-gps', ['activeLoan' => null, 'ownerLocation' => null]);
        }

        $ownerLocation = null;
        if ($activeLoan->owner && $activeLoan->owner->latitude && $activeLoan->owner->longitude) {
            $ownerLocation = $activeLoan->owner;
        } elseif ($activeLoan->motorcycle && $activeLoan->motorcycle->latitude && $activeLoan->motorcycle->longitude) {
            $ownerLocation = (object) [
                'latitude' => $activeLoan->motorcycle->latitude,
                'longitude' => $activeLoan->motorcycle->longitude,
                'location_name' => $activeLoan->motorcycle->location_name,
                'name' => $activeLoan->owner->name ?? 'Owner',
            ];
        }

        if ($ownerLocation) {
            $url = 'https://www.google.com/maps/dir/?api=1&destination=' . $ownerLocation->latitude . ',' . $ownerLocation->longitude . '&travelmode=driving';
            return redirect()->away($url);
        }

        return view('locations.driver-gps', ['activeLoan' => $activeLoan, 'ownerLocation' => null]);
    }

    public function driverGpsUpdate(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric|between:0,360',
        ]);

        DriverLocation::create([
            'user_id' => Auth::id(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed' => $request->speed,
            'heading' => $request->heading,
            'captured_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function track(Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $loan->owner_id !== $user->id && $loan->driver_id !== $user->id) {
            abort(403);
        }

        $loan->load(['motorcycle', 'owner', 'driver']);
        $driver = $loan->driver;
        $latestLocation = DriverLocation::where('user_id', $driver->id)
            ->where('captured_at', '>=', now()->subHours(2))
            ->orderBy('captured_at', 'desc')
            ->first();

        $route = DriverLocation::where('user_id', $driver->id)
            ->where('captured_at', '>=', now()->subDay())
            ->orderBy('captured_at', 'asc')
            ->get(['latitude', 'longitude', 'captured_at', 'speed']);

        $ownerLocation = null;
        if ($loan->owner && $loan->owner->latitude && $loan->owner->longitude) {
            $ownerLocation = $loan->owner;
            $ownerLocation->location_source = 'profile';
        } elseif ($loan->motorcycle && $loan->motorcycle->latitude && $loan->motorcycle->longitude) {
            $ownerLocation = (object) [
                'latitude' => $loan->motorcycle->latitude,
                'longitude' => $loan->motorcycle->longitude,
                'location_name' => $loan->motorcycle->location_name,
                'name' => $loan->owner->name ?? 'Owner',
                'location_source' => 'motorcycle',
            ];
        }

        return view('locations.track', compact('loan', 'driver', 'latestLocation', 'route', 'ownerLocation'));
    }

    public function apiLatest(Loan $loan)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $loan->owner_id !== $user->id && $loan->driver_id !== $user->id) {
            abort(403);
        }

        $location = DriverLocation::where('user_id', $loan->driver_id)
            ->where('captured_at', '>=', now()->subHours(2))
            ->orderBy('captured_at', 'desc')
            ->first();

        if (!$location) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'speed' => $location->speed,
            'captured_at' => $location->captured_at,
        ]);
    }

    // =============================================
    // FLEET MAPS (GPS Tracker / Traccar)
    // =============================================

    public function adminMap()
    {
        $points = Motorcycle::where('verification_status', 'verified')
            ->where('status', 'assigned')
            ->whereNotNull('last_location_at')
            ->with('latestLocation')
            ->get()
            ->filter(fn($v) => $v->latestLocation)
            ->map(fn($v) => [
                'lat' => (float) $v->latestLocation->latitude,
                'lng' => (float) $v->latestLocation->longitude,
            ])
            ->values();

        return $this->redirectToGoogleMaps($points);
    }

    public function ownerMap()
    {
        $user = Auth::user();
        $points = Motorcycle::where('owner_id', $user->id)
            ->where('verification_status', 'verified')
            ->whereNotNull('last_location_at')
            ->with('latestLocation')
            ->get()
            ->filter(fn($v) => $v->latestLocation)
            ->map(fn($v) => [
                'lat' => (float) $v->latestLocation->latitude,
                'lng' => (float) $v->latestLocation->longitude,
            ])
            ->values();

        return $this->redirectToGoogleMaps($points);
    }

    private function redirectToGoogleMaps($points)
    {
        if ($points->isEmpty()) {
            return redirect('https://www.google.com/maps/@-6.7924,39.2083,12z');
        }

        if ($points->count() === 1) {
            $p = $points->first();
            return redirect("https://www.google.com/maps?q={$p['lat']},{$p['lng']}");
        }

        $centerLat = $points->avg('lat');
        $centerLng = $points->avg('lng');
        $latSpan = $points->max('lat') - $points->min('lat');
        $lngSpan = $points->max('lng') - $points->min('lng');
        $span = max($latSpan, $lngSpan);

        if ($span > 5) $zoom = 6;
        elseif ($span > 2) $zoom = 7;
        elseif ($span > 1) $zoom = 8;
        elseif ($span > 0.5) $zoom = 9;
        elseif ($span > 0.1) $zoom = 10;
        else $zoom = 13;

        return redirect("https://www.google.com/maps/@{$centerLat},{$centerLng},{$zoom}z");
    }

    public function apiVehiclePositions()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $vehicles = Motorcycle::where('verification_status', 'verified')
                ->where('status', 'assigned')
                ->with('latestLocation')
                ->get();
        } elseif ($user->isOwner()) {
            $vehicles = Motorcycle::where('owner_id', $user->id)
                ->where('verification_status', 'verified')
                ->with('latestLocation')
                ->get();
        } else {
            return response()->json([]);
        }

        $positions = $vehicles->filter(fn($v) => $v->latestLocation)->map(fn($v) => [
            'motorcycle_id' => $v->id,
            'plate_number' => $v->plate_number,
            'make' => $v->make,
            'model' => $v->model,
            'driver_name' => $v->driver->name ?? 'Unassigned',
            'latitude' => (float) $v->latestLocation->latitude,
            'longitude' => (float) $v->latestLocation->longitude,
            'speed' => $v->latestLocation->speed ? round($v->latestLocation->speed * 3.6) : 0,
            'course' => $v->latestLocation->course,
            'signal' => $v->gpsSignalStatus(),
            'recorded_at' => $v->latestLocation->recorded_at->toIso8601String(),
        ])->values();

        return response()->json($positions);
    }

    public function apiVehicleRoute(Request $request, Motorcycle $motorcycle)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $motorcycle->owner_id !== $user->id) {
            abort(403);
        }

        $from = $request->get('from', now()->subDay()->toIso8601String());
        $to = $request->get('to', now()->toIso8601String());

        $locations = VehicleLocation::where('motorcycle_id', $motorcycle->id)
            ->whereBetween('recorded_at', [$from, $to])
            ->orderBy('recorded_at', 'asc')
            ->get(['latitude', 'longitude', 'speed', 'recorded_at']);

        return response()->json($locations);
    }

    public function ownerTrackVehicle(Motorcycle $motorcycle)
    {
        $user = Auth::user();
        if ($motorcycle->owner_id !== $user->id) {
            abort(403);
        }

        $latest = VehicleLocation::where('motorcycle_id', $motorcycle->id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if ($latest && $latest->latitude && $latest->longitude) {
            return redirect("https://www.google.com/maps?q={$latest->latitude},{$latest->longitude}");
        }

        if ($motorcycle->latitude && $motorcycle->longitude) {
            return redirect("https://www.google.com/maps?q={$motorcycle->latitude},{$motorcycle->longitude}");
        }

        return redirect()->route('owner.vehicles.show', $motorcycle)->with('warning', 'No GPS data available for this bodaboda yet.');
    }

    public function apiVehicleStats()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $total = Motorcycle::where('verification_status', 'verified')->where('status', 'assigned')->count();
            $withGps = Motorcycle::where('verification_status', 'verified')->where('status', 'assigned')->whereNotNull('gps_device_id')->count();
            $live = Motorcycle::where('verification_status', 'verified')->where('status', 'assigned')
                ->whereNotNull('last_location_at')
                ->where('last_location_at', '>=', now()->subHours(1))
                ->count();
        } else {
            $total = Motorcycle::where('owner_id', $user->id)->where('verification_status', 'verified')->count();
            $withGps = Motorcycle::where('owner_id', $user->id)->where('verification_status', 'verified')->whereNotNull('gps_device_id')->count();
            $live = Motorcycle::where('owner_id', $user->id)->where('verification_status', 'verified')
                ->whereNotNull('last_location_at')
                ->where('last_location_at', '>=', now()->subHours(1))
                ->count();
        }

        return response()->json([
            'total_vehicles' => $total,
            'with_gps' => $withGps,
            'live' => $live,
        ]);
    }
}
