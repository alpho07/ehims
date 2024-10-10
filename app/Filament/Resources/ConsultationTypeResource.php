<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultationTypeResource\Pages;
use App\Filament\Resources\ConsultationTypeResource\RelationManagers;
use App\Models\ConsultationType;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConsultationTypeResource extends Resource
{
    protected static ?string $model = ConsultationType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Consultation Type Name')
                    ->unique() // Ensure each consultation type is unique
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Consultation Type Name'),
                TextColumn::make('description')->label('Description'),
                BooleanColumn::make('is_active')->label('Active Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListConsultationTypes::route('/'),
            'create' => Pages\CreateConsultationType::route('/create'),
            'edit' => Pages\EditConsultationType::route('/{record}/edit'),
        ];
    }
}
