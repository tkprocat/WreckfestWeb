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

    public function mount(): void
    {
        $this->refreshStatus();
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
}
