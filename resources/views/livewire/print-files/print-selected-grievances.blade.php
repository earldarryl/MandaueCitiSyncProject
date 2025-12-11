<div class="page w-full relative p-5 print:mx-0 print:p-5">
    @php
        $grievanceDepartments = $grievances
            ->pluck('departments')
            ->flatten()
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->unique('department_id');

        $departmentCount = $grievanceDepartments->count();

        if ($departmentCount === 0) {
            $departmentName = 'Administration';
            $departmentProfile = null;
        } elseif ($departmentCount === 1) {
            $departmentName = $grievanceDepartments->first()->department_name;
            $departmentProfile = $grievanceDepartments->first()->department_profile;
        } else {
            $departmentName = "{$departmentCount} Departments";
            $departmentProfile = null;
        }

        $palette = ['0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899','14B8A6','6366F1','F97316','84CC16'];
        $index = crc32($departmentName) % count($palette);
        $bgColor = $palette[$index];

        if ($departmentProfile) {
            $departmentLogo = Storage::url($departmentProfile);
        } else {
            $departmentLogo = 'https://ui-avatars.com/api/?name=' . urlencode($departmentName) . '&background=' . $bgColor . '&color=fff&size=128';
        }
    @endphp
    <div class="header flex justify-center items-center border-b-4 border-blue-700 pb-2 mb-4">
        <div class="header-left flex items-center gap-3 border-r-2 border-gray-800 pr-3">
            <img src="{{ asset('images/mandaue-logo.png') }}" alt="Mandaue Logo"
                 class="w-16 h-16 rounded-full object-cover bg-white">
            <img src="{{ $departmentLogo }}" alt="Department Logo" class="w-16 h-16 rounded-full object-cover bg-white">
        </div>
        <div class="header-right flex flex-col justify-center items-center text-center ml-3">
            <span class="text-sm text-black">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</span>
            <span class="text-2xl font-light uppercase text-black">{{ strtoupper($departmentName) }}</span>
        </div>
    </div>

    <div class="summary-date text-center font-semibold mt-5 mb-3 text-sm">
        {{ now()->format('F d, Y') }}
    </div>

    <div class="text-center font-semibold mt-5 mb-3 text-2xl">
        LIST OF REPORTS
    </div>

    <div class="text-center font-bold mb-2">
        Total Reports: {{ $grievances->count() }}
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-300 bg-white">
        <table class="w-full text-xs border-collapse text-gray-800">
            <thead class="bg-gray-100 uppercase font-semibold text-gray-700 text-xs">
                <tr>
                    <th class="px-3 py-2 border">TICKET ID</th>
                    <th class="px-3 py-2 border">TITLE</th>
                    <th class="px-3 py-2 border">TYPE</th>
                    <th class="px-3 py-2 border">CATEGORY</th>
                    <th class="px-3 py-2 border">PRIORITY</th>
                    <th class="px-3 py-2 border">STATUS</th>
                    <th class="px-3 py-2 border">DATE FILED</th>
                    <th class="px-3 py-2 border">SUBMITTED BY</th>
                    <th class="px-3 py-2 border">DETAILS</th>
                    <th class="px-3 py-2 border">ATTACHMENTS</th>
                    <th class="px-3 py-2 border">REMARKS</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                @forelse ($grievances as $index => $grievance)
                    @php
                        $rawRemarks = $grievance->grievance_remarks ?? [];
                        $remarks = is_array($rawRemarks) ? $rawRemarks : json_decode($rawRemarks, true);

                        $submittedBy = $grievance->is_anonymous
                            ? 'Anonymous'
                            : ($grievance->user
                                ? ($grievance->user->info
                                    ? "{$grievance->user->info->first_name} {$grievance->user->info->last_name}"
                                    : $grievance->user->name)
                                : '—');
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-3 py-2 border text-center font-bold">
                            {{ $grievance->grievance_ticket_id }}
                        </td>

                        <td class="px-3 py-2 border">
                            {{ $grievance->grievance_title }}
                        </td>

                        <td class="px-3 py-2 border text-center">
                            {{ $grievance->grievance_type }}
                        </td>

                        <td class="px-3 py-2 border text-center">
                            {{ ucfirst($grievance->grievance_category) }}
                        </td>

                        <td class="px-3 py-2 border text-center">
                            {{ ucfirst($grievance->priority_level) }}
                        </td>

                        <td class="px-3 py-2 border text-center">
                            {{ ucwords(str_replace('_', ' ', $grievance->grievance_status ?? '—')) }}
                        </td>

                        <td class="px-3 py-2 border text-center">
                            {{ $grievance->created_at->format('Y-m-d h:i A') }}
                        </td>

                        <td class="px-3 py-2 border text-center">
                            {{ $submittedBy }}
                        </td>

                        <td class="px-3 py-2 border">
                            {!! Str::limit(strip_tags($grievance->grievance_details), 120, '...') !!}
                        </td>

                        <td class="px-3 py-2 border text-center">
                            @if ($grievance->attachments && $grievance->attachments->count() > 0)
                                <span class="text-blue-700 font-semibold">
                                    {{ $grievance->attachments->count() }} file(s)
                                </span>
                            @else
                                <span class="text-gray-500">None</span>
                            @endif
                        </td>

                        <td class="px-3 py-2 border text-left align-top grievance-remark-td">
                            @if (!empty($remarks))
                                <div class="space-y-1">
                                    @foreach ($remarks as $remark)
                                        <div>
                                            <strong>[{{ date('Y-m-d H:i', strtotime($remark['timestamp'])) }}]</strong>
                                            {{ $remark['user_name'] ?? '—' }}
                                            ({{ $remark['role'] ?? '—' }}):
                                            {{ $remark['message'] ?? '' }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center italic text-gray-500 py-3">
                            No grievances available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="status-footer">
        <div class="footer-row">
            <div class="footer-label">Noted by:</div>
            <div class="footer-value">
                @if(isset($hr_liaison))
                    {{ $hr_liaison->name }}
                @elseif(isset($admin))
                    {{ $admin->name }}
                @else
                    N/A
                @endif
            </div>
        </div>
        <div class="footer-subtext">
            @if(isset($hr_liaison))
                HR Liaison
            @elseif(isset($admin))
                Admin
            @else
                —
            @endif
        </div>
    </div>

</div>
