<div class="flex flex-col h-[500px] rounded-lg border border-gray-300 dark:border-zinc-700 bg-gray-200 dark:bg-gray-800 overflow-hidden">

    <div
        id="chat-box"
        x-ref="chatBox"
        class="flex-1 overflow-y-auto p-4 space-y-3 scroll-smooth"
        x-data="chatScroll({{ $canLoadMore ? 'true' : 'false' }})"
        x-init="init"
    >
       <template x-if="loadingOlder && hasMore">
            <div class="text-center text-gray-400 text-xs py-2">
                Loading older messages...
            </div>
        </template>

        @if (empty($messages))
            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400">
                <x-heroicon-o-chat-bubble-left-ellipsis class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
                <p class="text-sm font-medium">No messages yet</p>
                <p class="text-xs">Start a conversation with your HR Liaison if needed.</p>
            </div>
        @else
            <div x-show="!hasMore" x-cloak>
                <div class="text-center text-gray-400 text-xs py-2 italic">
                    — No more messages —
                </div>
            </div>

            @foreach($this->messages as $msg)
                @php
                    $isSender = $msg['sender_id'] === auth()->id();
                    $files = $msg['file_path'] ? json_decode($msg['file_path'], true) : [];
                    $names = $msg['file_name'] ? json_decode($msg['file_name'], true) : [];
                    $isImageOnly = count($files) > 0 && collect($names)->every(fn($n) => in_array(strtolower(pathinfo($n, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif','webp']));
                @endphp

                <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }}">
                    <div class="flex flex-col max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg px-3 py-2 rounded-lg text-sm break-words
                                {{ $isSender ? 'bg-blue-600 text-white rounded-tl-2xl rounded-bl-2xl' : 'bg-gray-300 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 rounded-tr-2xl rounded-br-2xl' }}">

                        @if(!empty($msg['message']))
                            <p class="whitespace-pre-wrap">{{ $msg['message'] }}</p>
                        @endif

                        @if(!empty($files))
                            <div class="relative mt-3 space-y-3">
                                @php
                                    $maxVisible = 4;
                                    $total = count($files);

                                    $imageExts = ['jpg','jpeg','png','gif','webp'];
                                    $imageFiles = [];
                                    $docFiles = [];

                                    foreach ($files as $index => $file) {
                                        $name = $names[$index] ?? basename($file);
                                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                        if (in_array($ext, $imageExts)) {
                                            $imageFiles[] = ['file' => $file, 'name' => $name];
                                        } else {
                                            $docFiles[] = ['file' => $file, 'name' => $name, 'ext' => strtoupper($ext)];
                                        }
                                    }
                                @endphp

                                @if(count($imageFiles) > 0)
                                    <div x-data="{ showAll: false }" class="space-y-2">
                                        <div class="grid gap-2 grid-cols-{{ count($imageFiles) > 1 ? '2' : '1' }}">
                                            @foreach($imageFiles as $index => $img)
                                                @php
                                                    $file = $img['file'];
                                                    $name = $img['name'];
                                                @endphp

                                                @if($index < $maxVisible - 1 || count($imageFiles) <= $maxVisible)
                                                    <div class="group relative rounded-lg overflow-hidden aspect-[1/1]">
                                                        <img
                                                            src="{{ Storage::url($file) }}"
                                                            alt="{{ $name }}"
                                                            class="w-full h-full object-cover rounded-lg"
                                                            @click="$dispatch('zoom-image', { src: '{{ Storage::url($file) }}' })"
                                                        />

                                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-start justify-end pt-2 pr-2 cursor-pointer" @click="$dispatch('zoom-image', { src: '{{ Storage::url($file) }}' })">
                                                            <a href="{{ Storage::url($file) }}" download="{{ $name }}"
                                                            class="inline-flex items-center justify-center rounded-full h-9 w-9 bg-white/40 hover:bg-white/70">
                                                                <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-white" />
                                                            </a>
                                                        </div>
                                                    </div>
                                                @elseif($index === $maxVisible - 1 && count($imageFiles) > $maxVisible)
                                                    <div class="group relative rounded-lg overflow-hidden aspect-[1/1]">
                                                        <button @click="showAll = true"
                                                                class="absolute inset-0 bg-gray-900/80 hover:bg-gray-900/60 transition-all duration-300 rounded-lg flex items-center justify-center">
                                                            <span class="text-xl font-semibold text-white">
                                                                +{{ count($imageFiles) - ($maxVisible - 1) }}
                                                            </span>
                                                        </button>
                                                        <img src="{{ Storage::url($file) }}" alt="{{ $name }}" class="w-full h-full object-cover rounded-lg">
                                                    </div>
                                                    @break
                                                @endif
                                            @endforeach
                                        </div>

                                        <div
                                            x-show="showAll"
                                            x-transition
                                            x-cloak
                                            class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
                                        >
                                            <div class="bg-white dark:bg-gray-800 rounded-2xl px-4 max-w-3xl w-full overflow-y-auto max-h-[80vh] relative">
                                                <div class="flex items-center justify-between border-b border-gray-300 dark:border-zinc-700 py-2">
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                                        <x-heroicon-o-photo class="w-5 h-5" />
                                                        Additional Images
                                                    </h3>
                                                    <flux:button
                                                        @click="showAll = false"
                                                        icon="x-mark"
                                                        variant="subtle"
                                                    />

                                                </div>

                                                <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 md:grid-cols-4 my-6">
                                                    @foreach($imageFiles as $index => $img)
                                                        @if($index >= $maxVisible - 1)
                                                            @php
                                                                $file = $img['file'];
                                                                $name = $img['name'];
                                                            @endphp
                                                            <div class="group relative rounded-lg overflow-hidden aspect-[1/1]">
                                                                <img
                                                                    src="{{ Storage::url($file) }}"
                                                                    alt="{{ $name }}"
                                                                    class="w-full h-full object-cover rounded-lg"
                                                                    @click="$dispatch('zoom-image', { src: '{{ Storage::url($file) }}' })"
                                                                />
                                                                <div class="absolute inset-0 z-10 bg-black/50 opacity-0 group-hover:opacity-100 flex items-start justify-end pt-2 pr-2 transition cursor-pointer" @click="$dispatch('zoom-image', { src: '{{ Storage::url($file) }}' })">
                                                                    <a
                                                                        href="{{ Storage::url($file) }}"
                                                                        download="{{ $name }}"
                                                                        class="inline-flex items-center justify-center rounded-full h-10 w-10 bg-white/40 hover:bg-white/70"
                                                                    >
                                                                        <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-white" />
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(count($docFiles) > 0)
                                    <div class="space-y-2">
                                        @foreach($docFiles as $doc)
                                            @php
                                                $file = $doc['file'];
                                                $name = $doc['name'];
                                                $ext  = $doc['ext'];

                                                $size = Storage::disk('public')->exists($file)
                                                    ? $this->readableSize(Storage::disk('public')->size($file))
                                                    : 'Unavailable';
                                            @endphp


                                            <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-lg p-2">
                                                <div class="flex flex-col text-xs">
                                                    <span class="flex items-center gap-1 text-sm font-bold">
                                                        <x-heroicon-o-document class="w-4 h-4 text-gray-700 dark:text-gray-200" />
                                                        <span class="text-black dark:text-white">{{ $name }}</span>
                                                    </span>
                                                    <span class="text-gray-500 dark:text-gray-300">{{ $ext }} · {{ $size ?? '' }}</span>
                                                </div>
                                                <a href="{{ Storage::url($file) }}" download="{{ $name }}"
                                                class="p-2 rounded-lg bg-white hover:bg-gray-300 transition">
                                                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-black" />
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        @php
                            $sender = App\Models\User::with('departments', 'roles')
                                        ->find($msg['sender_id']);

                            $senderName = 'Unknown';

                            if ($sender) {
                                if ($sender->is_anonymous ?? false) {
                                    $senderName = 'Anonymous User';
                                } elseif ($sender->hasRole('hr_liaison')) {
                                    $departmentNames = $sender->departments->pluck('department_name')->join(', ');
                                    $senderName = "HR Liaison" . ($departmentNames ? " - {$departmentNames}" : '');
                                } else {
                                    $senderName = $sender->name;
                                }
                            }
                        @endphp

                        <span class="block text-[10px] mt-2 {{ $isSender ? 'text-blue-100' : 'text-gray-700 dark:text-gray-300' }}">
                        {{ $senderName }} · {{ \Carbon\Carbon::parse($msg['created_at'])->timezone(config('app.timezone'))->format('M d, Y h:i A') }} · {{ \Carbon\Carbon::parse($msg['created_at'])->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="border-t border-gray-300 dark:border-zinc-700 bg-white dark:bg-black p-3 flex-shrink-0" x-data="{ openModal: false }">
        <form wire:submit.prevent="sendMessage" class="flex items-center gap-3">
            <div class="flex flex-1 items-center border border-gray-300 dark:border-zinc-700 rounded-full bg-gray-100 dark:bg-zinc-800 overflow-hidden">
                <input
                    type="text"
                    wire:model.defer="newMessage"
                    placeholder="Type a message..."
                    class="flex-1 px-3 py-2 bg-transparent focus:ring-0 focus:outline-none text-sm"
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

            <flux:button
                type="button"
                @click="openModal = true"
                variant="primary"
                color="blue"
                class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 rounded-full transition duration-300 ease-in-out"
                icon="plus"
            />

        </form>

        <div
            x-show="openModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <div
                @click="openModal = false"
                class="fixed inset-0 bg-black/50"
            ></div>

            <div
                class="w-full max-w-md bg-white dark:bg-zinc-800 rounded-t-xl shadow-xl transform transition-all duration-300"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="translate-y-full opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="translate-y-full opacity-0"
            >

                <header class="flex w-full justify-center items-center py-2">
                    <flux:button icon="chevron-down" variant="subtle" class="w-full" @click="openModal = false" />
                </header>

                <div
                    class="max-h-72 w-full justify-center items-center overflow-y-auto border border-gray-200 dark:border-zinc-700
                        rounded-lg bg-white/60 dark:bg-zinc-800/60 px-3 py-4"
                    style="scrollbar-gutter: stable;"
                >
                    <div class="min-h-[12rem]">
                        {{ $this->form }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div
        x-data="imageZoom()"
        x-cloak
    >
        <div
            x-show="zoomSrc"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div class="relative max-w-3xl w-full">
                <button
                    @click="close()"
                    class="absolute top-2 right-2 bg-black/60 hover:bg-black/80 text-white p-2 rounded-full z-50"
                >
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>

                <img
                    :src="zoomSrc"
                    class="rounded-lg max-h-[85vh] mx-auto object-contain shadow-xl"
                />
            </div>
        </div>
    </div>

    <script>
    function imageZoom() {
        return {
            zoomSrc: null,
            open(src) { this.zoomSrc = src },
            close() { this.zoomSrc = null },
            init() {
                window.addEventListener('zoom-image', e => {
                    this.zoomSrc = e.detail.src;
                });
            }
        }
    }
    </script>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatScroll', (initialCanLoadMore) => ({
        hasMore: initialCanLoadMore,
        loadingOlder: false,

        checkScroll() {
            const el = this.$refs.chatBox;
            if (!el || !this.hasMore || this.loadingOlder) return;

            if (Math.ceil(el.scrollTop) === 0) {
                this.loadingOlder = true;

                const prevScrollTop = el.scrollTop;
                const prevScrollHeight = el.scrollHeight;

                const onUpdated = (event) => {
                    this.hasMore = event.detail.canLoadMore;
                    this.$nextTick(() => {
                        const newScrollHeight = el.scrollHeight;

                        el.scrollTop = prevScrollTop + (newScrollHeight - prevScrollHeight);

                        this.loadingOlder = false;
                    });
                    window.removeEventListener('messagesLoaded', onUpdated);
                };

                window.addEventListener('messagesLoaded', onUpdated);

                this.$wire.loadOlderMessages();
            }
        },

        init() {
            const el = this.$refs.chatBox;

            this.hasMore = initialCanLoadMore;

            el.addEventListener('scroll', () => this.checkScroll());

            this.$nextTick(() => {
                if (el.scrollHeight > el.clientHeight) {
                    el.scrollTop = el.scrollHeight;
                }
            });

            window.addEventListener('messageReceived', (event) => {
                if ((el.scrollHeight - el.scrollTop - el.clientHeight) < 100) {
                    this.$nextTick(() => {
                        el.scrollTop = el.scrollHeight;
                    });
                }
            });
        }
    }));
});
</script>
