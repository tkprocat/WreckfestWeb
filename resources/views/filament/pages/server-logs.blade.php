<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Controls --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex justify-end">
                <x-filament::button wire:click="refreshLogs" color="gray">
                    Refresh
                </x-filament::button>
            </div>
        </div>

        {{-- Logs Display --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Server Logs</h3>

            @if($logs && is_array($logs) && count($logs) > 0)
                <div class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm overflow-x-auto max-h-[600px] overflow-y-auto">
                    @foreach($logs as $log)
                        <div class="hover:bg-gray-800">
                            @if(is_array($log))
                                {{ json_encode($log) }}
                            @else
                                {{ $log }}
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif($logs && is_string($logs))
                <div class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm overflow-x-auto max-h-[600px] overflow-y-auto whitespace-pre-wrap">{{ $logs }}</div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No logs available</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>
