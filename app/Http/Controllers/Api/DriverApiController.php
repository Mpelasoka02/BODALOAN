<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DriverApiController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'driver')->withCount('loans');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $drivers = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($drivers);
    }

    public function show(User $driver)
    {
        $driver->load('assignedMotorcycle.loan', 'loans.payments');

        return response()->json($driver);
    }
}
