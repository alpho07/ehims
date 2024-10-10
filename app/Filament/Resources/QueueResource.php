<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QueueResource\Pages;
use App\Models\Queue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;



class QueueResource extends Resource
{
    protected static ?string $model = Queue::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('clinic_id')
                    ->label('Clinic')
                    ->relationship('clinic', 'name')
                    ->required(),
                Forms\Components\Select::make('patient_id')
                    ->label('Patient')
                    ->relationship('patient', 'name')
                    ->required(),
                Forms\Components\TextInput::make('position')->numeric()->required(),
                Forms\Components\TextInput::make('status')->default('waiting')->label('Queue Status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('clinic.name')->label('Clinic'),
                Tables\Columns\TextColumn::make('patient.name')->label('Patient'),
                Tables\Columns\TextColumn::make('position')->label('Queue Position'),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQueues::route('/'),
            'create' => Pages\CreateQueue::route('/create'),
            'edit' => Pages\EditQueue::route('/{record}/edit'),
        ];
    }
}
