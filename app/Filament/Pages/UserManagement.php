<?php

namespace App\Filament\Pages;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserManagement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'User Management';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.user-management';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ViewAction::make()
                    ->schema([
                        TextInput::make('name')
                            ->disabled(),
                        TextInput::make('email')
                            ->disabled(),
                        TextInput::make('email_verified_at')
                            ->label('Email Verified At')
                            ->disabled(),
                        TextInput::make('created_at')
                            ->label('Created At')
                            ->disabled(),
                    ]),
                EditAction::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->revealable()
                            ->confirmed()
                            ->minLength(8)
                            ->maxLength(255)
                            ->helperText('Leave blank to keep current password'),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->dehydrated(false)
                            ->revealable()
                            ->maxLength(255),
                    ])
                    ->mutateRecordDataUsing(function (array $data): array {
                        // Hash password if provided
                        if (!empty($data['password'])) {
                            $data['password'] = Hash::make($data['password']);
                        } else {
                            unset($data['password']);
                        }
                        unset($data['password_confirmation']);

                        return $data;
                    }),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => $record->id !== Auth::id()),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createUser')
                ->label('New User')
                ->icon('heroicon-o-user-plus')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(User::class, 'email')
                        ->maxLength(255),
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->revealable()
                        ->confirmed()
                        ->minLength(8)
                        ->maxLength(255),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->required()
                        ->dehydrated(false)
                        ->revealable()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                        'email_verified_at' => now(), // Auto-verify manually created users
                    ]);

                    Notification::make()
                        ->title('User created')
                        ->body("User {$data['name']} has been created successfully.")
                        ->success()
                        ->send();
                })
                ->color('primary'),
            Action::make('sendInvite')
                ->label('Send Invitation')
                ->icon('heroicon-o-envelope')
                ->schema([
                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->placeholder('user@example.com'),
                ])
                ->action(function (array $data): void {
                    // Check if email is already registered
                    if (User::where('email', $data['email'])->exists()) {
                        Notification::make()
                            ->title('User already exists')
                            ->body('A user with this email address is already registered.')
                            ->danger()
                            ->send();

                        return;
                    }

                    // Check if there's already a pending invitation
                    $existingInvite = Invitation::where('email', $data['email'])
                        ->whereNull('accepted_at')
                        ->where('expires_at', '>', now())
                        ->first();

                    if ($existingInvite) {
                        Notification::make()
                            ->title('Invitation already sent')
                            ->body('This email already has a pending invitation.')
                            ->warning()
                            ->send();

                        return;
                    }

                    // Create invitation
                    $invitation = Invitation::create([
                        'email' => $data['email'],
                        'token' => Invitation::generateToken(),
                        'invited_by' => Auth::id(),
                        'expires_at' => now()->addDays(7),
                    ]);

                    // Send email
                    try {
                        Mail::to($invitation->email)
                            ->send(new InvitationMail($invitation));

                        Notification::make()
                            ->title('Invitation sent')
                            ->body("An invitation has been sent to {$data['email']}")
                            ->success()
                            ->send();
                    } catch (Exception $e) {
                        $invitation->delete();

                        Notification::make()
                            ->title('Failed to send invitation')
                            ->body('Could not send the invitation email. Please check your email configuration.')
                            ->danger()
                            ->send();
                    }
                })
                ->color('success'),
        ];
    }

    public function getSubheading(): ?string
    {
        $userCount = User::count();
        $pendingInvites = Invitation::whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->count();

        return "{$userCount} users • {$pendingInvites} pending invitations";
    }
}
