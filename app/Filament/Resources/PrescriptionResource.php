<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrescriptionResource\Pages;
use App\Models\Prescription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;


    protected static ?string $navigationIcon =  'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Prescriptions';
    protected static ?string $navigationGroup = 'Queue Management';

    public static function shouldRegisterNavigation(): bool
    {
        // Check if the user has permission to view any appointments
        return Auth::user()->hasAnyPermission([
            'view_any_prescription',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('prescription_data')
                    ->label('Prescription Details')
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->reactive(),

                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->visible(fn($get) => $get('status') === 'rejected')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visit.patient.name')->label('Patient Name'),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->actions([

                Action::make('view')
                    ->label('View Prescription')
                    ->icon('heroicon-o-eye')
                    ->visible(fn(Prescription $record) => $record->status === 'pending' || $record->status === 'approved' || $record->status === 'dispensed')
                    ->url(fn(Prescription $record) => PrescriptionResource::getUrl('view', ['record' => $record->id])),

                Action::make('createOrder')
                    ->label('Create/View Order')
                    ->icon('heroicon-o-plus-circle')
                    ->visible(fn(Prescription $record) => $record->status === 'approved' && !$record->order)
                    ->url(fn(Prescription $record) => PrescriptionResource::getUrl('createOrder', ['record' => $record->id])),

                Action::make('viewOrder')
                    ->label('View Order')
                    ->icon('heroicon-o-eye')
                    ->visible(fn(Prescription $record) => $record->status === 'dispensed' )
                    ->url(fn(Prescription $record) => PrescriptionResource::getUrl('createOrder', ['record' => $record->id])),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrescriptions::route('/'),
            'view' => Pages\ViewPrescription::route('/{record}/view'),
            'createOrder' => Pages\CreatePrescriptionOrder::route('/{record}/create-order'),
        ];
    }
}
