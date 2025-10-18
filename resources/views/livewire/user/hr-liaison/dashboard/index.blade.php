<div class="w-full flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900 relative p-3"
     x-data
     x-init="
        Livewire.hook('message.sent', () => { document.body.style.overflow = 'hidden' });
        Livewire.hook('message.processed', () => { document.body.style.overflow = '' });
     ">

    <!-- Global Loader -->
    <div wire:loading wire:target="startDate,endDate"
         class="absolute inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-50">
        <div class="flex flex-col mt-6 items-center">
            <flux:icon.loading class="h-12 w-12 text-blue-600"/>
            <span class="mt-3 text-gray-700 dark:text-gray-300 text-sm">Refreshing dashboard…</span>
        </div>
    </div>

    <!-- Date Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 w-full px-4">

        <!-- Start Date -->
        <div class="w-full md:w-1/2 cursor-pointer" x-data x-init="$nextTick(() => {
            const setupFlatpickr = (ref, livewireProperty) => {
                const applyDarkMode = (instance, isDark) => {
                    const calendar = instance.calendarContainer;
                    if (!calendar) return;
                    calendar.classList.toggle('flatpickr-dark', isDark);
                };

                const isDark = document.documentElement.classList.contains('dark');

                if (!ref._flatpickr) {
                    flatpickr(ref, {
                        dateFormat: 'Y-m-d',
                        defaultDate: ref.value || null,
                        onChange: (selectedDates, dateStr) => {
                            @this.set(livewireProperty, dateStr);
                        },
                        onReady: (selectedDates, dateStr, instance) => applyDarkMode(instance, isDark),
                    });
                } else {
                    applyDarkMode(ref._flatpickr, isDark);
                }

                // Observe dark mode changes
                const observer = new MutationObserver(() => {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (ref._flatpickr) applyDarkMode(ref._flatpickr, isDark);
                });
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            };

            setupFlatpickr($refs.startInput, 'startDate');
        })">
            <flux:input
                type="text"
                label="Start Date"
                placeholder="Select start date"
                x-ref="startInput"
                readonly
                class:input="w-full"
                value="{{ $startDate }}" />
        </div>

        <!-- End Date -->
        <div class="w-full md:w-1/2 cursor-pointer" x-data x-init="$nextTick(() => {
            const setupFlatpickr = (ref, livewireProperty) => {
                const applyDarkMode = (instance, isDark) => {
                    const calendar = instance.calendarContainer;
                    if (!calendar) return;
                    calendar.classList.toggle('flatpickr-dark', isDark);
                };

                const isDark = document.documentElement.classList.contains('dark');

                if (!ref._flatpickr) {
                    flatpickr(ref, {
                        dateFormat: 'Y-m-d',
                        defaultDate: ref.value || null,
                        onChange: (selectedDates, dateStr) => {
                            @this.set(livewireProperty, dateStr);
                        },
                        onReady: (selectedDates, dateStr, instance) => applyDarkMode(instance, isDark),
                    });
                } else {
                    applyDarkMode(ref._flatpickr, isDark);
                }

                // Observe dark mode changes
                const observer = new MutationObserver(() => {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (ref._flatpickr) applyDarkMode(ref._flatpickr, isDark);
                });
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            };

            setupFlatpickr($refs.endInput, 'endDate');
        })">
            <flux:input
                type="text"
                label="End Date"
                placeholder="Select end date"
                x-ref="endInput"
                readonly
                class:input="w-full"
                value="{{ $endDate }}" />
        </div>

    </div>

    <div class="flex flex-col gap-4 border-box">

        <!-- HR Liaison Stats -->
        <section class="w-full p-4">
            <div class="flex flex-col lg:flex-row gap-6">
                <livewire:user.hr-liaison.dashboard.hr-liaison-stats
                    :start-date="$startDate"
                    :end-date="$endDate"
                    wire:key="hr-liaison-stats-{{ $startDate }}-{{ $endDate }}"/>
            </div>
        </section>

        <!-- Bar Chart -->
       <section class="w-full h-full p-4">
            <div class="flex flex-col lg:flex-row gap-6 w-full h-full">
                <div class="flex-1 w-full">
                    <livewire:grievance-bar-chart
                        :start-date="$startDate"
                        :end-date="$endDate"
                        wire:key="grievance-bar-{{ $startDate }}-{{ $endDate }}"/>
                </div>

                <div class="flex-1 w-full">
                    <livewire:hr-liaison-user-grievance-chart
                        :start-date="$startDate"
                        :end-date="$endDate"
                        wire:key="user-grievance-chart-{{ $startDate }}-{{ $endDate }}"/>
                </div>
            </div>
        </section>

        <!-- Grievance Table -->
        <section class="w-full p-4 relative rounded-lg">
                <livewire:dashboard-grievance-table
                    :start-date="$startDate"
                    :end-date="$endDate"
                    wire:key="grievances-table-{{ $startDate }}-{{ $endDate }}" />
        </section>

    </div>
</div>
