<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        // Handle occurrence_type -> repeat.frequency mapping
        $occurrenceType = $data['occurrence_type'] ?? 'single';

        if ($occurrenceType === 'single') {
            $data['repeat'] = null;
        } else {
            // Ensure repeat array exists and set frequency
            $data['repeat'] = $data['repeat'] ?? [];
            $data['repeat']['frequency'] = $occurrenceType;
        }

        // Remove the virtual field
        unset($data['occurrence_type']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
