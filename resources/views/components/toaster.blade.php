<div
    x-data="{ show: false, message: '', type: 'info' }"
    x-init="
        console.log('Toaster mounted');
        Livewire.on('toast', (data) => {
        console.log('Toast received:', data.message, data.type);
        message = data.message ?? '';
        type = data.type ?? 'info';
        show = true;
        setTimeout(() => show = false, 3000);
    });
    "
    x-show="show"
    x-transition
    class="fixed top-5 right-5 z-50"
>
    <div
        class="px-4 py-2 rounded shadow-lg text-white flex gap-2 items-center"
        :class="{
            'bg-mc_primary_color': type === 'welcome',
            'bg-green-500': type === 'success',
            'bg-red-500': type === 'error',
            'bg-yellow-500': type === 'warning',
            'bg-blue-500': type === 'info'
        }"
    >
        <!-- Icons -->
        <template x-if="type === 'success'">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </template>

        <template x-if="type === 'error'">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </template>

        <template x-if="type === 'warning'">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v2m0 4h.01M12 5a7 7 0 100 14 7 7 0 000-14z"/>
            </svg>
        </template>

        <template x-if="type === 'info'">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
            </svg>
        </template>

        <template x-if="type === 'welcome'">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M5 13l4 4L19 7"/>
            </svg>
        </template>

        <template x-if="!['success','error','warning','info','welcome'].includes(type)">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
            </svg>
        </template>

        <span x-text="message"></span>
    </div>
</div>
