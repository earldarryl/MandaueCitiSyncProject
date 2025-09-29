<div
    class="flex flex-col h-96 border rounded-lg bg-gray-50 dark:bg-zinc-800"
    x-data="{ newMessage: '' }"
>
    {{-- Messages --}}
    <div id="chat-box" class="flex-1 overflow-y-auto p-4 space-y-2">
        @foreach($messages as $msg)
            <div class="{{ $msg['sender_id'] === auth()->id() ? 'flex justify-end' : 'flex justify-start' }}">
                <div class="max-w-xs px-3 py-2 rounded-lg text-sm
                     {{ $msg['sender_id'] === auth()->id()
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-200 dark:bg-zinc-700 text-gray-900 dark:text-gray-100' }}">
                    <p>{{ $msg['message'] }}</p>
                    <span class="block text-[10px] text-gray-400 mt-1">
                        {{ $msg['sender']['name'] ?? 'Unknown' }} ·
                        {{ \Carbon\Carbon::parse($msg['created_at'])->diffForHumans() }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Input form --}}
    <form wire:submit.prevent="sendMessage" class="flex border-t p-2">
        <input
            type="text"
            wire:model.defer="newMessage"
            placeholder="Type a message..."
            class="flex-1 px-3 py-2 border rounded-l"
        />
        <button
            type="submit"
            class="bg-blue-600 text-white px-4 rounded-r hover:bg-blue-700"
        >
            Send
        </button>
    </form>
</div>
