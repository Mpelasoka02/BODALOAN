<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifiedUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin() || $user->isApproved()) {
            return $next($request);
        }

        if (!$user->hasVerificationDocuments()) {
            return redirect()->route('verification.form')->with('warning', 'Please complete your account verification to access this feature. You need to submit your NIDA number, profile photo, and ID photo.');
        }

        if ($user->hasSubmittedVerification()) {
            return redirect()->route('verification.form')->with('warning', 'Your verification is pending admin review. Please wait for approval to access this feature.');
        }

        return $next($request);
    }
}
