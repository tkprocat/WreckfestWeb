<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CurrentPlayersWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string $routePath = '/';

    public function getWidgets(): array
    {
        return [
            CurrentPlayersWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }
}
