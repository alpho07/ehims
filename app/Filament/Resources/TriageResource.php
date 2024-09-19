<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TriageResource\Pages;
use App\Filament\Resources\TriageResource\RelationManagers;
use App\Models\Triage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TriageResource extends Resource
{
    protected static ?string $model = Triage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')->required(),
                TimePicker::make('time')->required(),
                TextInput::make('temperature')->numeric()->required(),
                TextInput::make('pulse')->numeric()->required(),
                TextInput::make('respiratory_rate')->numeric()->required(),
                TextInput::make('bp_systolic')->numeric()->required(),
                TextInput::make('bp_diastolic')->numeric()->required(),
                TextInput::make('visual_acuity')->required(),
                TextInput::make('iop')->numeric()->required(),
                TextInput::make('nurse_name')->required(),
                TextInput::make('signature')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListTriages::route('/'),
            'create' => Pages\CreateTriage::route('/create'),
            'edit' => Pages\EditTriage::route('/{record}/edit'),
        ];
    }
}
