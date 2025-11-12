<div
    x-data="{
        isDocked: false,
        isDragging: false,
        isResizing: false,
        position: { x: window.innerWidth - 450, y: 100 },
        size: { width: 384, height: 500 },
        offset: { x: 0, y: 0 },
        resizeStart: { x: 0, y: 0, width: 0, height: 0 },
        messagesContainer: null,
        pendingUserMessage: '',

        toggleDock() {
            this.isDocked = !this.isDocked;
        },

        startDrag(e) {
            if (this.isDocked) return;
            this.isDragging = true;
            this.offset.x = e.clientX - this.position.x;
            this.offset.y = e.clientY - this.position.y;
        },

        stopDrag() {
            this.isDragging = false;
        },

        startResize(e) {
            if (this.isDocked) return;
            e.stopPropagation();
            this.isResizing = true;
            this.resizeStart.x = e.clientX;
            this.resizeStart.y = e.clientY;
            this.resizeStart.width = this.size.width;
            this.resizeStart.height = this.size.height;
        },

        stopResize() {
            this.isResizing = false;
        },

        handleMouseMove(e) {
            if (this.isDragging && !this.isDocked) {
                this.position.x = e.clientX - this.offset.x;
                this.position.y = e.clientY - this.offset.y;
            } else if (this.isResizing && !this.isDocked) {
                const deltaX = e.clientX - this.resizeStart.x;
                const deltaY = e.clientY - this.resizeStart.y;
                this.size.width = Math.max(300, this.resizeStart.width + deltaX);
                this.size.height = Math.max(400, this.resizeStart.height + deltaY);
            }
        }
    }"
    @mousemove.window="handleMouseMove($event)"
    @mouseup.window="stopDrag(); stopResize()"
    :class="isDocked ? 'relative mb-4' : 'fixed z-50'"
    :style="isDocked ? 'width: 100%; height: 500px;' : 'left: ' + position.x + 'px; top: ' + position.y + 'px; width: ' + size.width + 'px; height: ' + size.height + 'px;'"
>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 h-full flex flex-col relative">
        <!-- Header -->
        <div
            @mousedown="startDrag($event)"
            class="flex items-center justify-between p-3 bg-primary-600 dark:bg-primary-700 text-white rounded-t-lg"
            :class="!isDocked ? 'cursor-move' : ''"
        >
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <span class="font-semibold text-sm">AI Assistant</span>
            </div>
            <div class="flex items-center gap-1">
                <button
                    @click="toggleDock()"
                    class="p-1 hover:bg-primary-700 dark:hover:bg-primary-800 rounded transition"
                    :title="isDocked ? 'Undock (floating)' : 'Pin to top'"
                >
                    <!-- Pin icon when not docked -->
                    <svg x-show="!isDocked" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    <!-- Unpin icon when docked -->
                    <svg x-show="isDocked" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </button>
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
            class="overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900 flex-1"
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
        >
            <!-- Existing messages from Livewire -->
            @foreach($messages as $message)
                <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] {{ $message['role'] === 'user' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700' }} rounded-lg px-4 py-3 shadow-sm">
                        <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $message['content'] }}</p>
                    </div>
                </div>
            @endforeach

            <!-- Pending user message (shown immediately when form submitted) -->
            <div x-show="pendingUserMessage !== ''" class="flex justify-end">
                <div class="max-w-[80%] bg-blue-600 text-white rounded-lg px-4 py-3 shadow-sm">
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
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <span>AI is thinking...</span>
                    </div>
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

        <!-- Resize Handle (only show when floating) -->
        <div
            x-show="!isDocked"
            @mousedown="startResize($event)"
            class="absolute bottom-0 right-0 w-4 h-4 cursor-se-resize"
            title="Drag to resize"
        >
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-600" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 16V10h-2v4h-4v2h6zM16 6V0h-2v4h-4v2h6z"/>
            </svg>
        </div>
    </div>
</div>
