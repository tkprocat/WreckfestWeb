<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Server Status Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Server Status</h3>

            @if($status)
                <div class="space-y-2">
                    @foreach($status as $key => $value)
                        <div class="flex justify-between">
                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                            <span class="text-gray-600 dark:text-gray-400">
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
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">Unable to fetch server status</p>
            @endif

            <div class="mt-4">
                <x-filament::button wire:click="refreshStatus" color="gray">
                    Refresh Status
                </x-filament::button>
            </div>
        </div>

        {{-- Server Control Actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Server Control</h3>

            <div class="flex gap-4">
                <x-filament::button
                    wire:click="startServer"
                    color="success"
                    icon="heroicon-o-play">
                    Start Server
                </x-filament::button>

                <x-filament::button
                    wire:click="stopServer"
                    color="danger"
                    icon="heroicon-o-stop">
                    Stop Server
                </x-filament::button>

                <x-filament::button
                    wire:click="restartServer"
                    color="warning"
                    icon="heroicon-o-arrow-path">
                    Restart Server
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
