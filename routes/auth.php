<?php

use App\Http\Controllers\VerifyOtpController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Livewire\Pages\Auth\Register;


Route::middleware('guest')->group(function () {

    Volt::route('/', 'pages.auth.login')->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');

});


Route::middleware(['auth', 'verified.redirect'])->prefix('verify')->group(function () {

    Volt::route('email', 'pages.auth.verify-otp')->name('verification.notice');

    Route::post('verify-otp', [VerifyOtpController::class, 'verifyOtp'])->name('verification.otp.submit');

    Route::post('email/verification-notification', function () {
        auth()->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');

});



