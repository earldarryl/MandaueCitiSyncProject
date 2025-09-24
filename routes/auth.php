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

    // -------------------- Citizen Routes --------------------
    Route::middleware('role:citizen')->group(function () {
        Volt::route('citizen/grievance/index', 'user.citizen.grievance.index')->name('citizen.grievance.index');
        Volt::route('citizen/grievance/create', 'user.citizen.grievance.create')->name('citizen.grievance.create');
        Volt::route('citizen/grievance/view/{id}', 'user.citizen.grievance.view')->name('citizen.grievance.view');
        Volt::route('citizen/grievance/edit/{id}', 'user.citizen.grievance.edit')->name('citizen.grievance.edit');
    });

    // -------------------- HR Liaison Routes --------------------
    Route::middleware('role:hr_liaison')->group(function () {
        Volt::route('hr-liaison/dashboard', 'user.hr-liaison.dashboard.index')->name('hr-liaison.dashboard');
        Volt::route('hr-liaison/department/index', 'user.hr-liaison.deparment.index')->name('hr-liaison.department.index');
        Volt::route('hr-liaison/grievance/index', 'user.hr-liaison.grievance.index')->name('hr-liaison.grievance.index');
        Volt::route('hr-liaison/grievance/view/{id}', 'user.hr-liaison.grievance.view')->name('hr-liaison.grievance.view');
        Volt::route('hr-liaison/activity-logs', 'user.admin.activtiy-logs.index')->name('hr-liaison.activity-logs.index');
    });

    // -------------------- Admin Routes --------------------
    Route::middleware('role:admin')->group(function () {
        Volt::route('admin/dashboard', 'user.admin.dashboard.index')->name('admin.dashboard');
        Volt::route('admin/activity-logs', 'user.admin.activtiy-logs.index')->name('admin.activity-logs.index');
        Volt::route('admin/users/citizens', 'user.admin.users.citizens.index')->name('admin.users.citizens');
        Volt::route('admin/users/hr-liaisons', 'user.admin.users.hr-liaisons.index')->name('admin.users.hr-liaisons');
    });

    // -------------------- General Routes --------------------
    Volt::route('/settings', 'layout.settings')->name('settings');
    Volt::route('/settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('/settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('/settings/two-factor-auth', 'settings.two-factor-auth')->name('settings.two-factor-auth');
    Volt::route('user/confirm-password', 'pages.auth.password-confirm')->name('password.confirm');
    Volt::route('/sidebar', 'layout.sidebar')->name('sidebar');

});

