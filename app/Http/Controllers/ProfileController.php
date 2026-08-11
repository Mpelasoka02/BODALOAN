<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'nida' => 'nullable|string|max:30|unique:users,nida,' . $user->id,
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        foreach (['profile_photo', 'id_photo'] as $field) {
            if ($request->hasFile($field)) {
                if ($user->$field) {
                    Storage::disk('public')->delete($user->$field);
                }
                $validated[$field] = $request->file($field)->store('users', 'public');
            }
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function submitVerification(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nida' => 'required|string|size:20|unique:users,nida,' . $user->id,
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'id_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        foreach (['profile_photo', 'id_photo'] as $field) {
            if ($request->hasFile($field)) {
                if ($user->$field) {
                    Storage::disk('public')->delete($user->$field);
                }
                $validated[$field] = $request->file($field)->store('users', 'public');
            }
        }

        $validated['verification_submitted_at'] = now();
        $validated['rejection_reason'] = null;

        $user->update($validated);

        return redirect()->route('verification.form')->with('success', 'Verification documents submitted successfully. Please wait for admin approval.');
    }
}
