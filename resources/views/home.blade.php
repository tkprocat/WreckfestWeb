<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wreckfest Web</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/js/app.js'])
    @livewireStyles
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
                @livewire('server-name')
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
        <!-- Server Status Badge -->
        @livewire('server-status')

        <!-- Players and Track Rotation Grid -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Current Players Card -->
            @livewire('player-list')

            <!-- Current Track Rotation Card -->
            @livewire('track-rotation')
        </div>
        <!-- End Players and Track Rotation Grid -->

        <!-- Refresh Notice -->
        <div class="mt-8 text-center">
            <p class="text-gray-500 text-sm font-semibold uppercase tracking-wide">
                <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Real-time updates enabled
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
@livewireScripts
</body>
</html>
