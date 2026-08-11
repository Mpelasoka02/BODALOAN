<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::get('/verify-email', [VerificationController::class, 'showVerifyForm'])->name('verify.email');
Route::post('/verify-email', [VerificationController::class, 'verify'])->name('verify.email.submit');
Route::post('/verify-email/resend', [VerificationController::class, 'resend'])->name('verify.email.resend');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
