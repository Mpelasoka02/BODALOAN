<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirebaseController extends Controller
{
    public function registerToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'platform' => 'nullable|string|in:web,android,ios',
        ]);

        $firebase = app(FirebaseService::class);
        $result = $firebase->registerDeviceToken(
            Auth::id(),
            $validated['token'],
            $validated['platform'] ?? 'web'
        );

        return response()->json(['success' => $result]);
    }

    public function removeToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $firebase = app(FirebaseService::class);
        $result = $firebase->removeDeviceToken(Auth::id(), $validated['token']);

        return response()->json(['success' => $result]);
    }
}
