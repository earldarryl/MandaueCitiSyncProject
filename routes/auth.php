<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('emails/verify-otp', function () {
    return view('emails.verify-otp');
});
Route::get('emails/reset-password', function () {
    return view('emails.reset-password');
});

Route::middleware('guest')->group(function () {
    Volt::route('/', 'pages.auth.login')->name('login');
    Volt::route('/admin', 'pages.auth.admin.login')->name('admin.login');
    Volt::route('/hr-liaison', 'pages.auth.hr-liaison.login')->name('hr-liaison.login');
    Volt::route('forgot-password', 'pages.auth.forgot-password')->name('password.request');
    Volt::route('reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');
});

Route::middleware(['auth', 'verified.redirect', 'single_session'])->prefix('verify')->group(function () {
    Volt::route('email', 'pages.auth.verify-otp')->name('verification.notice');

    Route::post('email/verification-notification', function () {
        auth()->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'verified', 'single_session'])->group(function () {

    Route::middleware('role:citizen')->group(function () {
        Volt::route('citizen/grievance/index', 'user.citizen.grievance.index')->name('citizen.grievance.index');
        Volt::route('citizen/grievance/create', 'user.citizen.grievance.create')->name('citizen.grievance.create');
        Volt::route('citizen/grievance/view/{grievance}', 'user.citizen.grievance.view')->name('citizen.grievance.view');
        Volt::route('citizen/grievance/edit/{grievance}', 'user.citizen.grievance.edit')->name('citizen.grievance.edit');
        Volt::route('citizen/feedback-form', 'user.citizen.feedback-form')->name('citizen.feedback-form');
        Volt::route('citizen/submission-history', 'user.citizen.submission-history')->name('citizen.submission-history');
    });

    Route::middleware('role:hr_liaison')->group(function () {
        Volt::route('hr-liaison/dashboard', 'user.hr-liaison.dashboard.index')->name('hr-liaison.dashboard');
        Volt::route('hr-liaison/department/index', 'user.hr-liaison.department.index')->name('hr-liaison.department.index');
        Volt::route('hr-liaison/department/{department}', 'user.hr-liaison.department.view')->name('hr-liaison.department.view');
        Volt::route('hr-liaison/grievance/index', 'user.hr-liaison.grievance.index')->name('hr-liaison.grievance.index');
        Volt::route('hr-liaison/grievance/view/{grievance}', 'user.hr-liaison.grievance.view')->name('hr-liaison.grievance.view');
        Volt::route('hr-liaison/activity-logs', 'user.hr-liaison.activity-logs.index')->name('hr-liaison.activity-logs.index');
        Volt::route('hr-liaison/reports-and-analytics/index', 'user.hr-liaison.reports-and-analytics.index')->name('hr-liaison.reports-and-analytics.index');
    });

    Route::middleware('role:admin')->group(function () {
        Volt::route('admin/dashboard', 'user.admin.dashboard.index')->name('admin.dashboard');
        Volt::route('admin/stakeholders/citizens', 'user.admin.stakeholders.citizens.index')->name('admin.stakeholders.citizens.index');
        Volt::route('admin/stakeholders/citizens/{id}', 'user.admin.stakeholders.citizens.view')->name('admin.stakeholders.citizens.view');
        Volt::route('admin/stakeholders/departments-and-hr-liaisons', 'user.admin.stakeholders.departments-and-hr-liaisons.index')->name('admin.stakeholders.departments-and-hr-liaisons.index');
        Volt::route('admin/stakeholders/departments-and-hr-liaisons/hr-liaisons-list-view/{department}', 'user.admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view')->name('admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view');
        Volt::route('admin/forms/grievances', 'user.admin.forms.grievances.index')->name('admin.forms.grievances.index');
        Volt::route('admin/forms/grievances/view/{grievance}', 'user.admin.forms.grievances.view')->name('admin.forms.grievances.view');
        Volt::route('admin/forms/feedbacks', 'user.admin.forms.feedbacks.index')->name('admin.forms.feedbacks.index');
        Volt::route('admin/forms/feedbacks/view/{id}', 'user.admin.forms.feedbacks.view')->name('admin.forms.feedbacks.view');
        Volt::route('admin/activity-logs', 'user.admin.admin-activity-logs.index')->name('admin.activtiy-logs.index');
        Volt::route('admin/reports-and-analytics/index', 'user.admin.reports-and-analytics.index')->name('admin.reports-and-analytics.index');
    });

    Volt::route('/settings', 'layout.settings')->name('settings');
    Volt::route('/settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('/settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('/settings/two-factor-auth', 'settings.two-factor-auth')->name('settings.two-factor-auth');
    Volt::route('/user/confirm-password', 'pages.auth.confirm-password')->name('password.confirm');
    Volt::route('/sidebar', 'layout.sidebar')->name('sidebar');

    Volt::route('/print/print-all-grievances', 'print-files.print-all-grievances')->name('print-all-grievances');
    Volt::route('/print/print-selected-grievances/{selected}', 'print-files.print-selected-grievances')->name('print-selected-grievances');
    Volt::route('/print/print-all-feedbacks', 'print-files.print-all-feedbacks')->name('print-all-feedbacks');
    Volt::route('/print/print-selected-feedbacks/{selected}', 'print-files.print-selected-feedbacks')->name('print-selected-feedbacks');
});
