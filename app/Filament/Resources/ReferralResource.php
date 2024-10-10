<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralResource\Pages;
use App\Models\Referral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class ReferralResource extends Resource
{
    protected static ?string $model = Referral::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('visit_id')
                    ->label('Visit')
                    ->relationship('visit', 'id')
                    ->required(),
                Forms\Components\Select::make('referred_from_id')
                    ->label('Referred From')
                    ->relationship('referredFrom', 'name')
                    ->required(),
                Forms\Components\Select::make('referred_to_id')
                    ->label('Referred To')
                    ->relationship('referredTo', 'name')
                    ->required(),
                Forms\Components\Textarea::make('reason')->label('Reason for Referral')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visit.id')->label('Visit ID'),
                Tables\Columns\TextColumn::make('referredFrom.name')->label('Referred From'),
                Tables\Columns\TextColumn::make('referredTo.name')->label('Referred To'),
                Tables\Columns\TextColumn::make('reason')->label('Reason for Referral')->limit(50),
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
            'index' => Pages\ListReferrals::route('/'),
            'create' => Pages\CreateReferral::route('/create'),
            'edit' => Pages\EditReferral::route('/{record}/edit'),
        ];
    }
}
