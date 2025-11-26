<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deploySchedule')
                ->label('Deploy Event Schedule')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Deploy Event Schedule to Controller')
                ->modalDescription('This will push all upcoming events to the Wreckfest Controller so it can automatically activate them at the scheduled times.')
                ->modalSubmitActionLabel('Deploy Schedule')
                ->action(function () {
                    try {
                        // Use EventService to build and push the schedule (same as EventObserver)
                        $eventService = app(\App\Services\EventService::class);
                        $eventCount = $eventService->pushScheduleToController();

                        if ($eventCount >= 0) {
                            Notification::make()
                                ->title('Event schedule deployed')
                                ->body($eventCount . ' event(s) have been pushed to the Wreckfest Controller.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Failed to deploy event schedule')
                                ->body('Could not communicate with the Wreckfest Controller.')
                                ->danger()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
