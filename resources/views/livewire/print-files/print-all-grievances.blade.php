<div class="page w-full relative p-5 print:mx-0 print:p-5">

    <div class="header flex justify-center items-center border-b-4 border-blue-700 pb-2 mb-4">
        <div class="header-left flex items-center gap-3 border-r-2 border-gray-800 pr-3">
            <img src="{{ asset('images/mandaue-logo.png') }}" alt="Mandaue Logo"
                 class="w-16 h-16 rounded-full object-cover bg-white">
        </div>
        <div class="header-right flex flex-col justify-center items-center text-center ml-3">
            <span class="text-sm text-black">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</span>
            <span class="text-2xl font-light uppercase text-black">GRIEVANCE REPORTS</span>
        </div>
    </div>

    <div class="summary-date text-center font-semibold mt-5 mb-3 text-sm">
        {{ now()->format('F d, Y') }}
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-300 bg-white">
        <table class="w-full text-xs border-collapse text-gray-800">
            <thead class="bg-gray-100 uppercase font-semibold text-gray-700 text-xs">
                <tr>
                    <th class="px-3 py-2 border">TICKET ID</th>
                    <th class="px-3 py-2 border">CITIZEN</th>
                    <th class="px-3 py-2 border">DEPARTMENTS</th>
                    <th class="px-3 py-2 border">CATEGORY</th>
                    <th class="px-3 py-2 border">PRIORITY</th>
                    <th class="px-3 py-2 border">STATUS</th>
                    <th class="px-3 py-2 border">DATE FILED</th>
                    <th class="px-3 py-2 border">DETAILS</th>
                    <th class="px-3 py-2 border">ATTACHMENTS</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                @forelse ($grievances as $index => $grievance)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-3 py-2 border text-center font-bold">
                            {{ $grievance->grievance_ticket_id }}
                        </td>

                        <td class="px-3 py-2 border">
                            @if ($grievance->is_anonymous)
                                <span class="italic text-gray-500 flex justify-center w-full">Anonymous</span>
                            @else
                                {{ $grievance->user->name }}
                            @endif
                        </td>

                        <td class="px-3 py-2 border text-center">
                            {{ $grievance->departments->pluck('department_name')->join(', ') ?? '—' }}
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center italic text-gray-500 py-3">
                            No grievances available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="noted flex gap-3 mt-10 ml-5">
        <div class="noted-text text-base">Noted:</div>

        <div class="name-with-role flex flex-col items-center gap-1">

            {{-- Name Line --}}
            <div class="name font-semibold border-b-2 border-gray-800 px-3">
                @if(isset($hr_liaison))
                    {{ $hr_liaison->name }}
                @elseif(isset($admin))
                    {{ $admin->name }}
                @else
                    N/A
                @endif
            </div>

            <div class="position text-gray-500 text-sm font-medium text-center">
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

</div>
