<div class="page w-full relative p-5 print:mx-0 print:p-5">

    {{-- HEADER --}}
    <div class="header flex justify-center items-center border-b-4 border-blue-700 pb-2 mb-4">
        <div class="header-left flex items-center gap-3 border-r-2 border-gray-800 pr-3">
            <img src="{{ asset('images/mandaue-logo.png') }}" alt="Mandaue Logo"
                 class="w-16 h-16 rounded-full object-cover bg-white">
        </div>
        <div class="header-right flex flex-col justify-center items-center text-center ml-3">
            <span class="text-sm text-black">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</span>
            <span class="text-2xl font-light uppercase text-black">FEEDBACK REPORTS</span>
        </div>
    </div>

    {{-- DATE --}}
    <div class="summary-date text-center font-semibold mt-5 mb-3 text-sm">
        {{ now()->format('F d, Y') }}
    </div>

    {{-- FEEDBACK TABLE --}}
    <div class="overflow-x-auto rounded-lg border border-gray-300 bg-white">
        <table class="w-full text-xs border-collapse text-gray-800">
            <thead class="bg-gray-100 uppercase font-semibold text-gray-700 text-xs">
                <tr>
                    <th class="px-3 py-2 border">Feedback ID</th>
                    <th class="px-3 py-2 border">User</th>
                    <th class="px-3 py-2 border">CC Summary</th>
                    <th class="px-3 py-2 border">SQD Summary</th>
                    <th class="px-3 py-2 border">Date Submitted</th>
                    <th class="px-3 py-2 border">Answers Summary</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                @forelse ($feedbacks as $index => $feedback)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-3 py-2 border text-center font-bold">
                            {{ $feedback->id }}
                        </td>
                        <td class="px-3 py-2 border text-center">
                            @if($feedback->user)
                                {{ $feedback->user->name ?? 'Unknown' }}
                            @else
                                <span class="italic text-gray-500 flex justify-center w-full">Anonymous / N/A</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 border">
                            {!! Str::limit(strip_tags($feedback->cc_summary), 120, '...') !!}
                        </td>
                        <td class="px-3 py-2 border">
                            {!! Str::limit(strip_tags($feedback->sqd_summary), 120, '...') !!}
                        </td>
                        <td class="px-3 py-2 border text-center">
                            {{ $feedback->date->format('Y-m-d h:i A') }}
                        </td>
                        <td class="px-3 py-2 border">
                            {!! Str::limit(strip_tags($feedback->answers_summary), 200, '...') !!}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center italic text-gray-500 py-3">
                            No feedbacks available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="noted flex gap-3 mt-10 ml-5">
        <div class="noted-text text-base">Noted:</div>

        <div class="name-with-role flex flex-col items-center gap-1">

            <div class="name font-semibold border-b-2 border-gray-800 px-3">
                @if(isset($admin))
                    {{ $admin->name }}
                @else
                    N/A
                @endif
            </div>

            <div class="position text-gray-500 text-sm font-medium text-center">
                @if(isset($admin))
                    Admin
                @else
                    —
                @endif
            </div>
        </div>
    </div>

</div>
