<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MotorcycleApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Motorcycle::with('owner', 'driver', 'loan');

        if (Auth::user()->isOwner()) {
            $query->where('owner_id', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhereHas('driver', fn($dq) => $dq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('owner', fn($oq) => $oq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $motorcycles = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($motorcycles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:motorcycles,plate_number',
            'model' => 'nullable|string|max:100',
            'make' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'color' => 'nullable|string|max:50',
            'engine_cc' => 'nullable|string|max:10',
            'driver_id' => 'nullable|exists:users,id',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $validated['owner_id'] = Auth::user()->isOwner() ? Auth::id() : ($validated['owner_id'] ?? Auth::id());
        $validated['status'] = 'active';

        $motorcycle = Motorcycle::create($validated);

        return response()->json($motorcycle->load(['owner', 'driver']), 201);
    }

    public function show(Motorcycle $motorcycle)
    {
        $this->authorizeAccess($motorcycle);

        return response()->json($motorcycle->load(['owner', 'driver', 'loan.payments']));
    }

    public function update(Request $request, Motorcycle $motorcycle)
    {
        $this->authorizeAccess($motorcycle);

        $validated = $request->validate([
            'model' => 'nullable|string|max:100',
            'make' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'color' => 'nullable|string|max:50',
            'engine_cc' => 'nullable|string|max:10',
            'driver_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,completed,overdue,inactive',
        ]);

        $motorcycle->update($validated);

        return response()->json($motorcycle->load(['owner', 'driver']));
    }

    public function destroy(Motorcycle $motorcycle)
    {
        $this->authorizeAccess($motorcycle);
        $motorcycle->delete();

        return response()->json(['message' => 'Motorcycle deleted successfully.']);
    }

    private function authorizeAccess(Motorcycle $motorcycle)
    {
        if (Auth::user()->isOwner() && $motorcycle->owner_id !== Auth::id()) {
            abort(403);
        }
    }
}
