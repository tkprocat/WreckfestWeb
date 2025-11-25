<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Server Status Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Server Status</h3>

            @if($status)
                <div class="space-y-3">
                    {{-- Server Running Status --}}
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Status:</span>
                        <span class="flex items-center gap-2">
                            @if($status['isRunning'] ?? false)
                                <span class="flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-success-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-success-500"></span>
                                </span>
                                <span class="text-success-600 dark:text-success-400 font-semibold">Online</span>
                            @else
                                <span class="flex h-3 w-3 rounded-full bg-danger-500"></span>
                                <span class="text-danger-600 dark:text-danger-400 font-semibold">Offline</span>
                            @endif
                        </span>
                    </div>

                    {{-- Process ID --}}
                    @if(isset($status['processId']) && $status['processId'])
                        <div class="flex justify-between">
                            <span class="font-medium">Process ID:</span>
                            <span class="text-gray-600 dark:text-gray-400 font-mono">{{ $status['processId'] }}</span>
                        </div>
                    @endif

                    {{-- Uptime --}}
                    @if(isset($status['uptime']) && $status['uptime'])
                        <div class="flex justify-between">
                            <span class="font-medium">Uptime:</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $status['uptime'] }}</span>
                        </div>
                    @endif

                    {{-- Current Track --}}
                    @if(isset($status['currentTrack']) && $status['currentTrack'])
                        <div class="flex justify-between">
                            <span class="font-medium">Current Track:</span>
                            <span class="text-gray-600 dark:text-gray-400 text-sm">{{ $status['currentTrack'] }}</span>
                        </div>
                    @endif

                    {{-- OCR Enabled --}}
                    @if(isset($status['ocrEnabled']))
                        <div class="flex justify-between">
                            <span class="font-medium">OCR Tracking:</span>
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $status['ocrEnabled'] ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    @endif
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
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Graceful server operations using in-game commands. Server will wait for a safe moment to execute.
            </p>

            <div class="flex flex-wrap gap-4">
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

                <x-filament::button
                    wire:click="addBot"
                    color="info"
                    icon="heroicon-o-user-plus">
                    Add Bot
                </x-filament::button>
            </div>
        </div>

        {{-- Force Control Actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-2 border-red-500 dark:border-red-700">
            <h3 class="text-lg font-semibold mb-2 text-red-600 dark:text-red-400">Force Control</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                <strong>Warning:</strong> These actions immediately kill the server process. Use only when graceful methods fail.
            </p>

            <div class="flex gap-4">
                <x-filament::button
                    wire:click="forceStopServer"
                    color="danger"
                    icon="heroicon-o-x-circle"
                    wire:confirm="Are you sure? This will immediately kill the server process.">
                    Force Stop
                </x-filament::button>

                <x-filament::button
                    wire:click="forceRestartServer"
                    color="danger"
                    icon="heroicon-o-bolt"
                    wire:confirm="Are you sure? This will forcefully restart the server.">
                    Force Restart
                </x-filament::button>
            </div>
        </div>

        {{-- Custom Command Input --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Send Command</h3>
            <form wire:submit.prevent="sendCustomCommand" class="flex items-center gap-3">
                <label for="command" class="font-medium text-sm whitespace-nowrap">Command:</label>
                <input
                    type="text"
                    id="command"
                    wire:model="command"
                    placeholder="/bot, /restart, /message Hello, etc."
                    class="flex-1 px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                />
                <x-filament::button
                    type="submit"
                    color="primary"
                    icon="heroicon-o-paper-airplane">
                    Send
                </x-filament::button>
            </form>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Send any server command directly. Example: <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">/message Welcome!</code>
            </p>
        </div>

        {{-- Server Logs --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6"
             x-data="{
                channel: null,
                wsConnected: false,
                wsError: null,

                connectReverb() {
                    try {
                        // Subscribe to the server-console channel via Laravel Echo/Reverb
                        this.channel = window.Echo.channel('server-console');

                        this.channel.listen('.ConsoleLogReceived', (event) => {
                            console.log('Received console logs:', event.logs);

                            // Append all logs from the batch
                            event.logs.forEach(log => {
                                $wire.call('appendLog', log);
                            });
                        });

                        // Set connected state
                        this.wsConnected = true;
                        this.wsError = null;
                        console.log('Subscribed to server-console channel via Reverb');
                    } catch (error) {
                        console.error('Failed to connect to Reverb:', error);
                        this.wsError = error.message;
                        this.wsConnected = false;
                    }
                },

                disconnectReverb() {
                    if (this.channel) {
                        window.Echo.leave('server-console');
                        this.channel = null;
                        this.wsConnected = false;
                    }
                }
            }"
             x-init="connectReverb()"
             @beforeunload.window="disconnectReverb()">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-semibold">Server Logs</h3>
                    <span x-show="wsConnected" class="flex items-center gap-1 text-xs text-success-600 dark:text-success-400">
                        <span class="flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-success-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-success-500"></span>
                        </span>
                        Live
                    </span>
                    <span x-show="!wsConnected && !wsError" class="text-xs text-gray-500 dark:text-gray-400">
                        Connecting...
                    </span>
                    <span x-show="wsError" class="text-xs text-danger-600 dark:text-danger-400" x-text="wsError"></span>
                </div>
                <x-filament::button wire:click="refreshLogs" color="gray" size="sm">
                    Refresh Logs
                </x-filament::button>
            </div>

            @if($logs && is_array($logs) && count($logs) > 0)
                <div
                    x-data="{
                        scrollToBottom() {
                            this.$refs.logsContainer.scrollTop = this.$refs.logsContainer.scrollHeight;
                        }
                    }"
                    x-init="$nextTick(() => scrollToBottom())"
                    x-ref="logsContainer"
                    @logs-updated.window="$nextTick(() => scrollToBottom())"
                    @log-appended.window="$nextTick(() => scrollToBottom())"
                    class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm overflow-x-auto max-h-[600px] overflow-y-auto">
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
                <div
                    x-data="{
                        scrollToBottom() {
                            this.$refs.logsContainer.scrollTop = this.$refs.logsContainer.scrollHeight;
                        }
                    }"
                    x-init="$nextTick(() => scrollToBottom())"
                    x-ref="logsContainer"
                    @logs-updated.window="$nextTick(() => scrollToBottom())"
                    @log-appended.window="$nextTick(() => scrollToBottom())"
                    class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm overflow-x-auto max-h-[600px] overflow-y-auto whitespace-pre-wrap">{{ $logs }}</div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No logs available</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>
