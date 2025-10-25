<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $serverNamePlain }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #1f1f1f;
        }

        .metal-texture {
            background: linear-gradient(145deg, #2a2a2a, #1f1f1f);
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.5),
            inset -2px -2px 5px rgba(255, 255, 255, 0.05);
        }

        .glow-orange {
            box-shadow: 0 0 20px rgba(160, 61, 0, 0.5);
        }

        .text-wreckfest {
            color: #d45500;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .border-wreckfest {
            border-color: #a03d00;
        }

        .bg-wreckfest {
            background-color: #a03d00;
        }

        .bg-wreckfest-dark {
            background: linear-gradient(135deg, #a03d00 0%, #802f00 100%);
        }

        .button-text-shadow {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }
    </style>
</head>
<body class="h-full">
<div class="min-h-full">
    <!-- Header -->
    <header class="metal-texture shadow-2xl border-b-4 border-wreckfest">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-black uppercase tracking-wider"
                        style="font-family: Impact, 'Arial Black', sans-serif; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);">{!! $serverNameHtml !!}</h1>
                    <p class="text-gray-400 mt-1 font-semibold tracking-wide">WRECKFEST DEDICATED SERVER</p>
                </div>
                @auth
                    <a href="/admin"
                       class="bg-wreckfest-dark hover:bg-wreckfest text-white font-semibold py-3 px-8 rounded transition duration-200 shadow-lg uppercase tracking-wider border-2 border-wreckfest glow-orange button-text-shadow">
                        Admin
                    </a>
                @else
                    <a href="/admin/login"
                       class="bg-wreckfest-dark hover:bg-wreckfest text-white font-semibold py-3 px-8 rounded transition duration-200 shadow-lg uppercase tracking-wider border-2 border-wreckfest glow-orange button-text-shadow">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($apiError)
            <!-- API Error Banner -->
            <div class="mb-6 bg-red-900/50 border-2 border-red-600 rounded shadow-2xl p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-xl font-bold text-red-200 uppercase tracking-wide">Unable to Contact Wreckfest
                            Controller</h3>
                        <p class="mt-2 text-red-300 font-semibold">
                            The Wreckfest API is not responding. Please ensure the Wreckfest Controller is running and
                            accessible.
                        </p>
                        <p class="mt-1 text-red-400 text-sm font-medium">
                            Expected API URL: {{ config('wreckfest.api_url') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Server Status Badge -->
        <div class="mb-8 flex justify-center">
            <div
                class="metal-texture rounded-lg shadow-2xl p-6 border-2 border-gray-700 inline-flex items-center space-x-4">
                @if(!empty($serverStatus))
                    <span class="relative flex h-5 w-5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-5 w-5 bg-green-500"></span>
                        </span>
                    <span class="text-green-400 font-black uppercase tracking-wider text-2xl">Server Online</span>
                @else
                    <span class="relative flex h-5 w-5 rounded-full bg-red-500"></span>
                    <span class="text-red-400 font-black uppercase tracking-wider text-2xl">Server Offline</span>
                @endif
            </div>
        </div>

        <!-- Players and Track Rotation Grid -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Current Players Card -->
            <div class="metal-texture rounded shadow-2xl p-8 border-2 border-gray-700 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-black text-wreckfest uppercase tracking-wider"
                        style="font-family: Impact, 'Arial Black', sans-serif;">Current Players</h2>
                    <span
                        class="bg-wreckfest-dark text-white font-semibold py-2 px-5 rounded border-2 border-wreckfest uppercase tracking-wider button-text-shadow">
                            {{ is_array($players) ? count($players) : 0 }} / {{ $maxPlayers }}
                        </span>
                </div>

                <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar pr-2">
                    @if(is_array($players) && count($players) > 0)
                            @foreach($players as $player)
                                @php
                                    $isBot = false;
                                    $playerName = 'Unknown Player';

                                    if (is_array($player)) {
                                        $isBot = ($player['isBot'] ?? $player['IsBot'] ?? false) === true;
                                        $playerName = $player['name'] ?? 'Unknown Player';
                                    } else {
                                        $playerName = $player;
                                    }

                                    $displayName = $isBot ? '(BOT) ' . $playerName : $playerName;
                                    $firstLetter = strtoupper(substr($playerName, 0, 1));
                                @endphp

                                <div
                                    class="bg-black/30 rounded p-4 hover:bg-black/40 transition-colors border-l-4 border-wreckfest shadow-lg">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-white font-bold">{{ $displayName }}</span>
                                    </div>
                                </div>
                            @endforeach
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="mt-4 text-gray-400 text-lg font-bold uppercase tracking-wide">No players currently
                                online</p>
                            <p class="mt-2 text-gray-500 text-sm font-semibold">Be the first to join!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Current Track Rotation Card -->
            <div class="metal-texture rounded shadow-2xl p-8 border-2 border-gray-700 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-black text-wreckfest uppercase tracking-wider"
                        style="font-family: Impact, 'Arial Black', sans-serif;">Track Rotation</h2>
                    @if($collectionName)
                        <span class="bg-gray-700 text-gray-300 font-semibold py-2 px-4 rounded border-2 border-gray-600 uppercase tracking-wider text-sm">
                            {{ $collectionName }}
                        </span>
                    @endif
                </div>

                <div class="space-y-2 flex-1 overflow-y-auto custom-scrollbar pr-2">
                    @if(is_array($trackRotation) && count($trackRotation) > 0)
                        @foreach($trackRotation as $index => $track)
                            @php
                                $isCurrent = isset($currentTrack) && ($track['track'] ?? '') === $currentTrack;
                                $trackId = $track['track'] ?? '';
                                $trackName = $track['trackName'] ?? 'Unknown Track';
                                $locationName = $track['locationName'] ?? '';
                                $gamemodeName = $track['gamemodeName'] ?? '';
                                $laps = $track['laps'] ?? null;
                                $bots = $track['bots'] ?? null;

                                // Generate image path (same logic as TrackBrowser)
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
                            <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            <p class="mt-4 text-gray-400 text-lg font-bold uppercase tracking-wide">No track rotation configured</p>
                            <p class="mt-2 text-gray-500 text-sm font-semibold">Configure tracks in the admin panel</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- End Players and Track Rotation Grid -->

        <!-- Refresh Notice -->
        <div class="mt-8 text-center">
            <p class="text-gray-500 text-sm font-semibold uppercase tracking-wide">
                <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Page refreshes automatically every 30 seconds
            </p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="metal-texture mt-12 border-t-4 border-wreckfest">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-400 text-sm font-semibold uppercase tracking-wider">
                Powered by Laravel + Filament • <span class="text-wreckfest">Wreckfest</span> Server Admin Panel
            </p>
        </div>
    </footer>
</div>

<!-- Auto-refresh script -->
<script>
    // Auto-refresh every 30 seconds
    setTimeout(function () {
        location.reload();
    }, 30000);
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #1a1a1a;
        border: 1px solid #333;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #a03d00 0%, #802f00 100%);
        border: 1px solid #a03d00;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #b84800;
    }
</style>
</body>
</html>
