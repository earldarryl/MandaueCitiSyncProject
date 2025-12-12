<?php

namespace App\Livewire\User\Admin\Dashboard;

use App\Notifications\GeneralNotification;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use App\Models\User;
use App\Models\Grievance;
use App\Models\Assignment;
use App\Models\Feedback;
use App\Models\ActivityLog;
use Carbon\Carbon;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Cache;
class CustomStats extends Widget implements Forms\Contracts\HasForms
{
    use WithFileUploads, InteractsWithForms, InteractsWithActions;
    protected string $view = 'livewire.user.admin.dashboard.custom-stats';

    public $startDate;
    public $endDate;

    public $totalUsers = 0;
    public $citizenUsers = 0;
    public $hrLiaisonUsers = 0;
    public $onlineUsers = 0;
    public $citizenOnline = 0;
    public $hrLiaisonOnline = 0;

    public $totalAssignments = 0;
    public $assignmentsByDepartment = [];
    public $totalGrievances = 0;
    public $pendingGrievances = 0;
    public $unresolvedGrievances = 0;
    public $inProgressGrievances = 0;
    public $resolvedGrievances = 0;

    public $totalFeedbacks = 0;
    public $citizenFeedbacks = 0;
    protected $listeners = ['dateRangeUpdated' => 'updateDateRange', 'refresh' => '$refresh'];
    public $department_profile;
    public $department_background;
    public $newDepartment = [
        'department_name' => '',
        'department_code' => '',
        'department_description' => '',
        'is_active' => '',
        'is_available' => '',
    ];


    public $newLiaison = [
        'name' => '',
        'email' => '',
        'password' => '',
    ];

    public function resetFields(): void
    {
        $this->newDepartment = [
            'department_name' => '',
            'department_code' => '',
            'department_description' => '',
            'is_active' => '',
            'is_available' => '',
        ];

        $this->newLiaison = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];

        $this->edit_department_profile = null;
        $this->edit_department_background = null;

        $this->profilePreview = null;
        $this->backgroundPreview = null;

