<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $serverName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
            background-image:
                repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255, 255, 255, 0.03) 2px, rgba(255, 255, 255, 0.03) 4px),
                linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
        }
        .metal-texture {
            background: linear-gradient(145deg, #2a2a2a, #1f1f1f);
            box-shadow:
                inset 2px 2px 5px rgba(0, 0, 0, 0.5),
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
                        <h1 class="text-4xl font-black text-wreckfest uppercase tracking-wider" style="font-family: Impact, 'Arial Black', sans-serif;">{{ $serverName }}</h1>
                        <p class="text-gray-400 mt-1 font-semibold tracking-wide">WRECKFEST DEDICATED SERVER</p>
                    </div>
                    @auth
                        <a href="/admin" class="bg-wreckfest-dark hover:bg-wreckfest text-white font-semibold py-3 px-8 rounded transition duration-200 shadow-lg uppercase tracking-wider border-2 border-wreckfest glow-orange button-text-shadow">
                            Admin
                        </a>
                    @else
                        <a href="/admin/login" class="bg-wreckfest-dark hover:bg-wreckfest text-white font-semibold py-3 px-8 rounded transition duration-200 shadow-lg uppercase tracking-wider border-2 border-wreckfest glow-orange button-text-shadow">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Server Status Card -->
                <div class="metal-texture rounded shadow-2xl p-8 border-2 border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-black text-wreckfest uppercase tracking-wider" style="font-family: Impact, 'Arial Black', sans-serif;">Server Status</h2>
                        @if(!empty($serverStatus))
                            <span class="flex items-center">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                </span>
                                <span class="ml-2 text-green-400 font-black uppercase tracking-wide">Online</span>
                            </span>
                        @else
                            <span class="flex items-center">
                                <span class="relative flex h-3 w-3 rounded-full bg-red-500"></span>
                                <span class="ml-2 text-red-400 font-black uppercase tracking-wide">Offline</span>
                            </span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @if(!empty($serverStatus))
                            @foreach($serverStatus as $key => $value)
                                <div class="flex justify-between items-center py-3 border-b border-gray-700">
                                    <span class="text-gray-400 font-semibold uppercase text-sm tracking-wide">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                    <span class="text-white font-bold">
                                        @if(is_bool($value))
                                            {{ $value ? 'Yes' : 'No' }}
                                        @elseif(is_array($value))
                                            {{ json_encode($value) }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-4 text-gray-500 font-semibold">Unable to connect to server</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Current Players Card -->
                <div class="metal-texture rounded shadow-2xl p-8 border-2 border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-black text-wreckfest uppercase tracking-wider" style="font-family: Impact, 'Arial Black', sans-serif;">Current Players</h2>
                        <span class="bg-wreckfest-dark text-white font-semibold py-2 px-5 rounded border-2 border-wreckfest uppercase tracking-wider button-text-shadow">
                            {{ is_array($players) ? count($players) : 0 }} / {{ $maxPlayers }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        @if(is_array($players) && count($players) > 0)
                            <div class="max-h-96 overflow-y-auto space-y-3 pr-2 custom-scrollbar">
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

                                    <div class="bg-black/30 rounded p-4 hover:bg-black/40 transition-colors border-l-4 border-wreckfest shadow-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded flex items-center justify-center text-white font-semibold shadow-lg button-text-shadow" style="background: linear-gradient(135deg, #a03d00 0%, #802f00 100%);">
                                                {{ $firstLetter }}
                                            </div>
                                            <span class="text-white font-bold">{{ $displayName }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="mt-4 text-gray-400 text-lg font-bold uppercase tracking-wide">No players currently online</p>
                                <p class="mt-2 text-gray-500 text-sm font-semibold">Be the first to join!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Refresh Notice -->
            <div class="mt-8 text-center">
                <p class="text-gray-500 text-sm font-semibold uppercase tracking-wide">
                    <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
        setTimeout(function() {
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
