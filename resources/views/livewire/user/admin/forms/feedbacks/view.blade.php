<div class="w-full px-2 bg-gray-100/20 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 flex flex-col gap-6">

    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto py-2">
        <x-responsive-nav-link
            href="{{ route('admin.forms.feedbacks.index') }}"
            wire:navigate
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-gray-100 dark:bg-zinc-800 text-gray-800 dark:text-gray-200
                border border-gray-500 dark:border-gray-200
                hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 w-full sm:w-52"
        >
            <x-heroicon-o-home class="w-5 h-5 text-gray-700 dark:text-gray-300" />
            <span class="hidden lg:inline">Return to Feedbacks</span>
            <span class="lg:hidden">Home</span>
        </x-responsive-nav-link>
    </div>

    <header class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 flex flex-col gap-6 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex flex-col gap-2">
                <h2 class="flex items-center gap-2 text-md font-semibold text-gray-500 dark:text-gray-400 tracking-wider uppercase">
                    <x-heroicon-o-identification class="w-4 h-4 inline mr-1 text-gray-500 dark:text-gray-400" />
                    FEEDBACK ID
                </h2>
                <p class="text-3xl sm:text-4xl font-extrabold text-blue-700 dark:text-blue-400 leading-tight">
                    {{ $feedback->id }}
                </p>
            </div>

            <p class="hidden sm:flex text-sm text-gray-500 dark:text-gray-400 italic items-center gap-1 shrink-0">
                <x-heroicon-o-clock class="w-4 h-4 shrink-0" />
                <span>Submitted {{ optional($feedback->date)->format('M d, Y') }}</span>
            </p>
        </div>

        <div class="flex flex-col gap-2">
            <h2 class="flex items-center gap-2 text-md font-semibold text-gray-500 dark:text-gray-400 tracking-wider uppercase">
                <x-heroicon-o-tag class="w-4 h-4 inline mr-1 text-gray-500 dark:text-gray-400" />
                SERVICE
            </h2>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 truncate overflow-hidden capitalize leading-tight">
                {{ $feedback->service }}
            </p>
        </div>
    </header>

    @php
        $hasDetails = $feedback->gender || $feedback->region || $feedback->email || $feedback->cc1 || $feedback->cc2 || $feedback->cc3;

        $ccQuestions = [
            'cc1' => 'Which of the following best describes your awareness of a CC?',
            'cc2' => 'If aware of CC (answered 1–3 in CC1), would you say the CC of this office was...?',
            'cc3' => 'If aware of CC (answered 1–3 in CC1), how much did the CC help you in your transaction?'
        ];

        $ccChoices = [
            1 => '1. I know what a CC is and I saw this office’s CC / Easy to see / Helped very much',
            2 => '2. I know what a CC is but did not see this office’s CC / Somewhat easy / Somewhat helped',
            3 => '3. I learned of the CC only when I saw this office’s CC / Difficult to see / Did not help',
            4 => '4. I do not know what a CC is and did not see one / Not visible / N/A',
            5 => '5. N/A',
        ];
    @endphp

    <div class="flex flex-col gap-4 p-5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm">
        <h4 class="flex items-center gap-2 text-[17px] font-semibold text-gray-600 dark:text-gray-400 mb-2 tracking-wide uppercase border-b border-gray-200 dark:border-zinc-700 pb-2">
            <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            Feedback Details
        </h4>
         @php
            $details = [
                ['label' => 'Gender', 'value' => $feedback->gender ? ucfirst($feedback->gender) : 'N/A', 'icon' => 'user'],
                ['label' => 'Region', 'value' => $feedback->region ?? 'N/A', 'icon' => 'map'],
                ['label' => 'Email', 'value' => $feedback->email ?? 'N/A', 'icon' => 'envelope'],
            ];
        @endphp
         @if (collect($details)->contains(fn($item) => $item['value'] !== 'N/A'))
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8">
                <div class="flex-1 flex flex-col gap-2">
                    @foreach ($details as $item)
                        <div class="flex items-start justify-between border-b border-gray-300 dark:border-zinc-700 py-2">
                            <div class="flex items-center gap-2 w-44">
                                <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                <span class="text-[15px] font-semibold text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                            </div>
                            <span class="text-[15px] font-bold flex-1 text-right text-gray-900 dark:text-gray-100">
                                {{ $item['value'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-700 my-4"></div>

            <div class="space-y-6">
                <h5 class="flex items-center gap-2 text-[16px] font-semibold text-gray-700 dark:text-gray-300 tracking-wide">
                    <x-heroicon-o-document-text class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                    Citizen’s Charter (CC) Responses
                </h5>

                @foreach ($ccQuestions as $field => $question)
                    <div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-4 bg-gray-50 dark:bg-zinc-900/40 hover:shadow-sm transition">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">
                            {{ strtoupper($field) }}. {{ $question }}
                        </p>

                        @php
                            $ccValue = $feedback->{$field} ?? null;
                            $selectedText = $ccValue && isset($ccChoices[$ccValue]) ? $ccChoices[$ccValue] : 'N/A';
                            $ccColor = match($ccValue) {
                                1 => 'text-green-600 dark:text-green-400',
                                2 => 'text-blue-600 dark:text-blue-400',
                                3 => 'text-amber-600 dark:text-amber-400',
                                4 => 'text-orange-600 dark:text-orange-400',
                                5 => 'text-gray-500 dark:text-gray-400',
                                default => 'text-gray-400 dark:text-gray-500',
                            };
                        @endphp

                        <div class="flex items-start gap-2 mt-1">
                            <x-heroicon-o-check-circle class="w-5 h-5 {{ $ccColor }}" />
                            <span class="text-[15px] font-medium leading-relaxed {{ $ccColor }}">
                                {{ $selectedText }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400 w-full">
                <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
                <p class="text-sm font-medium">No feedback details available</p>
            </div>
        @endif
    </div>

    @php
        $answers = is_array($feedback->answers) ? $feedback->answers : json_decode($feedback->answers, true) ?? [];

        $questions = [
            'SQD0. I am satisfied with the service that I availed.',
            'SQD1. I spent a reasonable amount for my time.',
            'SQD2. The office followed the transaction\'s requirements and steps.',
            'SQD3. The steps (including payment) were easy and simple.',
            'SQD4. I easily found information about my transaction.',
            'SQD5. I paid a reasonable amount of fees.',
            'SQD6. The office was fair to everyone (“walang palakasan”).',
            'SQD7. The staff were courteous and helpful.',
            'SQD8. I got what I needed or denial was sufficiently explained.',
        ];

        $satisfactionLevels = [
            1 => ['label' => 'Strongly Disagree', 'icon' => '😡', 'color' => 'text-red-500 dark:text-red-400'],
            2 => ['label' => 'Disagree', 'icon' => '😣', 'color' => 'text-orange-500 dark:text-orange-400'],
            3 => ['label' => 'Neither Agree nor Disagree', 'icon' => '😐', 'color' => 'text-amber-500 dark:text-amber-400'],
            4 => ['label' => 'Agree', 'icon' => '🙂', 'color' => 'text-blue-500 dark:text-blue-400'],
            5 => ['label' => 'Strongly Agree', 'icon' => '😄', 'color' => 'text-green-600 dark:text-green-400'],
            6 => ['label' => 'N/A', 'icon' => '❓', 'color' => 'text-gray-500 dark:text-gray-400'],
        ];
    @endphp

    <div class="flex flex-col gap-4 p-5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm">
        <h5 class="flex items-center gap-2 text-[17px] font-semibold text-gray-600 dark:text-gray-400 mb-2 tracking-wide uppercase">
            <x-heroicon-o-question-mark-circle class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            Service Quality & Delivery (SQD) Answers
        </h5>

        <div class="border-t border-gray-300 dark:border-zinc-700 my-2"></div>

        @if(!empty($answers))
            <div class="flex flex-col divide-y divide-gray-300 dark:divide-zinc-700">
                @foreach ($questions as $index => $question)
                    @php
                        $answer = $answers[$index] ?? null;
                        $display = $satisfactionLevels[$answer]['label'] ?? ($answer ?: '—');
                        $icon = $satisfactionLevels[$answer]['icon'] ?? '❔';
                        $color = $satisfactionLevels[$answer]['color'] ?? 'text-gray-900 dark:text-gray-100';
                    @endphp

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-3 hover:bg-gray-50 dark:hover:bg-zinc-800/60 transition-all duration-150">
                        <div class="flex items-start gap-2 w-full sm:w-2/3">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0 mt-0.5" />
                            <span class="text-[15px] font-semibold text-gray-700 dark:text-gray-300 leading-snug">
                                {{ $question }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 sm:justify-end mt-2 sm:mt-0 sm:flex-1 text-[15px] font-medium leading-relaxed {{ $color }}">
                            <span class="text-lg">{{ $icon }}</span>
                            <span>{{ $display }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400 w-full">
                <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
                <p class="text-sm font-medium">No answers available</p>
            </div>
        @endif
    </div>

    <div class="flex flex-col gap-4 p-5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm mb-3">
        <h5 class="flex items-center gap-2 text-[17px] font-semibold text-gray-600 dark:text-gray-400 mb-2 tracking-wide uppercase">
            <x-heroicon-o-light-bulb class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            Suggestions
        </h5>

        @if($feedback->suggestions)
            <p class="text-gray-900 dark:text-gray-200 leading-relaxed">{!! $feedback->suggestions !!}</p>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400 w-full">
                <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
                <p class="text-sm font-medium">No suggestions provided</p>
            </div>
        @endif
    </div>
</div>
