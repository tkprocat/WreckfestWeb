<div class="metal-texture rounded shadow-2xl p-8 border-2 border-gray-700 flex flex-col">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-black text-wreckfest uppercase tracking-wider"
            style="font-family: Impact, 'Arial Black', sans-serif;">Current Players</h2>
        <span class="bg-wreckfest-dark text-white font-semibold py-2 px-5 rounded border-2 border-wreckfest uppercase tracking-wider button-text-shadow">
            {{ count($players) }} / {{ $maxPlayers }}
        </span>
    </div>

    <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar pr-2">
        @if(count($players) > 0)
            @foreach($players as $player)
                @php
                    $isBot = $player['isBot'] ?? false;
                    $playerName = $player['name'] ?? 'Unknown Player';
                    $displayName = $isBot ? '(BOT) ' . $playerName : $playerName;
                @endphp

                <div class="bg-black/30 rounded p-4 hover:bg-black/40 transition-colors border-l-4 border-wreckfest shadow-lg">
                    <div class="flex items-center space-x-3">
                        <span class="text-white font-bold">{{ $displayName }}</span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="mt-4 text-gray-400 text-lg font-bold uppercase tracking-wide">No players currently online</p>
                <p class="mt-2 text-gray-500 text-sm font-semibold">Be the first to join!</p>
            </div>
        @endif
    </div>
</div>
