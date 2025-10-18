<?php

namespace App\Filament\Pages;

use App\Models\TrackCollection;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageCollections extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Track Collections';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.manage-collections';
    protected static ?string $title = 'Track Collections';

    public function table(Table $table): Table
    {
        return $table
            ->query(TrackCollection::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Collection Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tracks')
                    ->label('Number of Tracks')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) : 0)
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderByRaw('JSON_LENGTH(tracks) ' . $direction);
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('name')
                            ->label('Collection Name')
                            ->required()
                            ->maxLength(255),
                    ]),
                DeleteAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('newCollection')
                ->label('New Collection')
                ->icon('heroicon-o-document-plus')
                ->form([
                    TextInput::make('name')
                        ->label('Collection Name')
                        ->required()
                        ->placeholder('e.g., Racing Only, Mixed Modes')
                        ->maxLength(255)
                        ->default('New Collection'),
                ])
                ->action(function (array $data): void {
                    TrackCollection::create([
                        'name' => $data['name'],
                        'tracks' => [],
                    ]);

                    Notification::make()
                        ->title('Collection created')
                        ->body("Created: {$data['name']}")
                        ->success()
                        ->send();
                })
                ->color('primary'),
        ];
    }
}
