<?php

namespace App\Filament\Pages;

use App\Exceptions\WreckfestApiException;
use App\Services\WreckfestApiClient;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ServerControl extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-server';

    protected static ?string $navigationLabel = 'Server Control';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.server-control';

    public ?array $status = null;

    public ?array $logs = null;

    public string $command = '';

    public function mount(): void
    {
        $this->refreshStatus();
        $this->refreshLogs();
    }

    public function refreshStatus(): void
    {
        try {
            $apiClient = app(WreckfestApiClient::class);
            $this->status = $apiClient->getServerStatus();
        } catch (WreckfestApiException $e) {
            Notification::make()
                ->title('Unable to contact Wreckfest Controller')
                ->body('Please ensure the Wreckfest API is running and accessible.')
                ->danger()
                ->send();

            $this->status = null;
        }
    }

    public function refreshLogs(): void
    {
        try {
            $apiClient = app(WreckfestApiClient::class);
            $this->logs = $apiClient->getLogFile(100);

            // Dispatch event to trigger auto-scroll in the frontend
            $this->dispatch('logs-updated');
        } catch (WreckfestApiException $e) {
            Notification::make()
                ->title('Unable to fetch server logs')
                ->body('Please ensure the Wreckfest API is running and accessible.')
                ->danger()
                ->send();

            $this->logs = null;
        }
    }

    public function startServer(): void
    {
        $apiClient = app(WreckfestApiClient::class);

        if ($apiClient->startServer()) {
            Notification::make()
                ->title('Server start command sent')
                ->success()
                ->send();

            $this->refreshStatus();
        } else {
            Notification::make()
                ->title('Failed to start server')
                ->danger()
                ->send();
        }
    }

    public function stopServer(): void
    {
        $apiClient = app(WreckfestApiClient::class);

        if ($apiClient->stopServer()) {
            Notification::make()
                ->title('Server stop command sent')
                ->success()
                ->send();

            $this->refreshStatus();
        } else {
            Notification::make()
                ->title('Failed to stop server')
                ->danger()
                ->send();
        }
    }

    public function restartServer(): void
    {
        $apiClient = app(WreckfestApiClient::class);

        if ($apiClient->restartServer()) {
            Notification::make()
                ->title('Server restart command sent')
                ->success()
                ->send();

            $this->refreshStatus();
        } else {
            Notification::make()
                ->title('Failed to restart server')
                ->danger()
                ->send();
        }
    }

    public function forceStopServer(): void
    {
        $apiClient = app(WreckfestApiClient::class);

        if ($apiClient->forceStopServer()) {
            Notification::make()
                ->title('Server force stop command sent')
                ->body('Server process was terminated immediately.')
                ->success()
                ->send();

            $this->refreshStatus();
        } else {
            Notification::make()
                ->title('Failed to force stop server')
                ->danger()
                ->send();
        }
    }

    public function forceRestartServer(): void
    {
        $apiClient = app(WreckfestApiClient::class);

        if ($apiClient->forceRestartServer()) {
            Notification::make()
                ->title('Server force restart command sent')
                ->body('Server was forcefully stopped and restarted.')
                ->success()
                ->send();

            $this->refreshStatus();
        } else {
            Notification::make()
                ->title('Failed to force restart server')
                ->danger()
                ->send();
        }
    }

    public function addBot(): void
    {
        $apiClient = app(WreckfestApiClient::class);

        if ($apiClient->addBot()) {
            Notification::make()
                ->title('Bot added')
                ->body('AI bot has been added to the server.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to add bot')
                ->body('Make sure the server is running.')
                ->danger()
                ->send();
        }
    }

    public function sendCustomCommand(): void
    {
        if (empty($this->command)) {
            Notification::make()
                ->title('Command required')
                ->body('Please enter a command to send.')
                ->warning()
                ->send();

            return;
        }

        $apiClient = app(WreckfestApiClient::class);

        if ($apiClient->sendCommand($this->command)) {
            Notification::make()
                ->title('Command sent')
                ->body("Command '{$this->command}' has been sent to the server.")
                ->success()
                ->send();

            // Clear the command input after successful send
            $this->command = '';
        } else {
            Notification::make()
                ->title('Failed to send command')
                ->body('Make sure the server is running.')
                ->danger()
                ->send();
        }
    }

    public function appendLog(string $logLine): void
    {
        // Initialize logs as array if null
        if ($this->logs === null) {
            $this->logs = [];
        }

        // Append the new log line
        $this->logs[] = $logLine;

        // Keep only the last 500 lines to prevent memory issues
        if (count($this->logs) > 500) {
            $this->logs = array_slice($this->logs, -500);
        }

        // Dispatch event to trigger auto-scroll in the frontend
        $this->dispatch('log-appended');
    }
}
