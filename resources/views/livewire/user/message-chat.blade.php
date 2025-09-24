<div class="flex flex-col h-96 bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
    <!-- Messages -->
    <div class="flex-1 overflow-y-auto space-y-3">
        @forelse ($messages as $msg)
            <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="px-3 py-2 rounded-lg max-w-xs text-sm
                    {{ $msg->sender_id === auth()->id()
                        ? 'bg-blue-500 text-white'
                        : 'bg-gray-200 dark:bg-zinc-700 text-gray-900 dark:text-gray-200' }}">
                    <p>{{ $msg->message }}</p>
                    <small class="block text-xs opacity-70 mt-1">
                        {{ $msg->created_at->diffForHumans() }}
                    </small>
                </div>
            </div>
        @empty
            <p class="text-center text-sm text-gray-500">No messages yet.</p>
        @endforelse
    </div>

    <!-- Input -->
    <form wire:submit.prevent="sendMessage" class="mt-3 flex gap-2">
        <input type="text" wire:model="messageText"
               class="flex-1 rounded-lg border-gray-300 dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
               placeholder="Type your message..." />
        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Send
        </button>
    </form>
</div>
