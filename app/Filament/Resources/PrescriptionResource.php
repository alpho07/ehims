<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrescriptionResource\Pages;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Visit;
use Doctrine\DBAL\Schema\View;
use Filament\Tables\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Prescription Queue';
    protected static ?string $navigationGroup = 'Queue Management';
    // Declare $visit property
    public Visit $visit;


    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visit.patient.name')->label('Patient Name'),
                Tables\Columns\TextColumn::make('status')->label('Prescription Status')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View Prescription')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Prescription $record) => PrescriptionResource::getUrl('view', ['record' => $record->id])),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('prescription_data')
                    ->label('Prescription Details')
                    ->disabled()
                    ->default(fn($record) => json_encode($record->getPrescriptionData())),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Textarea::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->visible(fn($get) => $get('status') === 'rejected')
                    ->nullable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrescriptions::route('/'),
            'view' => PrescriptionResource\Pages\ViewPrescription::route('/{record}/view'),

        ];
    }
}
