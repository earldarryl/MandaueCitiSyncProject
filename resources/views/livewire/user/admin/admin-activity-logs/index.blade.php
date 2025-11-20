<div class="w-full p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm"
     data-component="admin-activity-logs-index"
     data-wire-id="{{ $this->id() }}"
>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6 px-6">
        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                    border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                    transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-users class="h-8 w-8 text-blue-600 dark:text-blue-400"/>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Total Users</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $totalUsers }}</p>
        </div>

        <div class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
                    border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                    transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-signal class="h-8 w-8 text-green-600 dark:text-green-400"/>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Active Users</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">{{ $activeUsers }}</p>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 -mt-1">Online now (last 5 min)</p>
        </div>

        <div class="group relative bg-gradient-to-br from-purple-50 to-purple-100 dark:from-zinc-800 dark:to-zinc-900
                    border border-purple-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                    transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-purple-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-clock class="h-8 w-8 text-purple-600 dark:text-purple-400"/>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Total Online Time</p>
            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 tracking-tight">
                {{ $totalOnlineTimeFormatted }}
            </p>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 -mt-1">
                Sum of all active users
            </p>
        </div>

    </div>

    <div
        x-data="{ autoRefresh: true }"
        x-init="
            setInterval(() => {
                if (autoRefresh) {
                    $wire.call('applyFilter');
                }
            }, 300000);
        "
    >

        <div class="flex flex-col justify-center items-center gap-3 mb-5">

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center w-full gap-2">

                <x-filter-select
                    name="filter"
                    placeholder="Filter by module"
                    :options="$modules"
                    wire:model="filter"
                />

                <x-filter-select
                    name="roleFilter"
                    placeholder="Filter by role"
                    :options="['Admin', 'HR Liaison', 'Citizen']"
                    wire:model="roleFilter"
                />

                <div class="flex flex-col gap-1 w-full"
                    x-data="{ selected: @entangle('selectedDate') }"
                    x-init="$nextTick(() => {
                        flatpickr($refs.dateInput, {
                            dateFormat: 'Y-m-d',
                            defaultDate: selected,
                            onChange: (selectedDates, dateStr) => {
                                selected = dateStr
                            }
                        });
                    })"
                >
                    <div class="relative w-full">
                        <div
                            class="flex items-center justify-between px-3 py-2 border border-gray-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-900 cursor-pointer"
                            @click="$refs.dateInput._flatpickr.open()"
                        >
                            <input
                                type="text"
                                x-ref="dateInput"
                                x-model="selected"
                                readonly
                                placeholder="Select date"
                                class="w-full bg-transparent text-[12px] focus:outline-none cursor-pointer"
                            />
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    x-show="selected"
                                    @click.stop="selected = null; $wire.set('selectedDate', null); $refs.dateInput._flatpickr.clear()"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition p-1 rounded"
                                >
                                    <x-heroicon-o-x-mark class="w-4 h-4"/>
                                </button>
                                <x-heroicon-o-calendar class="w-4 h-4 text-gray-500" />
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="relative flex flex-col w-full">
                <button
                    wire:click="applyFilter"
                    wire:loading.attr="disabled"
                    wire:target="applyFilter"
                    class="flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white w-full font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
                    <flux:icon.adjustments-horizontal class="w-4 h-4" />
                    <span wire:loading.remove wire:target="applyFilter">Apply Filter</span>
                    <span wire:loading wire:target="applyFilter">Processing...</span>
                </button>
            </div>

            <div class="relative flex flex-col w-full">

                <div class="relative flex flex-row justify-end gap-2">

                    <button
                        wire:click="downloadCsv"
                        wire:loading.attr="disabled"
                        wire:target="downloadCsv"
                        class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                            bg-blue-100 text-blue-800 border-blue-300
                            hover:bg-blue-200 hover:border-blue-400
                            dark:bg-blue-800 dark:text-blue-200 dark:border-blue-700
                            dark:hover:bg-blue-700
                            whitespace-nowrap
                            transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span wire:loading.remove wire:target="downloadCsv">Export CSV</span>
                        <span wire:loading wire:target="downloadCsv">Processing...</span>
                    </button>

                    <button
                        wire:click="downloadExcel"
                        wire:loading.attr="disabled"
                        wire:target="downloadExcel"
                        class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                            bg-green-100 text-green-800 border-green-300
                            hover:bg-green-200 hover:border-green-400
                            dark:bg-green-800 dark:text-green-200 dark:border-green-700
                            dark:hover:bg-green-700
                            whitespace-nowrap
                            transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        <span wire:loading.remove wire:target="downloadExcel">Export Excel</span>
                        <span wire:loading wire:target="downloadExcel">Processing...</span>
                    </button>

                    <button
                        wire:click="exportActivityLogsPDF"
                        wire:loading.attr="disabled"
                        wire:target="exportActivityLogsPDF"
                        class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                            bg-red-100 text-red-800 border-red-300
                            hover:bg-red-200 hover:border-red-400
                            dark:bg-red-800 dark:text-red-300 dark:border-red-700
                            dark:hover:bg-red-700
                            whitespace-nowrap
                            transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        <x-heroicon-o-document-arrow-down class="w-4 h-4" />

                        <span wire:loading.remove wire:target="exportActivityLogsPDF">
                            Export PDF
                        </span>

                        <span wire:loading wire:target="exportActivityLogsPDF">
                            Processing...
                        </span>
                    </button>
                </div>

            </div>

        </div>

    </div>

    @forelse ($groupedLogs as $dateLabel => $logs)
        <div wire:key="group-{{ md5($dateLabel) }}">
            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ $dateLabel }}</h2>

            <ol class="relative border-s border-gray-200 dark:border-gray-700 mb-8">
                @foreach ($logs as $log)
                    @php
                        $bgColor = $log->module === 'Grievance Management'
                            ? 'bg-green-500 dark:bg-green-700'
                            : 'bg-blue-500 dark:bg-blue-700';
                        $svgColor = 'text-white';
                    @endphp

                    <li class="mb-10 ms-5 group w-full" wire:key="log-{{ $log->id }}">
                        <span
                            class="absolute flex items-center justify-center w-6 h-6 {{ $bgColor }} rounded-full -start-3 ring-8 ring-white dark:ring-zinc-900 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-2.5 h-2.5 {{ $svgColor }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                            </svg>
                        </span>

                        <div
                            class="px-6 py-5 w-full bg-white dark:bg-zinc-900/60 border border-gray-200 dark:border-zinc-700/80 rounded-2xl
                                hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out backdrop-blur-sm">

                            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm hover:shadow-md transition p-4 mb-4 border border-gray-200 dark:border-zinc-700">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full p-2">
                                            <x-heroicon-o-clipboard-document-check class="h-6 w-6"/>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col gap-1">
                                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            {{ ucwords(str_replace('_', ' ', $log->action_type)) }}
                                        </span>

                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug tracking-tight">
                                            {{ str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $log->action))) }}
                                        </h3>

                                        <div class="flex flex-col gap-2 mt-2">

                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Module:</span>
                                                <span class="bg-blue-100 dark:bg-blue-800/40 text-blue-700 dark:text-blue-300 text-xs font-medium px-2 py-1 rounded-full">
                                                    {{ $log->module ?? 'N/A' }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Platform:</span>
                                                <span class="bg-gray-100 dark:bg-zinc-700/40 text-gray-700 dark:text-gray-300 text-xs font-medium px-2 py-1 rounded-full">
                                                    {{ $log->platform ?? 'N/A' }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">When:</span>
                                                <span class="text-xs text-gray-700 dark:text-gray-300">
                                                    {{ \Carbon\Carbon::parse($log->timestamp)->format('F j, Y – g:i A') ?? 'N/A' }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Location:</span>
                                                <span class="text-xs text-gray-700 dark:text-gray-300">
                                                    {{ $log->location ?? 'Unknown' }}
                                                </span>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400 w-full">
            <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
            <p class="text-sm font-medium">No activity logs available.</p>
        </div>
    @endforelse

    <div class="mt-6">
        {{ $logsPaginator->links() }}
    </div>

</div>
<script>
    (function () {
        const PAGINATION_SELECTOR = [
            'a[wire\\:click*="previousPage"]',
            'a[wire\\:click*="nextPage"]',
            'a[wire\\:click*="gotoPage"]',
            'button[wire\\:click*="previousPage"]',
            'button[wire\\:click*="nextPage"]',
            'button[wire\\:click*="gotoPage"]',
            '.pagination a',
            'ul.pagination li a'
        ].join(',');

        const SCROLL_CONTAINER_SELECTOR =
            'div.relative.flex.flex-col.flex-1.h-full.overflow-y-auto.overflow-x-auto';

        document.addEventListener('click', function (ev) {
            const el = ev.target.closest(PAGINATION_SELECTOR);
            if (!el) return;

            const scrollArea = document.querySelector(SCROLL_CONTAINER_SELECTOR);
            if (!scrollArea) return;

            setTimeout(() => {
                scrollArea.scrollTo({ top: 0, behavior: 'smooth' });
            }, 50);
        }, { passive: true });
    })();
</script>



