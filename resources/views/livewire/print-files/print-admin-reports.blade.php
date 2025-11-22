<div class="page w-full relative p-5 print:mx-0 print:p-5">
    <!-- Header -->
    <div class="header flex justify-center items-center border-b-4 border-blue-700 pb-2 mb-4">
        <div class="header-left flex items-center gap-3 border-r-2 border-gray-800 pr-3">
            <img src="{{ asset('images/mandaue-logo.png') }}" alt="Mandaue Logo" class="w-16 h-16 rounded-full object-cover bg-white">
        </div>
        <div class="header-right flex flex-col justify-center items-center text-center ml-3">
            <span class="text-sm text-black">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</span>
            @if($filterType)
                <span class="text-2xl font-light uppercase text-black">{{ $filterType }}</span>
            @endif
        </div>
    </div>

    <!-- Date Summary -->
    @php
        $start = $startDate ? \Carbon\Carbon::parse($startDate) : \Carbon\Carbon::now();
        $end = $endDate ? \Carbon\Carbon::parse($endDate) : \Carbon\Carbon::now();
    @endphp
    <div class="summary-date text-center font-semibold mt-5 mb-3 text-sm">
        {{ $start->format('F d, Y') }}
        @if($startDate !== $endDate)
            – {{ $end->format('F d, Y') }}
        @endif
    </div>

    <!-- Stats Grid -->
    @if(!empty($stats))
        <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-5 mb-5">
            @foreach($stats as $stat)
                <div class="stat-card flex flex-col items-center justify-center p-4 rounded-xl border border-gray-300 page-break-inside-avoid"
                     style="background-color: {{ $stat->bg ?? '#f3f4f6' }}; color: {{ $stat->text ?? '#374151' }}">
                    <h3 class="text-sm font-semibold text-center">{{ $stat->label ?? $stat->department_name ?? $stat->grievance_type ?? 'N/A' }}</h3>
                    <p class="text-2xl font-bold">{{ $stat->total ?? 0 }}</p>
                    @if(isset($stat->total_online_time))
                        <span class="text-gray-500 text-base mt-1">Total Online: {{ $stat->total_online_time }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-300 bg-white">
        <table class="w-full text-xs border-collapse text-gray-800">
            <thead class="bg-gray-100 uppercase font-semibold text-gray-700 text-xs">
                <tr>
                    @if($filterType === 'Grievances')
                        <th class="px-3 py-2 border">TICKET ID</th>
                        <th class="px-3 py-2 border">TITLE</th>
                        <th class="px-3 py-2 border">TYPE</th>
                        <th class="px-3 py-2 border">CATEGORY</th>
                        <th class="px-3 py-2 border">DEPARTMENT</th>
                        <th class="px-3 py-2 border">PRIORITY</th>
                        <th class="px-3 py-2 border">STATUS</th>
                        <th class="px-3 py-2 border">PROCESSING DAYS</th>
                        <th class="px-3 py-2 border">DATE</th>
                    @elseif($filterType === 'Departments')
                        <th class="px-3 py-2 border">DEPARTMENT NAME</th>
                        <th class="px-3 py-2 border">CODE</th>
                        <th class="px-3 py-2 border">TOTAL ASSIGNMENTS</th>
                        <th class="px-3 py-2 border">HR LIAISONS ONLINE</th>
                        <th class="px-3 py-2 border">CREATED AT</th>
                    @elseif($filterType === 'Feedbacks')
                        <th class="px-3 py-2 border">EMAIL</th>
                        <th class="px-3 py-2 border">SERVICE</th>
                        <th class="px-3 py-2 border">GENDER</th>
                        <th class="px-3 py-2 border">REGION</th>
                        <th class="px-3 py-2 border">CC SUMMARY</th>
                        <th class="px-3 py-2 border">SQD SUMMARY</th>
                        <th class="px-3 py-2 border">SUGGESTIONS</th>
                        <th class="px-3 py-2 border">DATE</th>
                    @elseif($filterType === 'Users' && $filterUserType === 'Citizen')
                        <th class="px-3 py-2 border">FIRST NAME</th>
                        <th class="px-3 py-2 border">MIDDLE NAME</th>
                        <th class="px-3 py-2 border">LAST NAME</th>
                        <th class="px-3 py-2 border">SUFFIX</th>
                        <th class="px-3 py-2 border">GENDER</th>
                        <th class="px-3 py-2 border">CIVIL STATUS</th>
                        <th class="px-3 py-2 border">BARANGAY</th>
                        <th class="px-3 py-2 border">SITIO</th>
                        <th class="px-3 py-2 border">BIRTHDATE</th>
                        <th class="px-3 py-2 border">AGE</th>
                        <th class="px-3 py-2 border">PHONE</th>
                        <th class="px-3 py-2 border">EMERGENCY NAME</th>
                        <th class="px-3 py-2 border">EMERGENCY NUMBER</th>
                        <th class="px-3 py-2 border">RELATIONSHIP</th>
                        <th class="px-3 py-2 border">EMAIL</th>
                        <th class="px-3 py-2 border">CREATED AT</th>
                    @elseif($filterType === 'Users' && $filterUserType === 'HR Liaison')
                        <th class="px-3 py-2 border">NAME</th>
                        <th class="px-3 py-2 border">EMAIL</th>
                        <th class="px-3 py-2 border">DEPARTMENT</th>
                        <th class="px-3 py-2 border">STATUS</th>
                        <th class="px-3 py-2 border">CREATED AT</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($data as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        @if($filterType === 'Grievances')
                            <td class="px-2 py-1 border text-center">{{ $item->grievance_ticket_id }}</td>
                            <td class="px-2 py-1 border">{{ ucwords($item->grievance_title) }}</td>
                            <td class="px-2 py-1 border text-center">{{ $item->grievance_type ?? '—' }}</td>
                            <td class="px-2 py-1 border text-center">{{ $item->grievance_category ?? '—' }}</td>
                            <td class="px-2 py-1 border text-center">
                                {{ collect($item->departments)->join(', ') ?? '—' }}
                            </td>
                            <td class="px-2 py-1 border text-center">{{ $item->priority_level ?? '—' }}</td>
                            <td class="px-2 py-1 border text-center">{{ ucwords($item->grievance_status) ?? '—' }}</td>
                            <td class="px-2 py-1 border text-center">{{ $item->processing_days ?? '—' }}</td>
                            <td class="px-2 py-1 border text-center">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d h:i A') }}
                            </td>
                        @elseif($filterType === 'Departments')
                            <td class="px-2 py-1 border text-center">{{ $item->department_name }}</td>
                            <td class="px-2 py-1 border text-center">{{ $item->department_code }}</td>
                            <td class="px-2 py-1 border text-center">{{ $item->assignments_count ?? 0 }}</td>
                            <td class="px-2 py-1 border text-center">{{ $item->hrLiaisonsStatus ?? '—' }}</td>
                            <td class="px-2 py-1 border text-center">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d h:i A') }}
                            </td>
                        @elseif($filterType === 'Feedbacks')
                            <td class="px-2 py-1 border">{{ $item->email ?? 'N/A' }}</td>
                            <td class="px-2 py-1 border">{{ $item->service }}</td>
                            <td class="px-2 py-1 border text-center">{{ $item->gender }}</td>
                            <td class="px-2 py-1 border text-center">{{ $item->region }}</td>
                            <td class="px-2 py-1 border font-bold text-center">{{ $item->cc_summary }}</td>
                            <td class="px-2 py-1 border font-bold text-center">{{ $item->sqd_summary }}</td>
                            <td class="px-2 py-1 border">{{ $item->suggestions }}</td>
                            <td class="px-2 py-1 border text-center">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d h:i A') }}
                            </td>
                        @elseif($filterType === 'Users' && $filterUserType === 'Citizen')
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->first_name ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->middle_name ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->last_name ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->suffix ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->gender ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->civil_status ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->barangay ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->sitio ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional($item->userInfo)->birthdate ?? '—' }}</td>
                            <td class="px-2 py-1 border text-center">{{ optional(optional($item)->userInfo)->age ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->phone_number ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->emergency_contact_name ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->emergency_contact_number ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ optional(optional($item)->userInfo)->emergency_relationship ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ $item->email ?? '—' }}</td>
                            <td class="px-2 py-1 border text-center">{{ optional($item->created_at)->format('Y-m-d h:i A') ?? '—' }}</td>

                        @elseif($filterType === 'Users' && $filterUserType === 'HR Liaison')
                            <td class="px-2 py-1 border">{{ $item->name ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ $item->email ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ $item->departments ?? '—' }}</td>
                            <td class="px-2 py-1 border">{{ $item->status ?? '—' }}</td>
                            <td class="px-2 py-1 border text-center">{{ optional($item->created_at)->format('Y-m-d h:i A') ?? '—' }}</td>
                        @endif

                    </tr>
                @empty
                    <tr>
                        <td colspan="16" class="text-center italic text-gray-500 py-3">No data available for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    <!-- Noted Section -->
    <div class="noted flex gap-3 mt-10 ml-5">
        <div class="noted-text text-base">Noted:</div>
        <div class="name-with-role flex flex-col items-center gap-1">
            <div class="name font-semibold border-b-2 border-gray-800 px-3">{{ $adminName ?? 'N/A' }}</div>
            <div class="position text-gray-500 text-sm font-medium text-center">Admin</div>
        </div>
    </div>
</div>
