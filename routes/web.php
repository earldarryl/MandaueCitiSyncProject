<?php
use App\Http\Controllers\UserController;
use App\Livewire\Dashboard;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Controllers\SmsController;

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function(){

    Volt::route('/dashboard', Dashboard::class)->name('dashboard');
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

