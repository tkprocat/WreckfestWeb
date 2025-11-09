<?php

namespace App\Filament\Pages;

use App\Exceptions\WreckfestApiException;
use App\Services\WreckfestApiClient;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ServerLogs extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Server Logs';

    protected static ?int $navigationSort = 4;

    // Hide from navigation - logs are now integrated into Server Control page
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.server-logs';

    public ?array $logs = null;

    public function mount(): void
    {
        $this->refreshLogs();
    }

    public function refreshLogs(): void
    {
        try {
            $apiClient = app(WreckfestApiClient::class);
            $this->logs = $apiClient->getLogFile(100);
        } catch (WreckfestApiException $e) {
            Notification::make()
                ->title('Unable to contact Wreckfest Controller')
                ->body('Please ensure the Wreckfest API is running and accessible.')
                ->danger()
                ->send();

            $this->logs = null;
        }
    }
}
