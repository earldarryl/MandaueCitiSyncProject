<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::middleware('guest')->group(function () {

    Volt::route('/', 'pages.auth.login')->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');

});

Route::middleware(['auth', 'verified.redirect'])->prefix('verify')->group(function () {

    Volt::route('email', 'pages.auth.verify-otp')->name('verification.notice');

    Route::post('email/verification-notification', function () {
        auth()->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');

});

Route::middleware(['auth', 'verified'])->group(function(){

    Volt::route('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('user/confirm-password', 'pages.auth.password-confirm')->name('password.confirm');
    Volt::route('user/admin/activity-logs', 'user.admin.activity-logs')->name('user.admin.activity-logs');
    Volt::route('user/admin/users/citizens', 'user.admin.users.citizens')->name('user.admin.users.citizens');
    Volt::route('user/citizen/grievance-form', 'user.citizen.grievance-form')->name('user.citizen.grievance-form');
    Volt::route('/settings', 'layout.settings')->name('settings');
    Volt::route('/settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('/settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('/settings/two-factor-auth', 'settings.two-factor-auth')->name('settings.two-factor-auth');
    Volt::route('/sidebar', 'layout.sidebar')->name('sidebar');

});
