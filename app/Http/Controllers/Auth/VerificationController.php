<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    public function showVerifyForm(Request $request)
    {
        $email = $request->email;
        $debugCode = null;

        if (Auth::check()) {
            $email = Auth::user()->email;
            if (app()->environment('local') && Auth::user()->email_verification_code) {
                $debugCode = Auth::user()->email_verification_code;
            }
        } elseif ($email && app()->environment('local')) {
            $user = User::where('email', $email)->first();
            if ($user && $user->email_verification_code) {
                $debugCode = $user->email_verification_code;
            }
        }

        return view('auth.verify-email', compact('email', 'debugCode'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = null;

        if (Auth::check()) {
            $user = Auth::user();
        } elseif ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        }

        if (!$user) {
            return back()->withErrors(['code' => __('messages.invalid_code')]);
        }

        if ($user->email_verification_code !== $request->code) {
            return back()->withErrors(['code' => __('messages.invalid_code')]);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
        ]);

        if (!Auth::check()) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        if ($user->isPending()) {
            return redirect()->route('verification.form')->with('success', __('messages.email_verified'));
        }

        return redirect()->route('dashboard')->with('success', __('messages.email_verified'));
    }

    public function resend(Request $request)
    {
        $user = null;

        if (Auth::check()) {
            $user = Auth::user();
        } elseif ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        }

        if (!$user) {
            return back()->withErrors(['code' => __('messages.invalid_code')]);
        }

        if ($user->email_verified_at) {
            return back()->with('success', __('messages.email_verified'));
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update(['email_verification_code' => $code]);

        try {
            $sms = app(SmsService::class);
            $sms->sendVerificationCode($user->phone, $user->name, $code);
        } catch (\Exception $e) {
            Log::error('Failed to resend verification SMS', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }

        return back()->with('success', __('messages.code_sent', ['email' => $user->phone]));
    }
}
