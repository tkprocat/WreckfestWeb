<?php

namespace App\Filament\Pages;

use App\Services\WreckfestApiClient;
use Filament\Pages\Page;

class ServerLogs extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Server Logs';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.server-logs';

    public ?array $logs = null;

    public function mount(): void
    {
        $this->refreshLogs();
    }

    public function refreshLogs(): void
    {
        $apiClient = app(WreckfestApiClient::class);
        $this->logs = $apiClient->getLogFile(100);
    }
}
