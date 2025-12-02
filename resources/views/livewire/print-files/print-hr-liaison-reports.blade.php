<div class="w-full max-w-8xl mx-auto p-6 bg-white font-sans text-gray-800">
    <div class="flex justify-center items-center border-b-4 border-blue-800 pb-3 mb-6">
        <div class="flex items-end justify-end gap-3 border-r-2 border-gray-800 w-1/3 pr-3">
            <img src="{{ asset('images/mandaue-logo.png') }}" alt="Mandaue Logo" class="w-16 h-16 rounded-full object-cover bg-white">
            @php
                $department = $user->departments->first();
                $departmentName = $department->department_name ?? 'N/A';
                $departmentProfile = $department->department_profile ?? null;

                $palette = ['0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899','14B8A6','6366F1','F97316','84CC16'];
                $index = crc32($departmentName) % count($palette);
                $bgColor = $palette[$index];

                $departmentLogo = $departmentProfile
                    ? Storage::url($departmentProfile)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($departmentName) . '&background=' . $bgColor . '&color=fff&size=128';
            @endphp
            <img src="{{ $departmentLogo }}" alt="Department Logo" class="w-16 h-16 rounded-full object-cover">
        </div>
       <div class="flex-1 flex flex-col justify-center w-full">
            <p class="text-sm text-center text-black">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</p>
            <p class="text-2xl text-center font-light text-black">{{ strtoupper($departmentName) }}</p>
        </div>
    </div>

    <div class="text-center mb-4 border-b-2 border-gray-200 pb-2">
        <h2 class="text-lg font-medium text-gray-600">{{ $dynamicTitle }}</h2>
    </div>

    <div class="text-center font-semibold mb-6 text-sm">
        {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}
        @if($startDate !== $endDate) – {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }} @endif
    </div>

    <div class="text-center font-bold mb-2">
        Total Reports: {{ $data->count() }}
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        @php
            $total = $statuses->sum();
            function formatNumber($number) {
                if ($number >= 1000000) return round($number / 1000000, 1) . 'M';
                elseif ($number >= 1000) return round($number / 1000, 1) . 'K';
                return $number;
            }
        @endphp
        @foreach($statuses as $label => $count)
            @php
                $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                switch ($label) {
                    case 'Pending':
                        $bgColor = 'bg-yellow-100';
                        $textColor = 'text-yellow-800';
                        break;
                    case 'Overdue':
                        $bgColor = 'bg-red-100';
                        $textColor = 'text-red-800';
                        break;
                    case 'Resolved':
                        $bgColor = 'bg-green-100';
                        $textColor = 'text-green-800';
                        break;
                    default:
                        $bgColor = 'bg-gray-100';
                        $textColor = 'text-gray-800';
                }
            @endphp
            <div class="rounded-lg p-4 shadow hover:shadow-md {{ $bgColor }} {{ $textColor }} text-center">
                <div class="text-xl font-bold">{{ formatNumber($count) }} ({{ $percentage }}%)</div>
                <div class="mt-1">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <div class="overflow-x-auto bg-white w-full border border-gray-300 rounded-md shadow-sm mb-6">
        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-gray-100 text-gray-700 uppercase font-semibold">
                <tr>
                    <th class="border px-3 py-2 text-center">TICKET ID</th>
                    <th class="border px-3 py-2">TITLE</th>
                    <th class="border px-3 py-2 text-center">TYPE</th>
                    <th class="border px-3 py-2 text-center">CATEGORY</th>
                    <th class="border px-3 py-2 text-center">STATUS</th>
                    <th class="border px-3 py-2 text-center">PRIORITY LEVEL</th>
                    <th class="border px-3 py-2 text-center">PROCESSING DAYS</th>
                    <th class="border px-3 py-2 text-center">DATE</th>
                    <th class="border px-3 py-2 text-center">SUBMITTED BY</th>
                    <th class="border px-3 py-2 text-center">DETAILS</th>
                    <th class="border px-3 py-2 text-center">REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                    @php
                        $submittedBy = $item->is_anonymous
                            ? 'Anonymous'
                            : ($item->user
                                ? ($item->user->info
                                    ? "{$item->user->info->first_name} {$item->user->info->last_name}"
                                    : $item->user->name)
                                : '—');

                        $rawRemarks = $item->grievance_remarks ?? [];
                        $remarks = is_array($rawRemarks) ? $rawRemarks : json_decode($rawRemarks, true);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="border px-2 py-1 text-center">{{ $item->grievance_ticket_id }}</td>
                        <td class="border px-2 py-1">{{ $item->grievance_title }}</td>
                        <td class="border px-2 py-1 text-center capitalize">{{ $item->grievance_type ?? '—' }}</td>
                        <td class="border px-2 py-1 text-center capitalize">{{ $item->grievance_category ?? '—' }}</td>
                        <td class="border px-2 py-1 text-center uppercase">{{ strtoupper($item->grievance_status) }}</td>
                        <td class="border px-2 py-1 text-center uppercase">{{ strtoupper($item->priority_level) }}</td>
                        <td class="border px-2 py-1 text-center">{{ $item->processing_days ?? '—' }}</td>
                        <td class="border px-2 py-1 text-center">{{ $item->created_at->format('Y-m-d h:i A') }}</td>
                        <td class="border px-2 py-1 text-center">{{ $submittedBy }}</td>
                        <td class="border px-2 py-1">
                            {!! \Illuminate\Support\Str::limit(strip_tags($item->grievance_details), 120, '...') !!}
                        </td>
                        <td class="border px-2 py-1 text-left align-top">
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
                        <td colspan="11" class="text-center py-4 text-gray-500 italic">No data available for the selected dates.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="flex flex-col gap-1 ml-5 mt-8 text-sm text-gray-800 w-max">
        <div class="flex items-end gap-2">
            <div class="font-semibold text-gray-700">Noted by:</div>
            <div class="font-semibold text-base border-b-2 border-gray-700 pb-0.5">{{ $hrName ?? 'N/A' }}</div>
        </div>
        <div class="text-center text-gray-500 font-medium text-xs mt-0.5">HR Liaison</div>
    </div>
</div>
