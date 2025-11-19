<div class="max-w-5xl mx-auto bg-white p-10 rounded-lg shadow space-y-12">

    <div class="text-center border-b pb-6">
        <h1 class="text-3xl font-bold">All Feedback Reports</h1>

        @if(isset($admin))
            <p class="text-gray-600 mt-2">
                Admin: <strong>{{ $admin->name }}</strong>
            </p>
        @endif

        <p class="text-gray-500 text-sm">{{ now()->format('F j, Y, g:i A') }}</p>

        <button onclick="window.print()" class="no-print mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
            Print
        </button>
    </div>

    @forelse ($feedbacks as $feedback)
        <div class="border rounded-lg p-6 shadow-sm page-break">
            <h2 class="text-xl font-semibold mb-4 text-center text-blue-800">
                Feedback #{{ $feedback->id }}
            </h2>

            <div class="grid grid-cols-2 gap-6 mb-4">
                <div>
                    <p>
                        <strong>User:</strong>
                        @if($feedback->user)
                            {{ $feedback->user->name ?? 'Unknown' }}
                        @else
                            <span class="italic text-gray-500">Anonymous / N/A</span>
                        @endif
                    </p>
                    <p><strong>CC Summary:</strong> {{ $feedback->cc_summary }}</p>
                </div>
                <div>
                    <p><strong>Date Submitted:</strong> {{ $feedback->date->format('F j, Y') }}</p>
                    <p><strong>SQD Summary:</strong> {{ $feedback->sqd_summary }}</p>
                </div>
            </div>

            <div class="border-t pt-4">
                <p class="text-gray-700 whitespace-pre-line">{!! $feedback->answers_summary ?? 'No answers provided' !!}</p>
            </div>
        </div>

        @if (!$loop->last)
            <hr class="my-10 border-gray-300">
        @endif
    @empty
        <p class="text-center text-gray-600 italic">No feedbacks available for printing.</p>
    @endforelse
</div>
