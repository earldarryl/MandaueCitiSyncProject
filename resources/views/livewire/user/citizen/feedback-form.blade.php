<form wire:submit.prevent="submit" class="max-w-5xl mx-auto p-6 lg:p-10 space-y-8">

  <div class="prose max-w-full">
    <h1 class="text-2xl font-bold">Client Satisfaction Measurement (CSM)</h1>
    <p class="text-sm text-gray-600 dark:text-gray-300">
      This tracks the customer experience of government offices. Your feedback on your recently concluded transaction will help this office provide a better service.
      Personal information shared will be kept confidential and you always have the option not to answer this form.
    </p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <flux:field>
        <flux:label class="flex gap-2 items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.25 13.5V7.5a2.25 2.25 0 012.25-2.25h13.5a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25z" />
          </svg>
          <span>Date</span>
        </flux:label>

        <flux:input.group>
          <div x-data x-init="flatpickr($refs.datepicker, { dateFormat: 'Y-m-d', defaultDate: new Date() })" class="relative w-full">
            <flux:input x-ref="datepicker" wire:model="date" type="text" placeholder="Select a date" class="cursor-pointer select-none" readonly />
          </div>
        </flux:input.group>

        <flux:error name="date" />
      </flux:field>
    </div>

    <div>
      <flux:field>
        <flux:label class="flex gap-2 items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M19.5 9.75V6a3 3 0 00-3-3h-9a3 3 0 00-3 3v3.75M19.5 9.75h-15M19.5 9.75V21a1.5 1.5 0 01-1.5 1.5H6a1.5 1.5 0 01-1.5-1.5V9.75" />
          </svg>
          <span>Gender</span>
        </flux:label>

        <flux:input.group>
          <x-searchable-select
            name="gender"
            wire:model="gender"
            placeholder="Select gender"
            :options="['Male', 'Female', 'Prefer not to say']"
          />
        </flux:input.group>

        <flux:error name="gender" />
      </flux:field>
    </div>

    <div>
      <flux:field>
        <flux:label class="flex gap-2 items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 20.25c4.97-4.97 7.5-8.25 7.5-12A7.5 7.5 0 005.25 8.25c0 3.75 2.53 7.03 7.5 12z" />
          </svg>
          <span>Region of Residence</span>
        </flux:label>

        <flux:input.group>
          <flux:input wire:model="region" type="text" placeholder="Region / Province" clearable />
        </flux:input.group>

        <flux:error name="region" />
      </flux:field>
    </div>

    <div>
      <flux:field>
        <flux:label class="flex gap-2 items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M8.25 6.75h7.5M12 3v18m-6.75-9h13.5" />
          </svg>
          <span>Service Availed</span>
        </flux:label>

        <flux:input.group>
          <flux:input wire:model="service" type="text" placeholder="Name of the service" clearable />
        </flux:input.group>

        <flux:error name="service" />
      </flux:field>
    </div>
  </div>

    <div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-6 bg-white dark:bg-zinc-900 shadow-sm">
        <div class="flex flex-col gap-2 mb-3 border-b border-gray-200 dark:border-zinc-700">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Citizen's Charter (CC)</h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                <span class="font-semibold text-gray-800 dark:text-gray-200">Instructions:</span>
                The Citizen’s Charter is an official document that reflects the services of a government agency or office including
                its requirements, fees, and processing times. Please answer the questions below.
            </p>
        </div>

        <div class="space-y-6">
            @foreach ([
                'cc1' => 'Which of the following best describes your awareness of a CC?',
                'cc2' => 'If aware of CC (answered 1–3 in CC1), would you say the CC of this office was...?',
                'cc3' => 'If aware of CC (answered 1–3 in CC1), how much did the CC help you in your transaction?'
            ] as $field => $question)
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">
                        {{ strtoupper($field) }}. {{ $question }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <label
                                class="relative flex items-center space-x-3 p-3 rounded-xl border border-transparent
                                    hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/40
                                    transition cursor-pointer group">

                                <input
                                    type="radio"
                                    wire:model="{{ $field }}"
                                    value="{{ $i }}"
                                    class="sr-only peer"
                                />

                                <div
                                    class="w-5 h-5 rounded-full border-2 border-blue-400 flex-shrink-0
                                        peer-checked:border-blue-600 peer-checked:bg-blue-600
                                        dark:border-blue-300 dark:peer-checked:border-blue-400 dark:peer-checked:bg-blue-400
                                        transition flex items-center justify-center">
                                    <div
                                        class="w-2.5 h-2.5 rounded-full bg-white peer-checked:bg-white
                                            dark:bg-gray-900 dark:peer-checked:bg-gray-900 transition">
                                    </div>
                                </div>

                                <span
                                    class="text-sm text-gray-700 dark:text-gray-200 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition">
                                    {{ match($i) {
                                        1 => '1. I know what a CC is and I saw this office’s CC / Easy to see / Helped very much',
                                        2 => '2. I know what a CC is but did not see this office’s CC / Somewhat easy / Somewhat helped',
                                        3 => '3. I learned of the CC only when I saw this office’s CC / Difficult to see / Did not help',
                                        4 => '4. I do not know what a CC is and did not see one / Not visible / N/A',
                                        5 => '5. N/A',
                                    } }}
                                </span>
                            </label>
                        @endfor
                    </div>

                    <flux:error name="{{ $field }}" />
                </div>
            @endforeach
        </div>
    </fieldset>

   <div
    x-data="{
        questions: [
            'SQD0. I am satisfied with the service that I availed.',
            'SQD1. I spent a reasonable amount for my time.',
            'SQD2. The office followed the transaction\'s requirements and steps.',
            'SQD3. The steps (including payment) were easy and simple.',
            'SQD4. I easily found information about my transaction.',
            'SQD5. I paid a reasonable amount of fees.',
            'SQD6. The office was fair to everyone (“walang palakasan”).',
            'SQD7. The staff were courteous and helpful.',
            'SQD8. I got what I needed or denial was sufficiently explained.'
        ],
        answers: @entangle('answers').live,
        init() {
            // Initialize reactive array if empty
            if (Array.isArray(this.answers) === false || this.answers.length === 0) {
                this.answers = Array(this.questions.length).fill(null);
            }
        }
    }"
    class="space-y-8 max-w-5xl mx-auto bg-white dark:bg-zinc-900 rounded-2xl p-8 border border-gray-300 dark:border-zinc-800 mt-10"
