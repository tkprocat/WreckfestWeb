<?php

namespace App\Filament\Pages;

use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationEmail;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserManagement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'User Management';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.user-management';

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
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => $record->id !== Auth::id()),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendInvite')
                ->label('Send Invitation')
                ->icon('heroicon-o-envelope')
                ->form([
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
                        \Illuminate\Support\Facades\Mail::to($invitation->email)
                            ->send(new \App\Mail\InvitationMail($invitation));

                        Notification::make()
                            ->title('Invitation sent')
                            ->body("An invitation has been sent to {$data['email']}")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
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
