<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;

// Dashboard
// No need for a check here unless you're loading from multiple, potentially conflicting sources.
Breadcrumbs::for('dashboard', function (Trail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});

// Profile
Breadcrumbs::for('settings', function (Trail $trail) {
    $trail->push('Settings', route('settings'));
});

// Grievance Form
Breadcrumbs::for('user.citizen.grievance-form', function (Trail $trail) {
    $trail->push('Grievance Form', route('user.citizen.grievance-form'));
});

// Activity Logs
Breadcrumbs::for('user.admin.activity-logs', function (Trail $trail) {
    $trail->push('Activity Logs', route('user.admin.activity-logs'));
});

// Citizens
Breadcrumbs::for('user.admin.users.citizens', function (Trail $trail) {
    $trail->push('Citizens', route('user.admin.users.citizens'));
});

// Password Confirmation
Breadcrumbs::for('password.confirm', function (Trail $trail) {
    $trail->push('Password Confirmation', route('password.confirm'));
});