>
    <div class="flex flex-col gap-2">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Service Quality & Delivery (SQD)</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
            <span class="font-semibold text-gray-800 dark:text-gray-200">Instructions:</span>
            For SQD 0–8, please select the column that best corresponds to your answer.
        </p>
    </div>

    <div class="overflow-x-auto mt-6">
        <div class="min-w-[800px]">
            <div class="grid grid-cols-8 gap-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-zinc-700 pb-3">
                <div class="text-left col-span-2 text-gray-900 dark:text-white">Question</div>
                <div>Strongly Disagree</div>
                <div>Disagree</div>
                <div>Neither</div>
                <div>Agree</div>
                <div>Strongly Agree</div>
                <div>N/A</div>
            </div>

            <template x-for="(question, index) in questions" :key="index">
                <div class="grid grid-cols-8 gap-2 items-center py-3 border-b border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/60 transition">
                    <div class="col-span-2 text-sm text-gray-800 dark:text-gray-200 font-medium leading-snug" x-text="question"></div>

                    <template x-for="n in 6" :key="n">
                        <div
                            class="flex justify-center items-center w-full h-full cursor-pointer border border-gray-200 dark:border-zinc-700 rounded-md transition-all duration-150 hover:bg-blue-50 dark:hover:bg-blue-950 hover:border-blue-300"
                            :class="answers[index] == n ? 'bg-blue-100 dark:bg-blue-900 border-blue-500 shadow-sm' : ''"
                            @click="answers[index] = n"
                        >
                            <template x-if="answers[index] == n">
                                <svg xmlns='http://www.w3.org/2000/svg' class='w-5 h-5 text-blue-600 dark:text-blue-400' fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2'>
                                    <path stroke-linecap='round' stroke-linejoin='round' d='M5 13l4 4L19 7' />
                                </svg>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>



  <div class="flex flex-col gap-3 mt-3">
    <div>
        <flux:field>
        <flux:label class="flex gap-2 items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6" />
            </svg>
            <span>Suggestions (optional)</span>
        </flux:label>
        <flux:textarea wire:model="suggestions" resize="vertical" placeholder="Your comments or suggestions..." />
        </flux:field>
    </div>

    <div>
        <flux:field>
        <flux:label class="flex gap-2 items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6" />
            </svg>
            <span>Email (optional)</span>
        </flux:label>
        <flux:input.group>
            <flux:input wire:model="email" type="email" placeholder="you@example.com" clearable />
        </flux:input.group>
        <flux:error name="email" />
        </flux:field>
    </div>

    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-zinc-800">

        <button
            type="button"
            wire:click="resetForm"
            wire:loading.attr="disabled"
            wire:target="resetForm"
            class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                bg-gray-100 text-gray-800 border-gray-300
                hover:bg-gray-200 hover:border-gray-400
                dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-800/50
                transition-all duration-200
                disabled:opacity-50 disabled:cursor-not-allowed">
            <x-heroicon-o-x-mark class="w-4 h-4"/>
            <span wire:loading.remove wire:target="resetForm">Reset</span>
            <span wire:loading wire:target="resetForm">Processing...</span>
        </button>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="submit"
            class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                bg-blue-100 text-blue-800 border-blue-300
                hover:bg-blue-200 hover:border-blue-400
                dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800/60
                transition-all duration-200
                disabled:opacity-50 disabled:cursor-not-allowed">
            <x-heroicon-o-check class="w-4 h-4"/>
            <span wire:loading.remove wire:target="submit">Submit Feedback</span>
            <span wire:loading wire:target="submit">Submitting...</span>
        </button>

    </div>

  </div>

  <flux:modal wire:model.self="showConfirmModal" :closable="false">
    <div class="p-6 flex flex-col items-center text-center space-y-4">
        <div class="rounded-full bg-red-100 p-3 text-red-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" />
            </svg>
        </div>

        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            Missing Required Information
        </h2>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            Some required fields are incomplete or invalid. Please review your input before proceeding.
        </p>

        <div class="flex justify-center gap-3 mt-4">
            <flux:button
                variant="subtle" class="border border-gray-200 dark:border-zinc-800"
                @click="$wire.showConfirmModal = false"
            >
                Close
            </flux:button>
        </div>
    </div>
</flux:modal>
</form>
