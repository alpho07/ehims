<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DrugResource\Pages;
use App\Filament\Resources\DrugResource\RelationManagers;
use App\Models\Drug;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DrugResource extends Resource
{
    protected static ?string $model = Drug::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('drug_name')
                    ->required()
                    ->rules('string|max:255')
                    ->helperText('Enter the full name of the drug as labeled by the manufacturer.'),

                Select::make('drug_category')
                    ->options([
                        'antibiotic' => 'Antibiotic',
                        'analgesic' => 'Analgesic',
                        'antihypertensive' => 'Antihypertensive',
                        'anti_inflammatory' => 'Anti-inflammatory',
                        'antihistamine' => 'Antihistamine',
                        'antiviral' => 'Antiviral',
                        'antifungal' => 'Antifungal',
                        'mydriatic' => 'Mydriatic',
                        'glaucoma_medication' => 'Glaucoma Medication',
                        'lubricant' => 'Lubricant (Artificial Tears)',
                        'steroid' => 'Steroid',
                        'decongestant' => 'Decongestant',
                    ])
                    ->required()
                    ->searchable() // This makes the dropdown searchable
                    ->rules('in:antibiotic,analgesic,antihypertensive,anti_inflammatory,antihistamine,antiviral,antifungal,mydriatic,glaucoma_medication,lubricant,steroid,decongestant')
                    ->helperText('Select the appropriate category for this drug.'),

                Select::make('dosage_form')
                    ->options([
                        'tablet' => 'Tablet',
                        'capsule' => 'Capsule',
                        'eye_drops' => 'Eye Drops',
                        'eye_ointment' => 'Eye Ointment',
                        'ophthalmic_suspension' => 'Ophthalmic Suspension',
                        'ophthalmic_gel' => 'Ophthalmic Gel',
                        'ophthalmic_solution' => 'Ophthalmic Solution',
                        'ophthalmic_emulsion' => 'Ophthalmic Emulsion',
                        'injectable' => 'Injectable',
                        'implant' => 'Implant',
                        'powder_for_solution' => 'Powder for Ophthalmic Solution',
                    ])
                    ->required()
                    ->searchable() // This makes the dropdown searchable
                    ->rules('in:tablet,capsule,eye_drops,eye_ointment,ophthalmic_suspension,ophthalmic_gel,ophthalmic_solution,ophthalmic_emulsion,injectable,implant,powder_for_solution')
                    ->helperText('Specify the dosage form of the drug (e.g., Eye Drops, Capsule).'),

                TextInput::make('dosage_strength')
                    ->required()
                    ->rules('string|max:50')
                    ->helperText('Provide the strength of the drug (e.g., 0.5%, 500mg).'),

                TextInput::make('manufacturer')
                    ->required()
                    ->rules('string|max:255')
                    ->helperText('Enter the name of the drug’s manufacturer.'),

                TextInput::make('batch_number')
                    ->required()
                    ->rules('string|max:50')
                    ->helperText('Provide the batch or lot number as indicated on the packaging.'),

                DatePicker::make('expiry_date')
                    ->required()
                    ->rules('date|after:today')
                    ->helperText('Select the drug’s expiry date from the calendar. The date must be in the future.'),

                TextInput::make('quantity_in_stock')
                    ->required()
                    ->rules('integer|min:1')
                    ->helperText('Enter the current quantity of this drug available in stock. It must be at least 1.'),

                TextInput::make('reorder_level')
                    ->required()
                    ->rules('integer|min:1')
                    ->helperText('Enter the stock level at which a reorder should be triggered. It must be at least 1.'),

                TextInput::make('reorder_quantity')
                    ->required()
                    ->rules('integer|min:1')
                    ->helperText('Specify how much to reorder once stock reaches the reorder level. It must be at least 1.'),

                Textarea::make('storage_conditions')
                    ->required()
                    ->rules('string|max:500')
                    ->helperText('Provide the specific storage conditions (e.g., "Store below 25°C, in a dry place"). Maximum 500 characters.'),

                TextInput::make('price')
                    ->required()
                    ->rules('numeric|min:0')
                    ->helperText('Enter the price per unit of the drug. Must be a positive number.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('drug_name')->sortable()->searchable(),
                TextColumn::make('drug_category')->sortable(),
                TextColumn::make('quantity_in_stock')->sortable(),
                TextColumn::make('price')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListDrugs::route('/'),
            'create' => Pages\CreateDrug::route('/create'),
            'edit' => Pages\EditDrug::route('/{record}/edit'),
        ];
    }
}
