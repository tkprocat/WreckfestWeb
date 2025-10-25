<div class="mb-8 flex justify-center">
    <div class="metal-texture rounded-lg shadow-2xl p-6 border-2 border-gray-700 inline-flex items-center space-x-4">
        @if($isRunning)
            <span class="relative flex h-5 w-5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-5 w-5 bg-green-500"></span>
            </span>
            <span class="text-green-400 font-black uppercase tracking-wider text-2xl">Server Online</span>
        @else
            <span class="relative flex h-5 w-5 rounded-full bg-red-500"></span>
            <span class="text-red-400 font-black uppercase tracking-wider text-2xl">Server Offline</span>
        @endif
    </div>
</div>