        $this->resetErrorBag();
    }

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        $this->calculateStats();

        $this->form->fill([
            'department_profile' => $this->department_profile,
            'department_background' => $this->department_background,
        ]);
    }

    public function updateDateRange($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;

        $this->calculateStats();
    }

    public function confirmDeleteAllActivityLogs()
    {
        ActivityLog::query()->delete();

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Activity Logs Deleted',
            'message' => 'All activity logs have been successfully deleted.',
        ]);

        $this->dispatch('refresh');
        $this->dispatch('close-all-modals');
    }

    public function createHrLiaison()
    {
        $this->validate(
    [
                'newLiaison.name'     => 'required|string|max:255',
                'newLiaison.email'    => 'required|email|unique:users,email',
                'newLiaison.password' => 'required|string|min:6',
            ],
            [
                'newLiaison.name.required'     => 'Please enter the name of the HR liaison.',
                'newLiaison.name.string'       => 'Name must be a valid text.',
                'newLiaison.name.max'          => 'Name cannot exceed 255 characters.',

                'newLiaison.email.required'    => 'Please enter an email address.',
                'newLiaison.email.email'       => 'Please provide a valid email address.',
                'newLiaison.email.unique'      => 'This email is already registered. Please choose another one.',

                'newLiaison.password.required' => 'Please enter a password.',
                'newLiaison.password.string'   => 'Password must be a valid string.',
                'newLiaison.password.min'      => 'Password must be at least 6 characters long.',
            ]
        );

        $creator = auth()->user();

        $user = new User([
            'name' => $this->newLiaison['name'],
            'email' => $this->newLiaison['email'],
            'password' => $this->newLiaison['password'],
        ]);

        $user->forceFill([
            'email_verified_at' => $user->freshTimestamp(),
        ])->save();

        $user->assignRole('hr_liaison');

        $this->newLiaison = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];

        $this->dispatch('refresh');
        $this->dispatch('close-all-modals');
        $this->resetFields();

        $user->notify(new GeneralNotification(
            'Welcome to the HR Liaison Team',
            "You have been registered as an HR Liaison in the system.",
            'success',
            [],
            ['type' => 'success'],
            true,
            [[
                'label' => 'Go to Dashboard',
                'url' => route('hr-liaison.dashboard'),
                'open_new_tab' => false,
            ]]
        ));

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'New HR Liaison Added',
                "{$user->name} has been added as an HR Liaison.",
                'info',
                [],
                ['type' => 'info'],
                true,
                [[
                'label' => 'View Departments & HR Liaisons',
                'url' => route('admin.stakeholders.departments-and-hr-liaisons.index'),
                'open_new_tab' => false,
                ]]
            ));
        }

        $creator->notify(new GeneralNotification(
            'HR Liaison Created Successfully',
            "You added {$user->name} as a new HR Liaison.",
            'success',
            [],
            ['type' => 'success'],
            true,
            [[
                'label' => 'View Departments & HR Liaisons',
                'url' => route('admin.stakeholders.departments-and-hr-liaisons.index'),
                'open_new_tab' => false,
            ]]
        ));

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'role_id'     => auth()->user()->roles->first()?->id,
            'module'      => 'HR Liaisons',
            'action'      => 'Create',
            'action_type' => 'create',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => "Created new HR Liaison named '{$user->name}'",
            'changes'     => $user->toArray(),
            'status'      => 'success',
            'ip_address'  => request()->ip(),
            'device_info' => request()->header('device') ?? null,
            'user_agent'  => request()->userAgent(),
            'platform'    => php_uname('s'),
            'location'    => null,
            'timestamp'   => now(),
        ]);
    }

    public function createDepartment()
    {
        $this->validate([
            'newDepartment.department_name' => 'required|string|max:255|unique:departments,department_name',
            'newDepartment.department_code' => 'required|string|max:50|unique:departments,department_code',
            'newDepartment.department_description' => 'nullable|string|max:1000',
            'newDepartment.is_active' => 'required',
            'newDepartment.is_available' => 'required',
        ]);

        $state = $this->form->getState();
        $department_profile = $state['department_profile'] ?? null;
        $department_background = $state['department_background'] ?? null;

        $isActiveValue = strtolower($this->newDepartment['is_active']) === 'active' ? 1 : 0;
        $isAvailableValue = strtolower($this->newDepartment['is_available']) === 'yes' ? 1 : 0;

        $department = Department::create([
            'department_name' => $this->newDepartment['department_name'],
            'department_code' => $this->newDepartment['department_code'],
            'department_description' => $this->newDepartment['department_description'],
            'is_active' => $isActiveValue,
            'is_available' => $isAvailableValue,
            'department_profile' => $department_profile,
            'department_bg' => $department_background,
        ]);

        $this->newDepartment = [
            'department_name' => '',
            'department_code' => '',
            'department_description' => '',
            'is_active' => '',
            'is_available' => '',
            'department_profile' => null,
            'department_background' => null,
        ];

        $this->form->fill([
            'department_profile' => null,
            'department_background' => null,
        ]);

        $this->calculateStats();
        $this->dispatch('refresh');

        Notification::make()
            ->title('Department Created')
            ->body("The department <b>{$department->department_name}</b> has been successfully created.")
            ->success()
            ->duration(4000)
            ->send();

        $sender = auth()->user();

        $hrLiaisons = User::role('hr_liaison')->get();
        foreach ($hrLiaisons as $hr) {
            $hr->notify(new GeneralNotification(
                'New Department Created',
                "The department <b>{$department->department_name}</b> has been added.",
                'info',
                ['department_id' => $department->department_id],
                [],
                true,
                [
                    [
                        'label' => 'View Departments',
                        'url' => route('hr-liaison.department.index'),
                        'open_new_tab' => true,
                    ]
                ]
            ));
        }

        $sender?->notify(new GeneralNotification(
            'Department Created Successfully',
            "The department <b>{$department->department_name}</b> has been created.",
            'success',
            ['department_id' => $department->department_id],
            [],
            true,
        [
                    'label' => 'View Departments',
                    'url' => route('admin.stakeholders.departments-and-hr-liaisons.index'),
                    'open_new_tab' => true,
                ]
        ));
    }
    protected function calculateStats(): void
    {
        $cacheKey = 'custom_stats_' . $this->startDate . '_' . $this->endDate;

        $cachedStats = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
            $now = now();

            $users = User::with('roles')
                ->whereBetween('created_at', [$start, $end])
                ->get();

            $totalUsers = $users->count();
            $citizenUsers = $users->filter(fn($u) => $u->hasRole('citizen'))->count();
            $hrLiaisonUsers = $users->filter(fn($u) => $u->hasRole('hr_liaison'))->count();

            $onlineThreshold = $now->subMinutes(5);
            $onlineUsers = User::with('roles')
                ->whereNotNull('last_seen_at')
                ->where('last_seen_at', '>=', $onlineThreshold)
                ->get();

            $onlineCount = $onlineUsers->count();
            $citizenOnline = $onlineUsers->filter(fn($u) => $u->hasRole('citizen'))->count();
            $hrLiaisonOnline = $onlineUsers->filter(fn($u) => $u->hasRole('hr_liaison'))->count();

            $assignments = Assignment::whereBetween('assigned_at', [$start, $end])->get();
            $totalAssignments = $assignments->count();

            $departments = Department::all();
            $assignmentsByDepartment = $departments->map(fn($dept) => [
                'department_name' => $dept->department_name,
                'total' => $assignments->where('department_id', $dept->department_id)->count(),
            ]);

            $grievances = Grievance::whereBetween('created_at', [$start, $end])->get();
            $totalGrievances = $grievances->count();
            $pendingGrievances = $grievances->where('grievance_status', 'pending')->count();
            $inProgressGrievances = $grievances->where('grievance_status', 'in_progress')->count();
            $resolvedGrievances = $grievances->where('grievance_status', 'resolved')->count();
            $unresolvedGrievances = $grievances->where('grievance_status', 'unresolved')->count();

            $feedbacks = Feedback::with('user.roles')->whereBetween('date', [$start, $end])->get();
            $totalFeedbacks = $feedbacks->count();
            $citizenFeedbacks = $feedbacks->filter(fn($f) => $f->user->hasRole('citizen'))->count();

            return compact(
                'totalUsers', 'citizenUsers', 'hrLiaisonUsers', 'onlineCount',
                'citizenOnline', 'hrLiaisonOnline', 'totalAssignments',
                'assignmentsByDepartment', 'totalGrievances', 'pendingGrievances',
                'inProgressGrievances', 'resolvedGrievances', 'unresolvedGrievances',
                'totalFeedbacks', 'citizenFeedbacks'
            );
        });

        $this->totalUsers = $cachedStats['totalUsers'];
        $this->citizenUsers = $cachedStats['citizenUsers'];
        $this->hrLiaisonUsers = $cachedStats['hrLiaisonUsers'];
        $this->onlineUsers = $cachedStats['onlineCount'];
        $this->citizenOnline = $cachedStats['citizenOnline'];
        $this->hrLiaisonOnline = $cachedStats['hrLiaisonOnline'];
        $this->totalAssignments = $cachedStats['totalAssignments'];
        $this->assignmentsByDepartment = $cachedStats['assignmentsByDepartment'];
        $this->totalGrievances = $cachedStats['totalGrievances'];
        $this->pendingGrievances = $cachedStats['pendingGrievances'];
        $this->inProgressGrievances = $cachedStats['inProgressGrievances'];
        $this->resolvedGrievances = $cachedStats['resolvedGrievances'];
        $this->unresolvedGrievances = $cachedStats['unresolvedGrievances'];
        $this->totalFeedbacks = $cachedStats['totalFeedbacks'];
        $this->citizenFeedbacks = $cachedStats['citizenFeedbacks'];
    }

}
