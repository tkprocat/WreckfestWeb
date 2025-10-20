<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header with Refresh Button -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Wreckfest Server Configuration</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Plain text configuration matching server_config.cfg format</p>
            </div>
            <x-filament::button wire:click="refresh" icon="heroicon-o-arrow-path" color="gray">
                Refresh
            </x-filament::button>
        </div>

        <!-- Plain Text Configuration -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                        server_config.cfg
                    </h3>
                    <button
                        onclick="navigator.clipboard.writeText(document.getElementById('config-text').innerText)"
                        class="text-xs px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded transition"
                        title="Copy to clipboard"
                    >
                        Copy
                    </button>
                </div>
            </div>
            <div class="p-0">
                @if(!empty($plainTextConfig))
                    <pre id="config-text" class="p-6 text-sm font-mono text-gray-800 dark:text-gray-200 overflow-x-auto bg-gray-50 dark:bg-gray-900"><code>{{ $plainTextConfig }}</code></pre>
                @else
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No configuration available</p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Unable to retrieve server configuration from the API.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Track Rotation Count -->
        @if(!empty($trackRotation))
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        Track rotation contains <strong>{{ count($trackRotation) }}</strong> track{{ count($trackRotation) !== 1 ? 's' : '' }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- API Info Note -->
        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        This configuration is retrieved from the Wreckfest API at <code class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-800 rounded text-xs">{{ config('wreckfest.api_url', 'https://localhost:5101/api') }}</code>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
