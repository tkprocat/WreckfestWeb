<div class="metal-texture rounded shadow-2xl p-8 border-2 border-gray-700 flex flex-col">
    <div class="mb-6">
        <h2 class="text-2xl font-black text-wreckfest uppercase tracking-wider"
            style="font-family: Impact, 'Arial Black', sans-serif;">Track Rotation</h2>
        @if($collectionName)
            <p class="text-gray-400 font-semibold mt-1 uppercase tracking-wide text-sm">
                {{ $collectionName }}
            </p>
        @endif
    </div>

    <div class="space-y-2 flex-1 overflow-y-auto custom-scrollbar pr-2">
        @if(count($trackRotation) > 0)
            @foreach($trackRotation as $index => $track)
                @php
                    $trackId = $track['track'] ?? '';
                    $isCurrent = $trackId === $currentTrack;
                    $trackName = $track['trackName'] ?? 'Unknown Track';
                    $locationName = $track['locationName'] ?? '';
                    $gamemodeName = $track['gamemodeName'] ?? '';
                    $laps = $track['laps'] ?? null;
                    $bots = $track['bots'] ?? null;

                    // Generate image path
                    $imageFilename = str_replace(['/', ' '], ['_', '_'], strtolower($trackId)) . '.png';
                    $imagePath = asset("images/tracks/{$imageFilename}");
                @endphp

                <div class="{{ $isCurrent ? 'bg-wreckfest/20 border-l-4 border-wreckfest glow-orange' : 'bg-black/30 border-l-4 border-gray-600' }} rounded overflow-hidden hover:bg-black/40 transition-colors shadow-lg">
                    <div class="flex items-center">
                        <!-- Track Info -->
                        <div class="flex-1 p-4">
                            <div class="flex items-center space-x-2 mb-1">
                                @if($isCurrent)
                                    <span class="relative flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-500 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                                    </span>
                                @else
                                    <span class="text-gray-500 font-bold text-sm">#{{ $index + 1 }}</span>
                                @endif
                                <h3 class="{{ $isCurrent ? 'text-wreckfest' : 'text-white' }} font-bold text-lg">
                                    {{ $locationName }}
                                </h3>
                                @if($isCurrent)
                                    <span class="bg-wreckfest text-white text-xs font-bold px-2 py-1 rounded uppercase tracking-wide">
                                        NOW PLAYING
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-400 text-sm font-medium mb-2">{{ $trackName }}</p>
                            <div class="flex flex-wrap gap-2">
                                @if($gamemodeName)
                                    <span class="bg-gray-700 text-gray-300 text-xs font-semibold px-2 py-1 rounded">
                                        {{ $gamemodeName }}
                                    </span>
                                @endif
                                @if($laps)
                                    <span class="bg-gray-700 text-gray-300 text-xs font-semibold px-2 py-1 rounded">
                                        {{ $laps }} Laps
                                    </span>
                                @endif
                                @if($bots)
                                    <span class="bg-gray-700 text-gray-300 text-xs font-semibold px-2 py-1 rounded">
                                        {{ $bots }} Bots
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Track Image (Right Side) -->
                        <div class="w-32 h-20 flex-shrink-0 relative flex items-center justify-center pt-3 px-2 pb-2">
                            <div class="w-full h-full flex items-center justify-center overflow-hidden">
                                <img
                                    src="{{ $imagePath }}"
                                    alt="{{ $trackName }}"
                                    class="w-full h-full object-cover"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="absolute inset-0 flex items-center justify-center" style="display: none;">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
                <p class="mt-4 text-gray-400 text-lg font-bold uppercase tracking-wide">No track rotation configured</p>
                <p class="mt-2 text-gray-500 text-sm font-semibold">Configure tracks in the admin panel</p>
            </div>
        @endif
    </div>
</div>
