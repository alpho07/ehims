<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Facility;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Admin Management';

    public static function shouldRegisterNavigation(): bool
    {
        // Check if the user has permission to view any appointments
        return Auth::user()->can('view_any_user');
    }




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->required(),
                //Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()

                    ->maxLength(255)
                    ->same('password_confirmation')
                    ->required(fn($record) => $record === null)
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                    ->visible(fn($record) => $record === null), // Hide password field when editing

                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->label('Confirm Password')
                    ->required(fn($record) => $record === null)
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                    ->visible(fn($record) => $record === null), // Hide password field when editing
                Forms\Components\Select::make('roles')
                    //->multiple() // Allow multiple role selection
                    ->relationship('roles', 'id') // Specify role_id as the foreign key
                    ->options(Role::all()->pluck('name', 'id')->toArray()) // Get available roles                    ->preload() // Load options in advance for smoother UI
                    ->label('Roles')
                    ->default(
                        fn($record) => $record
                            ? $record->roles->pluck('id')->toArray() // Load role IDs for editing
                            : []
                    ),
                Forms\Components\Select::make('facility_id')
                    ->label('Facility')
                    ->options(Facility::pluck('facility_name', 'id'))
                    ->searchable()
                    ->required(),

            ]);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Here we ensure the roles are passed correctly during save
        return $data;
    }

    // Invoked automatically after saving
    public static function saved(User $user, array $data): void
    {

        if (isset($data['roles'])) {
            // Convert role IDs to role names
            $roleNames = Role::whereIn('id', $data['roles'])->pluck('name')->toArray();

            // Sync roles using role names (Spatie requires role names)
            $user->syncRoles($roleNames);
        }
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name') // Display assigned roles
                    ->label('Roles')
                    ->getStateUsing(function (User $record) {
                        return $record->roles->pluck('name')->implode(', ');
                    }),

                    Tables\Columns\TextColumn::make('facility.facility_name')->label('Facility'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
