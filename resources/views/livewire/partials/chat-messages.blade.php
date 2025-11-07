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
