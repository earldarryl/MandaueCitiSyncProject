<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;

/*
|--------------------------------------------------------------------------
| Citizen Routes
|--------------------------------------------------------------------------
*/

// Grievance Index
Breadcrumbs::for('citizen.grievance.index', function (Trail $trail) {
    $trail->push('<i class="bi bi-file-earmark-text"></i> Grievances', route('citizen.grievance.index'));
});

// Grievance Create
Breadcrumbs::for('citizen.grievance.create', function (Trail $trail) {
    $trail->parent('citizen.grievance.index');
    $trail->push('<i class="bi bi-plus-circle"></i> Create Grievance', route('citizen.grievance.create'));
});

// Grievance View
Breadcrumbs::for('citizen.grievance.view', function (Trail $trail, $id) {
    $trail->parent('citizen.grievance.index');
    $trail->push('<i class="bi bi-eye"></i> View Grievances', route('citizen.grievance.view', $id));
});

// Grievance Edit
Breadcrumbs::for('citizen.grievance.edit', function (Trail $trail, $id) {
    $trail->parent('citizen.grievance.index');
    $trail->push('<i class="bi bi-pencil-square"></i> Edit Grievance', route('citizen.grievance.edit', $id));
});



/*
|--------------------------------------------------------------------------
| HR Liaison Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Breadcrumbs::for('hr-liaison.dashboard', function (Trail $trail) {
    $trail->push('<i class="bi bi-speedometer2"></i> Dashboard', route('hr-liaison.dashboard'));
});

// Department Index
Breadcrumbs::for('hr-liaison.department.index', function (Trail $trail) {
    $trail->parent('hr-liaison.dashboard');
    $trail->push('<i class="bi bi-people"></i> Departments', route('hr-liaison.department.index'));
});

// Grievance Index
Breadcrumbs::for('hr-liaison.grievance.index', function (Trail $trail) {
    $trail->parent('hr-liaison.dashboard');
    $trail->push('<i class="bi bi-archive-fill"></i> Grievance Repository', route('hr-liaison.grievance.index'));
});

// Grievance View
Breadcrumbs::for('hr-liaison.grievance.view', function (Trail $trail, $id) {
    $trail->parent('hr-liaison.grievance.index');
    $trail->push('<i class="bi bi-eye"></i> View Grievance', route('hr-liaison.grievance.view', $id));
});

// Activity Logs
Breadcrumbs::for('hr-liaison.activity-logs.index', function (Trail $trail) {
    $trail->parent('hr-liaison.dashboard');
    $trail->push('<i class="bi bi-clock-history"></i> Activity Logs', route('hr-liaison.activity-logs.index'));
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Breadcrumbs::for('dashboard', function (Trail $trail) {
    $trail->push('<i class="bi bi-speedometer2"></i> Dashboard', route('dashboard'));
});

// Activity Logs
Breadcrumbs::for('activity-logs.index', function (Trail $trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="bi bi-list-check"></i> Activity Logs', route('activity-logs.index'));
});

// Citizens
Breadcrumbs::for('users.citizens', function (Trail $trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="bi bi-people"></i> Citizens', route('users.citizens'));
});

// HR Liaisons
Breadcrumbs::for('users.hr-liaisons', function (Trail $trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="bi bi-person-badge"></i> HR Liaisons', route('users.hr-liaisons'));
});


/*
|--------------------------------------------------------------------------
| General Routes
|--------------------------------------------------------------------------
*/

// Settings (root)
Breadcrumbs::for('settings', function (Trail $trail) {
    $trail->push('<i class="bi bi-gear"></i> Settings', route('settings'));
});

// Settings > Profile
Breadcrumbs::for('settings.profile', function (Trail $trail) {
    $trail->parent('settings');
    $trail->push('<i class="bi bi-person-circle"></i> Profile', route('settings.profile'));
});

// Settings > Appearance
Breadcrumbs::for('settings.appearance', function (Trail $trail) {
    $trail->parent('settings');
    $trail->push('<i class="bi bi-palette"></i> Appearance', route('settings.appearance'));
});

// Settings > Two Factor Auth
Breadcrumbs::for('settings.two-factor-auth', function (Trail $trail) {
    $trail->parent('settings');
    $trail->push('<i class="bi bi-shield-lock"></i> Two Factor Auth', route('settings.two-factor-auth'));
});

// Password Confirmation
Breadcrumbs::for('password.confirm', function (Trail $trail) {
    $trail->push('<i class="bi bi-key"></i> Password Confirmation', route('password.confirm'));
});

// Sidebar (optional)
Breadcrumbs::for('sidebar', function (Trail $trail) {
    $trail->push('<i class="bi bi-layout-sidebar-inset"></i> Sidebar', route('sidebar'));
});
