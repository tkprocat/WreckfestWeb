<div
    x-data="{
        isDragging: false,
        position: { x: window.innerWidth - 450, y: 100 },
        offset: { x: 0, y: 0 },
        messagesContainer: null,
        pendingUserMessage: '',

        startDrag(e) {
            this.isDragging = true;
            this.offset.x = e.clientX - this.position.x;
            this.offset.y = e.clientY - this.position.y;
        },

        stopDrag() {
            this.isDragging = false;
        }
    }"
    @mousemove.window="if (isDragging) { position.x = $event.clientX - offset.x; position.y = $event.clientY - offset.y; }"
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
            class="overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900"
            x-ref="messagesContainer"
            x-init="
                // Store ref to messagesContainer in parent scope
                messagesContainer = $refs.messagesContainer;

                // Scroll to bottom on initial load and when messages update
                $nextTick(() => {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                });

                // Listen for messages-updated event
                $watch('$wire.messages', () => {
                    $nextTick(() => {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    });
                    // Clear pending message when backend updates
                    pendingUserMessage = '';
                });

                // Watch for pending message changes
                $watch('pendingUserMessage', () => {
                    $nextTick(() => {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    });
                });
            "
            style="min-height: 400px; max-height: 400px;"
        >
            <!-- Existing messages from Livewire -->
            @foreach($messages as $message)
                <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] {{ $message['role'] === 'user' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700' }} rounded-lg px-4 py-3 shadow-sm">
                        <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $message['content'] }}</p>
                    </div>
                </div>
            @endforeach

            <!-- Pending user message (shown immediately when form submitted) -->
            <div x-show="pendingUserMessage !== ''" class="flex justify-end">
                <div class="max-w-[80%] bg-primary-600 text-white rounded-lg px-4 py-3 shadow-sm">
                    <p class="text-sm whitespace-pre-wrap leading-relaxed" x-text="pendingUserMessage"></p>
                </div>
            </div>

            <!-- Streaming AI response -->
            <div
                wire:loading
                wire:target="sendMessage"
                class="flex justify-start"
            >
                <div class="max-w-[80%] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 shadow-sm">
                    <!-- Streamed text content -->
                    <p wire:stream="ai-response" class="text-sm whitespace-pre-wrap leading-relaxed"></p>
                </div>
            </div>

        </div>

        <!-- Input Area -->
        <div
            x-show="!$wire.isMinimized"
            x-transition
            class="p-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 rounded-b-lg"
        >
            <form wire:submit="sendMessage" class="flex gap-2" @submit="pendingUserMessage = $wire.message">
                <input
                    wire:model="message"
                    type="text"
                    placeholder="Ask about tracks, collections..."
                    class="flex-1 px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
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
