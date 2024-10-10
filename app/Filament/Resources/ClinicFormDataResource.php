<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClinicFormDataResource\Pages;
use App\Filament\Resources\ClinicFormDataResource\RelationManagers;
use App\Models\ClinicFormData;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClinicFormDataResource extends Resource
{
    protected static ?string $model = ClinicFormData::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('clinic_id')
                    ->label('Clinic')
                    ->relationship('clinic', 'name')
                    ->required(),
                Forms\Components\Select::make('visit_id')
                    ->label('Visit')
                    ->relationship('visit', 'id')
                    ->required(),
                Forms\Components\Select::make('patient_id')
                    ->label('Patient')
                    ->relationship('patient', 'name')
                    ->required(),
                Forms\Components\Textarea::make('form_data')
                    ->label('Form Data')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('clinic.name')->label('Clinic'),
                Tables\Columns\TextColumn::make('visit.id')->label('Visit ID'),
                Tables\Columns\TextColumn::make('patient.name')->label('Patient'),
                Tables\Columns\TextColumn::make('created_at')->label('Submitted')->date(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListClinicFormData::route('/'),
            'create' => Pages\CreateClinicFormData::route('/create'),
            'edit' => Pages\EditClinicFormData::route('/{record}/edit'),
        ];
    }
}
