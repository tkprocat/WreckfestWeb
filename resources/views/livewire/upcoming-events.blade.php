<div class="metal-texture rounded shadow-2xl p-8 border-2 border-gray-700 flex flex-col">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-black text-wreckfest uppercase tracking-wider"
            style="font-family: Impact, 'Arial Black', sans-serif;">Upcoming Events</h2>
        @if(count($events) > 0)
            <span class="bg-wreckfest-dark text-white font-semibold py-2 px-5 rounded border-2 border-wreckfest uppercase tracking-wider button-text-shadow">
                {{ count($events) }} Scheduled
            </span>
        @endif
    </div>

    {{-- Active Event Banner --}}
    @if($activeEvent)
        <div class="mb-6 bg-gradient-to-r from-wreckfest/20 to-transparent border-l-4 border-wreckfest rounded p-4 shadow-lg">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-5 h-5 text-wreckfest animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-wreckfest font-bold uppercase tracking-wider text-sm">Currently Active</span>
            </div>
            <h3 class="text-white font-bold text-lg">{{ $activeEvent['name'] }}</h3>
            @if($activeEvent['description'])
                <div class="text-gray-300 text-sm mt-1 prose prose-sm prose-invert max-w-none">{!! $activeEvent['description'] !!}</div>
            @endif
        </div>
    @endif

    {{-- Upcoming Events List --}}
    <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar pr-2">
        @if(count($events) > 0)
            @foreach($events as $event)
                <div x-data="{ expanded: false }" class="bg-black/30 rounded p-4 hover:bg-black/40 transition-colors border-l-4 border-blue-500 shadow-lg">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="text-white font-bold text-xl">{{ $event['name'] }}</h3>
                        <div
                            class="text-gray-400 text-base font-mono whitespace-nowrap ml-4"
                            x-data="{
                                utcTime: '{{ $event['start_time'] }}',
                                localTime: ''
                            }"
                            x-init="localTime = new Date(utcTime).toLocaleString('en-US', {
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            }).replace(',', '')">
                            Starting at <span x-text="localTime"></span>
                        </div>
                    </div>

                    @if($event['description'])
                        <div class="text-gray-300 text-base mb-3 prose prose-base prose-invert max-w-none">{!! $event['description'] !!}</div>
                    @endif

                    <div class="flex items-center justify-between text-base">
                        <div class="flex items-center space-x-4">
                            @if($event['track_count'] > 0)
                                <button
                                    @click="expanded = !expanded"
                                    class="bg-blue-600/20 text-blue-400 font-semibold px-3 py-1 rounded text-sm border border-blue-600/30 hover:bg-blue-600/30 transition flex items-center gap-1">
                                    {{ $event['track_count'] }} tracks
                                    <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Countdown --}}
                        <div
                            class="flex items-center space-x-2 text-gray-400"
                            x-data="{
                                utcTime: '{{ $event['start_time'] }}',
                                countdown: ''
                            }"
                            x-init="
                                const updateCountdown = () => {
                                    const now = new Date();
                                    const eventDate = new Date(utcTime);
                                    const diff = eventDate - now;

                                    if (diff < 0) {
                                        countdown = 'Started';
                                        return;
                                    }

                                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                                    if (days > 0) {
                                        countdown = `in ${days}d ${hours}h`;
                                    } else if (hours > 0) {
                                        countdown = `in ${hours}h ${minutes}m`;
                                    } else {
                                        countdown = `in ${minutes}m`;
                                    }
                                };
                                updateCountdown();
                                setInterval(updateCountdown, 60000);
                            ">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-mono text-sm" x-text="countdown"></span>
                        </div>
                    </div>

                    {{-- Track List (Expandable) --}}
                    @if($event['track_count'] > 0)
                        <div x-show="expanded" x-collapse class="mt-3 pt-3 border-t border-gray-700">
                            <div class="space-y-2">
                                @foreach($event['tracks'] as $index => $track)
                                    @php
                                        $trackVariant = \App\Models\TrackVariant::where('variant_id', $track['track'])->with('track')->first();
                                        $trackName = $trackVariant?->name ?? $track['track'];
                                        $locationName = $trackVariant?->track?->name ?? 'Unknown';
                                        $gamemode = strtolower($track['gamemode'] ?? 'racing');
                                        $gamemodeName = config('wreckfest.gamemodes.' . $gamemode, ucfirst($gamemode));

                                        // Use configured laps or sensible defaults (5 for racing, 1 for derby)
                                        $laps = $track['laps'] ?? (in_array($gamemode, ['derby', 'derby deathmatch', 'team derby']) ? 1 : 5);
                                        $bots = $track['bots'] ?? null;

                                        // Generate image path
                                        $imageFilename = str_replace(['/', ' '], ['_', '_'], strtolower($track['track'])) . '.png';
                                        $imagePath = asset("images/tracks/{$imageFilename}");
                                    @endphp
                                    <div class="flex items-center bg-black/40 rounded overflow-hidden hover:bg-black/50 transition-colors">
                                        <!-- Track Number -->
                                        <div class="px-3 py-2 bg-black/30">
                                            <span class="text-gray-500 font-bold text-sm font-mono">{{ $index + 1 }}</span>
                                        </div>

                                        <!-- Track Info -->
                                        <div class="flex-1 px-3 py-2">
                                            <div class="font-bold text-white text-sm mb-1">{{ $locationName }}</div>
                                            <div class="text-gray-400 text-xs mb-1">{{ $trackName }}</div>
                                            <div class="flex flex-wrap gap-1">
                                                @if($gamemodeName)
                                                    <span class="bg-gray-700 text-gray-300 text-xs font-semibold px-2 py-0.5 rounded">
                                                        {{ $gamemodeName }}
                                                    </span>
                                                @endif
                                                <span class="bg-blue-600/30 text-blue-400 text-xs font-bold px-2 py-0.5 rounded border border-blue-600/50">
                                                    {{ $laps }} {{ $laps == 1 ? 'Lap' : 'Laps' }}
                                                </span>
                                                @if($bots)
                                                    <span class="bg-gray-700 text-gray-300 text-xs font-semibold px-2 py-0.5 rounded">
                                                        {{ $bots }} Bots
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Track Image -->
                                        <div class="w-20 h-14 flex-shrink-0 relative">
                                            <img
                                                src="{{ $imagePath }}"
                                                alt="{{ $trackName }}"
                                                class="w-full h-full object-cover"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            >
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/30" style="display: none;">
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="mt-4 text-gray-400 text-lg font-bold uppercase tracking-wide">No upcoming events</p>
                <p class="mt-2 text-gray-500 text-sm font-semibold">Check back later for scheduled events!</p>
            </div>
        @endif
    </div>
</div>
