<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages\CreateEvent;
use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Filament\Resources\EventResource\Pages\ListEvents;
use App\Models\Event;
use App\Models\TrackCollection;
use App\Services\WreckfestApiClient;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Events';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->nullable()
                            ->columnSpanFull(),

                        DateTimePicker::make('start_time')
                            ->required()
                            ->native(false)
                            ->seconds(false)
                            ->label('Start Date & Time (Copenhagen)')
                            ->helperText('Enter time in Copenhagen timezone - stored as UTC')
                            ->timezone('Europe/Copenhagen')
                            ->displayFormat('M d, Y H:i'),

                        Select::make('track_collection_id')
                            ->label('Track Rotation')
                            ->relationship('trackCollection', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Collection Name'),
                            ]),
                    ])
                    ->columns(2),

                Section::make('Server Configuration')
                    ->description('Server settings that will be applied when this event activates')
                    ->schema([
                        TextInput::make('server_config.serverName')
                            ->label('Server Name (supports color codes)')
                            ->maxLength(255)
                            ->suffixIcon('heroicon-o-paint-brush')
                            ->view('components.wreckfest-text-input'),

                        \Filament\Forms\Components\Textarea::make('server_config.welcomeMessage')
                            ->label('Welcome Message (supports color codes)')
                            ->rows(3)
                            ->view('components.wreckfest-textarea-input'),

                        TextInput::make('server_config.serverPassword')
                            ->label('Server Password')
                            ->password()
                            ->maxLength(255),
                    ])
                    ->collapsible(),

                Section::make('Recurring Pattern')
                    ->description('Optional: Make this event repeat automatically')
                    ->schema([
                        Select::make('recurring_pattern.type')
                            ->label('Frequency')
                            ->options([
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                            ])
                            ->reactive(),

                        Select::make('recurring_pattern.days')
                            ->label('Days of Week')
                            ->options([
                                1 => 'Monday',
                                2 => 'Tuesday',
                                3 => 'Wednesday',
                                4 => 'Thursday',
                                5 => 'Friday',
                                6 => 'Saturday',
                                0 => 'Sunday',
                            ])
                            ->multiple()
                            ->visible(fn ($get) => $get('recurring_pattern.type') === 'weekly'),

                        TextInput::make('recurring_pattern.time')
                            ->label('Time')
                            ->placeholder('20:00')
                            ->helperText('Format: HH:MM'),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Start Time')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                TextColumn::make('trackCollection.name')
                    ->label('Track Rotation')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_time', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('activate')
                    ->label('Activate Now')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Event $record, WreckfestApiClient $apiClient) {
                        try {
                            $success = $apiClient->activateEvent($record->id);

                            if ($success) {
                                Notification::make()
                                    ->title('Event activation initiated')
                                    ->body('The C# controller will activate this event shortly.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Failed to activate event')
                                    ->body('Could not communicate with the C# controller.')
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

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
