<?php

namespace App\Livewire\User\Citizen\Grievance;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Grievance;
use App\Models\GrievanceAttachment;
use App\Models\Department;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Grievance')]
class Create extends Component
{
    use WithFileUploads;

    public $grievance_type;
    public $grievance_title;
    public $grievance_details;
    public $departmentList = [];
    public $department = [];
    public $grievance_files = [];

    public function mount()
    {
        $this->departmentList = Department::pluck('department_name', 'department_id')->toArray();
    }

    protected $rules = [
        'grievance_type'    => 'required|string|in:Complaints,Inquiry,Request,Suggestion/Recommendation/Feedback',
        'grievance_title'   => 'required|string|regex:/^[A-Za-z\s]+$/',
        'grievance_details' => 'required|string|regex:/^[A-Za-z\s]+$/',
        'department'        => 'required|array|min:1',
        'department.*'      => 'integer|exists:departments,department_id',
        'grievance_files'   => 'array|max:5',
        'grievance_files.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
    ];

    protected $messages = [
        'grievance_type.required' => 'Please choose a grievance type.',
        'grievance_type.in'       => 'Invalid grievance type selected.',
        'grievance_title.required' => 'Please provide a grievance title.',
        'grievance_title.regex'    => 'The grievance title may only contain letters and spaces.',
        'grievance_details.required' => 'Please provide grievance details.',
        'grievance_details.regex'    => 'The grievance details may only contain letters and spaces.',
        'department.required' => 'Please choose at least one department.',
        'department.*.exists' => 'One or more selected departments are invalid.',
        'grievance_files.array'    => 'The grievance attachments must be in an array.',
        'grievance_files.max'      => 'You may not upload more than 5 files.',
        'grievance_files.*.file'   => 'Each attachment must be a valid file.',
        'grievance_files.*.mimes'  => 'Attachments must be PDF, JPG, JPEG, PNG, DOC, or DOCX files.',
        'grievance_files.*.max'    => 'Each attachment must not exceed 5MB.',
        'grievance_files.uploaded' => 'The grievance files failed to upload. Please try again.',
    ];

    public function submit()
    {
        $this->validate();

        // Save grievance
        $grievance = Grievance::create([
            'user_id'          => auth()->id(),
            'grievance_type'   => $this->grievance_type,
            'grievance_title'  => $this->grievance_title,
            'grievance_details'=> $this->grievance_details,
            'category'         => 'General',
            'grievance_status' => 'pending',
            'processing_days'  => 0,
        ]);

        if ($this->grievance_files) {
            foreach ($this->grievance_files as $file) {
                $path = $file->store('grievance_files', 'public');

                GrievanceAttachment::create([
                    'grievance_id' => $grievance->grievance_id,
                    'file_path'    => $path,
                    'file_name'    => $file->getClientOriginalName(),
                ]);
            }
        }

        foreach ($this->department as $deptId) {

            $hrLiaisons = User::whereHas('roles', function ($q) {
                $q->where('name', 'hr_liaison');
            })
            ->whereHas('departments', function ($q) use ($deptId) {
                $q->where('hr_liaison_department.department_id', $deptId);
            })
            ->get();

            foreach ($hrLiaisons as $hr) {
                Assignment::create([
                    'grievance_id'  => $grievance->grievance_id,
                    'department_id' => $deptId,
                    'assigned_at'   => now(),
                    'hr_liaison_id' => $hr->id,
                ]);
            }
        }

        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset([
            'grievance_type',
            'grievance_title',
            'grievance_details',
            'department',
            'grievance_files',
        ]);

        $this->dispatch('toast', message: 'Grievance submitted successfully', type: 'success');
        $this->dispatch('form-submitted');
        session()->flash('success', 'Grievance submitted with attachments and assigned to HR liaisons!');
    }


    public function render()
    {
        return view('livewire.user.citizen.grievance.create');
    }
}
