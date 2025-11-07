<div
    x-data="{
        isDragging: false,
        position: { x: window.innerWidth - 450, y: 100 },
        offset: { x: 0, y: 0 },

        startDrag(e) {
            this.isDragging = true;
            this.offset.x = e.clientX - this.position.x;
            this.offset.y = e.clientY - this.position.y;
        },

        drag(e) {
            if (this.isDragging) {
                this.position.x = e.clientX - this.offset.x;
                this.position.y = e.clientY - this.offset.y;
            }
        },

        stopDrag() {
            this.isDragging = false;
        }
    }"
    @mousemove.window="drag($event)"
    @mouseup.window="stopDrag()"
    class="fixed z-50"
    :style="'left: ' + position.x + 'px; top: ' + position.y + 'px;'"
>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 w-96">
        <!-- Header -->
        <div
            @mousedown="startDrag($event)"
            class="flex items-center justify-between p-3 bg-primary-600 dark:bg-primary-700 text-white rounded-t-lg cursor-move"
        >
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <span class="font-semibold text-sm">AI Assistant</span>
            </div>
            <div class="flex items-center gap-1">
                <button
                    wire:click="toggleMinimize"
                    class="p-1 hover:bg-primary-700 dark:hover:bg-primary-800 rounded transition"
                    title="Minimize"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <button
                    wire:click="clearChat"
                    wire:confirm="Are you sure you want to clear the chat history?"
                    class="p-1 hover:bg-primary-700 dark:hover:bg-primary-800 rounded transition"
                    title="Clear chat"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div
            x-show="!$wire.isMinimized"
            x-transition
            class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900"
            x-ref="messagesContainer"
            x-init="
                // Scroll to bottom on initial load
                setTimeout(() => $refs.messagesContainer.scrollTop = $refs.messagesContainer.scrollHeight, 100);
                // Watch for new messages
                $watch('$wire.messages', (value) => {
                    setTimeout(() => $refs.messagesContainer.scrollTop = $refs.messagesContainer.scrollHeight, 100)
                });
            "
            @messages-updated.window="
                // Force Alpine to refresh by clearing and reloading
                $wire.$refresh();
                setTimeout(() => {
                    $refs.messagesContainer.scrollTop = $refs.messagesContainer.scrollHeight;
                }, 100);
            "
            style="max-height: 400px !important;"
        >
            @forelse($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%]">
                        <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                            <div class="text-sm whitespace-pre-wrap">{{ $msg['content'] }}</div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $msg['role'] === 'user' ? 'text-right' : 'text-left' }}">
                            {{ \Carbon\Carbon::parse($msg['timestamp'])->format('H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400 text-sm">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <p>Ask me anything about track collections!</p>
                        <p class="text-xs mt-1">I can help you explore tracks, create collections, and more.</p>
                    </div>
                </div>
            @endforelse

            <!-- Loading indicator -->
            <div wire:loading wire:target="sendMessage" class="flex justify-start">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="flex gap-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                        </div>
                        <span class="text-xs text-gray-500">AI is thinking...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div
            x-show="!$wire.isMinimized"
            x-transition
            class="p-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 rounded-b-lg"
        >
            <form
                wire:submit="sendMessage"
                class="flex gap-2"
            >
                <input
                    x-ref="messageInput"
                    wire:model="message"
                    type="text"
                    placeholder="Ask about tracks, collections..."
                    class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                />
                <button
                    type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                >
                    <svg wire:loading.remove wire:target="sendMessage" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <svg wire:loading wire:target="sendMessage" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
