<div class="flex flex-col h-full rounded-lg border border-gray-300 dark:border-zinc-700 bg-gray-200 dark:bg-gray-800 overflow-hidden">

    <!-- Chat Box -->
    <div
        id="chat-box"
        class="flex-1 overflow-y-auto p-4 space-y-3 scroll-smooth"
        x-data="chatScroll"
        x-init="init"
    >
        <template x-if="$wire.loadingOlder">
            <div class="text-center text-gray-400 text-xs py-2 flex justify-center items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l3 3-3 3v-4a8 8 0 01-8-8z"></path>
                </svg>
                Loading older messages...
            </div>
        </template>

        @foreach($messages as $msg)
            @php
                $isSender = $msg['sender_id'] === auth()->id();
                $files = $msg['file_path'] ? json_decode($msg['file_path'], true) : [];
                $names = $msg['file_name'] ? json_decode($msg['file_name'], true) : [];
            @endphp

            <div class="{{ $isSender ? 'flex justify-end' : 'flex justify-start' }}">
                <div class="max-w-xs px-3 py-2 rounded-lg text-sm break-words {{ $isSender ? 'bg-blue-600 text-white' : 'bg-gray-300 dark:bg-zinc-700 text-gray-900 dark:text-gray-100' }}">
                    @if(!empty($msg['message']))
                        <p class="whitespace-pre-wrap">{{ $msg['message'] }}</p>
                    @endif

                    @if(!empty($files))
                        <div class="mt-2 space-y-2">
                            @foreach($files as $index => $file)
                                @php
                                    $name = $names[$index] ?? basename($file);
                                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                @endphp

                                @if($isImage)
                                    <div x-data="{ show: false }" class="relative">
                                        <img
                                            src="{{ Storage::url($file) }}"
                                            alt="{{ $name }}"
                                            class="w-40 h-auto rounded-lg cursor-pointer hover:scale-105 transition"
                                            @click="show = true"
                                        />
                                        <div
                                            x-show="show"
                                            x-transition.opacity
                                            x-cloak
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
                                            @click.self="show = false"
                                        >
                                            <div x-transition.scale class="relative max-w-[90vw] max-h-[80vh]">
                                                <!-- Control Buttons (Top Right Corner) -->
                                                <div class="absolute top-2 right-2 flex items-center gap-2">
                                                    <!-- Download Button -->
                                                    <a
                                                        href="{{ Storage::url($file) }}"
                                                        download="{{ $name }}"
                                                        class="text-white bg-black/50 rounded-full p-1.5 hover:bg-black transition"
                                                        title="Download Image"
                                                    >
                                                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                                                    </a>

                                                    <!-- Close Button -->
                                                    <button
                                                        @click="show = false"
                                                        class="text-white bg-black/50 rounded-full p-1.5 hover:bg-black transition"
                                                        title="Close"
                                                    >
                                                        <x-heroicon-o-x-mark class="w-5 h-5" />
                                                    </button>
                                                </div>

                                                <!-- Image Preview -->
                                                <img
                                                    src="{{ Storage::url($file) }}"
                                                    alt="{{ $name }}"
                                                    class="rounded-lg border border-gray-200 dark:border-gray-700 max-w-full max-h-[80vh]"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a
                                        href="{{ Storage::url($file) }}"
                                        target="_blank"
                                        class="underline text-sm flex items-center gap-1 hover:text-blue-500"
                                    >
                                        <x-heroicon-o-paper-clip class="w-4 h-4" />
                                        {{ $name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <span class="block text-[10px] text-white mt-1">
                        {{ $msg['sender']['name'] ?? 'Unknown' }} ·
                        {{ \Carbon\Carbon::parse($msg['created_at'])->diffForHumans() }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Input Area (fixed height & sticky bottom) -->
    <div class="border-t border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-3">
        <form wire:submit.prevent="sendMessage" class="flex flex-col gap-2">
            <!-- Message and Send -->
            <div class="flex items-center">
                <input
                    type="text"
                    wire:model.defer="newMessage"
                    placeholder="Type a message..."
                    class="flex-1 px-3 py-2 border rounded-l-full bg-gray-100 dark:bg-zinc-800 border-gray-300 dark:border-zinc-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                />
                <flux:button
                    type="submit"
                    variant="primary"
                    color="blue"
                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 rounded-r-full transition duration-300 ease-in-out"
                    icon="paper-airplane"
                >
                    Send
                </flux:button>
            </div>

            <div class="max-h-72 overflow-y-auto border border-gray-200 dark:border-zinc-700 rounded-lg bg-white/60 dark:bg-zinc-800/60 p-2">
                {{ $this->form }}
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatScroll', () => ({
        init() {
            const chatBox = this.$el;
            this.$nextTick(() => chatBox.scrollTop = chatBox.scrollHeight);

            let loading = false;
            chatBox.addEventListener('scroll', () => {
                if (chatBox.scrollTop <= 0 && !loading) {
                    loading = true;
                    $wire.call('loadMore').then(() => {
                        this.$nextTick(() => {
                            chatBox.scrollTop = 50;
                            loading = false;
                        });
                    });
                }
            });
        },
    }));
});
</script>
