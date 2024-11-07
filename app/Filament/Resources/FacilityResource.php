<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacilityResource\Pages;
use App\Filament\Resources\FacilityResource\RelationManagers;
use App\Models\Facility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacilityResource extends Resource
{
    protected static ?string $model = Facility::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Admin Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('facility_name')
                    ->required()
                    ->label('Facility Name'),
                Forms\Components\TextInput::make('mfl_code')
                    ->required()
                    ->unique(Facility::class, 'mfl_code', ignoreRecord: true)
                    ->label('MFL Code'),
                Forms\Components\TextInput::make('county')
                    ->required(),
                Forms\Components\TextInput::make('subcounty')
                    ->required(),
                Forms\Components\TextInput::make('ward')
                    ->required(),
                Forms\Components\Select::make('facility_type')
                    ->options([
                        'hub' => 'Hub',
                        'spoke' => 'Spoke',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state === 'hub') {
                            $set('parent_id', null);
                        }
                    }),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Hub')
                    ->options(Facility::where('facility_type', 'hub')->pluck('facility_name', 'id'))
                    ->searchable()
                    ->visible(fn($get) => $get('facility_type') === 'spoke')
                    ->required(fn($get) => $get('facility_type') === 'spoke'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('facility_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('mfl_code')->sortable(),
                Tables\Columns\TextColumn::make('facility_type')->sortable(),
                Tables\Columns\TextColumn::make('parent.facility_name')->label('Parent Hub')->sortable(),
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
            'index' => Pages\ListFacilities::route('/'),
            'create' => Pages\CreateFacility::route('/create'),
            'edit' => Pages\EditFacility::route('/{record}/edit'),
        ];
    }
}
