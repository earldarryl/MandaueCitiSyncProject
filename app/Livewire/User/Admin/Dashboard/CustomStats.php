<?php

namespace App\Livewire\User\Admin\Dashboard;

use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use App\Models\User;
use App\Models\Grievance;
use App\Models\Assignment;
use App\Models\Feedback;
use Carbon\Carbon;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Livewire\WithFileUploads;
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

    public $totalAssignments = 0;
    public $assignmentsByDepartment = [];
    public $totalGrievances = 0;
    public $pendingGrievances = 0;
    public $unresolvedGrievances = 0;
    public $inProgressGrievances = 0;
    public $resolvedGrievances = 0;

    public $totalFeedbacks = 0;
    public $citizenFeedbacks = 0;
    public $hrLiaisonFeedbacks = 0;
    protected $listeners = ['dateRangeUpdated' => 'updateDateRange'];
    public $department_profile;
    public $department_background;
    public $newDepartment = [
        'department_name' => '',
        'department_code' => '',
        'department_description' => '',
        'is_active' => '',
        'is_available' => '',
        'department_profile' => null,
        'department_background' => null,
    ];

    public $newLiaison = [
        'name' => '',
        'email' => '',
        'password' => '',
    ];

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        $this->calculateStats();
    }

    public function updateDateRange($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;

        $this->calculateStats();
    }

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('department_profile')
                ->label('Department Profile Image')
                ->image()
                ->directory('departments/profile')
                ->disk('public')
                ->openable()
                ->downloadable()
                ->preserveFilenames()
                ->avatar()
                ->alignCenter(true)
                ->previewable(true)
                ->maxSize(5120)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->helperText('Upload a profile image (JPG, PNG, or WEBP, max 5MB).'),

            FileUpload::make('department_background')
                ->label('Department Background Image')
                ->image()
                ->directory('departments/backgrounds')
                ->disk('public')
                ->openable()
                ->downloadable()
                ->preserveFilenames()
                ->previewable(true)
                ->maxSize(5120)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->helperText('Upload a background image (JPG, PNG, or WEBP, max 5MB).'),
        ];
    }

    public function createHrLiaison()
    {
        $this->validate([
            'newLiaison.name' => 'required|string|max:255',
            'newLiaison.email' => 'required|email|unique:users,email',
            'newLiaison.password' => 'required|string|min:6',
        ]);

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

        $this->calculateStats();
        $this->dispatch('refresh');

        Notification::make()
            ->title('HR Liaison Added')
            ->body("{$user->name} has been successfully added as HR Liaison.")
            ->success()
            ->send();
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
    }
    protected function calculateStats(): void
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $this->totalUsers = User::whereBetween('created_at', [$start, $end])->count();
        $this->citizenUsers = User::whereBetween('created_at', [$start, $end])
            ->whereHas('roles', fn($q) => $q->where('name', 'citizen'))->count();
        $this->hrLiaisonUsers = User::whereBetween('created_at', [$start, $end])
            ->whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))->count();
        $this->onlineUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))->count();

        $this->totalAssignments = Assignment::whereBetween('assigned_at', [$start, $end])->count();

        $departments = Department::all();

        $this->assignmentsByDepartment = $departments->map(function ($dept) use ($start, $end) {
            $count = Assignment::where('department_id', $dept->department_id)
                ->whereBetween('assigned_at', [$start, $end])
                ->count();

            return [
                'department_name' => $dept->department_name,
                'total' => $count,
            ];
        });

        $this->totalGrievances = Grievance::whereBetween('created_at', [$start, $end])->count();

        $this->totalFeedbacks = Feedback::whereBetween('date', [$start, $end])->count();
        $this->citizenFeedbacks = Feedback::whereBetween('date', [$start, $end])
            ->whereHas('user.roles', fn($q) => $q->where('name', 'citizen'))->count();
        $this->hrLiaisonFeedbacks = Feedback::whereBetween('date', [$start, $end])
            ->whereHas('user.roles', fn($q) => $q->where('name', 'hr_liaison'))->count();
    }
}
