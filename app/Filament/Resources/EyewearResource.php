<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EyewearResource\Pages;
use App\Filament\Resources\EyewearResource\RelationManagers;
use App\Models\Eyewear;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
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

class EyewearResource extends Resource
{
    protected static ?string $model = Eyewear::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->rules('string|max:255')
                    ->helperText('Enter the name of the eyewear or lens.'),

                TextInput::make('prescription')
                    ->required()
                    ->rules('string|max:500')
                    ->helperText('Enter the lens prescription details (e.g., SPH, CYL, AXIS, ADD).'),

                TextInput::make('pupillary_distance')
                    ->required()
                    ->rules('numeric|min:0')
                    ->helperText('Enter the pupillary distance in millimeters (e.g., 60mm). It should be a positive number.'),

                Select::make('lens_type')
                    ->options([
                        'single_vision' => 'Single Vision',
                        'bifocal' => 'Bifocal',
                        'progressive' => 'Progressive',
                    ])
                    ->required()
                    ->searchable()
                    ->rules('in:single_vision,bifocal,progressive')
                    ->helperText('Select the type of lens required.'),

                Select::make('lens_material')
                    ->options([
                        'plastic' => 'Plastic',
                        'polycarbonate' => 'Polycarbonate',
                        'high_index' => 'High-Index',
                    ])
                    ->required()
                    ->searchable()
                    ->rules('in:plastic,polycarbonate,high_index')
                    ->helperText('Choose the lens material based on the prescription or customer preference.'),

                Select::make('lens_coating')
                    ->options([
                        'anti_reflective' => 'Anti-Reflective',
                        'uv_protection' => 'UV Protection',
                        'scratch_resistant' => 'Scratch-Resistant',
                    ])
                    ->required()
                    ->searchable()
                    ->rules('in:anti_reflective,uv_protection,scratch_resistant')
                    ->helperText('Select the appropriate coating for the lenses.'),

                Select::make('frame_style')
                    ->options([
                        'full_rim' => 'Full Rim',
                        'half_rim' => 'Half Rim',
                        'rimless' => 'Rimless',
                    ])
                    ->required()
                    ->searchable()
                    ->helperText('Select the frame style for the eyewear (e.g., Full Rim, Aviator).'),

                Select::make('frame_material')
                    ->options([
                        'metal' => 'Metal',
                        'plastic' => 'Plastic',
                        'acetate' => 'Acetate',
                    ])
                    ->required()
                    ->searchable()
                    ->helperText('Select the material used for the frame (e.g., Metal, Plastic).'),

                    ColorPicker::make('frame_color')
                    ->required()
                    ->rules('string|max:50')
                    ->helperText('Select the color of the frame using the color picker.'),

                TextInput::make('bridge_size')
                    ->required()
                    ->rules('numeric|min:0')
                    ->helperText('Enter the bridge size in millimeters (e.g., 18mm).'),

                TextInput::make('temple_length')
                    ->required()
                    ->rules('numeric|min:0')
                    ->helperText('Enter the temple length in millimeters (e.g., 140mm).'),

                TextInput::make('lens_width')
                    ->required()
                    ->rules('numeric|min:0')
                    ->helperText('Enter the width of the lenses in millimeters (e.g., 55mm).'),

                TextInput::make('lens_height')
                    ->required()
                    ->rules('numeric|min:0')
                    ->helperText('Enter the height of the lenses in millimeters (e.g., 40mm).'),

                Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'unisex' => 'Unisex',
                    ])
                    ->required()
                    ->searchable()
                    ->helperText('Select whether the glasses are designed for men, women, or are unisex.'),

                TextInput::make('brand')
                    ->required()
                    ->rules('string|max:255')
                    ->helperText('Enter the brand or manufacturer of the eyewear.'),

                Select::make('frame_size')
                    ->options([
                        'small' => 'Small',
                        'medium' => 'Medium',
                        'large' => 'Large',
                    ])
                    ->required()
                    ->searchable()
                    ->helperText('Select the size category of the frame (e.g., Small, Medium, Large).'),

                TextInput::make('weight')
                    ->required()
                    ->rules('numeric|min:0')
                    ->helperText('Enter the weight of the glasses in grams (e.g., 25g).'),

                Select::make('uv_protection')
                    ->options([
                        '100_uv' => '100% UV Protection',
                        'polarized' => 'Polarized',
                        'none' => 'None',
                    ])
                    ->required()
                    ->searchable()
                    ->helperText('Specify if the lenses offer UV protection.'),

                TextInput::make('stock_quantity')
                    ->required()
                    ->rules('integer|min:1')
                    ->helperText('Enter the current quantity of this eyewear available in stock. Must be at least 1.'),

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
                    ->helperText('Enter the price per unit of the eyewear. Must be a positive number.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('lens_type')->sortable(),
                TextColumn::make('frame_style')->sortable(),
                TextColumn::make('stock_quantity')->sortable(),
                TextColumn::make('price')->sortable(),
            ])
            ->filters([])
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
            'index' => Pages\ListEyewears::route('/'),
            'create' => Pages\CreateEyewear::route('/create'),
            'edit' => Pages\EditEyewear::route('/{record}/edit'),
        ];
    }
}
