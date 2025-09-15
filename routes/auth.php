<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::middleware('guest')->group(function () {

    Volt::route('/', 'pages.auth.login')->name('login');
    Volt::route('/admin', 'pages.auth.admin.login')->name('admin.login');
    Volt::route('/hr-liaison', 'pages.auth.hr-liaison.login')->name('hr-liaison.login');

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

Route::middleware(['auth', 'verified'])->group(function () {

    // -------------------- Admin Routes --------------------
    Route::middleware('role:admin')->group(function () {
        Volt::route('/dashboard', 'user.admin.dashboard.index')->name('dashboard');
        Volt::route('admin/activity-logs', 'user.admin.activtiy-logs.index')->name('activity-logs.index');
        Volt::route('admin/users/citizens', 'user.admin.users.citizens.index')->name('users.citizens');
        Volt::route('admin/users/hr-liaisons', 'user.admin.users.hr-liaisons.index')->name('users.hr-liaisons');
    });

    // -------------------- Citizen Routes --------------------
    Route::middleware('role:citizen')->group(function () {
        Volt::route('grievance/index', 'user.citizen.grievance.index')->name('grievance.index');
        Volt::route('grievance/create', 'user.citizen.grievance.create')->name('grievance.create');
        Volt::route('grievance/edit', 'user.citizen.grievance.edit')->name('grievance.edit');
    });

    // -------------------- General Routes --------------------
    Volt::route('/settings', 'layout.settings')->name('settings');
    Volt::route('/settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('/settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('/settings/two-factor-auth', 'settings.two-factor-auth')->name('settings.two-factor-auth');
    Volt::route('user/confirm-password', 'pages.auth.password-confirm')->name('password.confirm');
    Volt::route('/sidebar', 'layout.sidebar')->name('sidebar');

});

