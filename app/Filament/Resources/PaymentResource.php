<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Patient;
use App\Models\Insurance;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $navigationGroup = 'Payment Management';

    protected static ?int $navigationSort = -3;

    public static function shouldRegisterNavigation(): bool
    {
        // Check if the user has permission to view any appointments
        return Auth::user()->hasAnyPermission([
            'view_any_payment',
            // Add other permissions as needed
        ]);
    }


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        // Left side: Patient Information
                        Card::make()
                            ->schema([
                                Select::make('patient_id')
                                    ->label('Patient')
                                    ->options(Patient::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $patient = Patient::find($state);
                                        $set('patient_name', $patient->name ?? '');
                                        $set('patient_dob', $patient->dob ?? '');
                                        $set('patient_age', $patient ? \Carbon\Carbon::parse($patient->dob)->age : '');
                                        $set('patient_phone', $patient->phone ?? '');
                                        $set('patient_address', $patient->address ?? '');

                                        // Automatically assign the latest visit

                                        $latestVisit = $patient->visits()->latest()->first();

                                        if (!$latestVisit) {
                                            // No visits found, get the last visit ID and increment it by 1
                                            $lastVisitId = \App\Models\Visit::latest('id')->first()->id ?? 0;
                                            $nextVisitId = $lastVisitId + 1;
                                        } else {
                                            // Latest visit exists
                                            $nextVisitId = $latestVisit->id;
                                        }
                                        $latestVisit = $patient->visits()->latest()->first();
                                        $set('visit_id', $latestVisit ? $latestVisit->id : $nextVisitId);
                                    }),

                                TextInput::make('patient_name')
                                    ->label('Name')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('patient_dob')
                                    ->label('Date of Birth')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('patient_age')
                                    ->label('Age')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('patient_phone')
                                    ->label('Phone Number')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('patient_address')
                                    ->label('Address')
                                    ->disabled()
                                    ->dehydrated(false),

                                // Hidden field to store the latest visit ID
                                TextInput::make('visit_id')
                                    ->label('Visit ID')
                                    //->hidden()
                                    ->required(),
                            ])
                            ->columnSpan(1),

                        // Right side: Payment Details
                        Card::make()
                            ->schema([
                                Repeater::make('payment_details')
                                    ->label('Payment Details')
                                    ->relationship('paymentDetails')
                                    ->schema([
                                        Select::make('payment_item_id')
                                            ->label('Payment Item')
                                            ->options(PaymentItem::all()->pluck('name', 'id'))
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set) =>
                                                $set('amount', PaymentItem::find($state)?->amount ?? 0)
                                            ),

                                        TextInput::make('amount')
                                            ->label('Item Amount')
                                            ->numeric()
                                            ->required()
                                            ->readOnly()
                                            ->reactive(),

                                        Select::make('payment_type')
                                            ->label('Payment Type')
                                            ->options([
                                                'Out of Pocket' => 'Out of Pocket',
                                                'Insurance' => 'Insurance',
                                            ])
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set, callable $get) =>
                                                $set('total_amount', $state === 'Out of Pocket' ? $get('amount') : ($state === 'Insurance' ? $get('amount') : $get('total_amount')))
                                            ),

                                        // Out of Pocket Payment Options
                                        Radio::make('out_of_pocket_option')
                                            ->label('Out of Pocket Option')
                                            ->options([
                                                'Full Payment' => 'Full Payment',
                                                'Waiver' => 'Waiver',
                                                'Free' => 'Free',
                                            ])
                                            ->default('Full Payment')
                                            ->visible(fn(Forms\Get $get) => $get('payment_type') === 'Out of Pocket')
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set, callable $get) =>
                                                $set('total_amount', match ($state) {
                                                    'Full Payment' => $get('amount'),
                                                    'Waiver' => $get('amount') - ($get('waiver_amount') ?? 0),
                                                    'Free' => 0,
                                                    default => $get('amount'),
                                                })
                                            ),

                                        TextInput::make('waiver_amount')
                                            ->label('Waiver Amount')
                                            ->numeric()
                                            ->minValue(0)
                                            ->visible(fn(Forms\Get $get) => $get('payment_type') === 'Out of Pocket' && $get('out_of_pocket_option') === 'Waiver')
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set, callable $get) =>
                                                $set('total_amount', $get('amount') - ($state ?? 0))
                                            ),

                                        Select::make('payment_mode')
                                            ->label('Mode of Payment')
                                            ->options([
                                                'Cash' => 'Cash',
                                                'Card' => 'Card',
                                                'Mobile Money' => 'Mobile Money',
                                            ])
                                            ->visible(fn(Forms\Get $get) => $get('payment_type') === 'Out of Pocket')
                                            ->reactive()
                                            ->required(),

                                        TextInput::make('payment_reference')
                                            ->label('Reference Number')
                                            ->required()
                                            ->visible(fn(Forms\Get $get) => $get('payment_mode') === 'Card' || $get('payment_mode') === 'Mobile Money'),


                                        // Insurance Payment Options
                                        Select::make('insurance_id')
                                            ->label('Insurance')
                                            ->options(Insurance::all()->pluck('name', 'id'))
                                            ->visible(fn(Forms\Get $get) => $get('payment_type') === 'Insurance')
                                            ->required(),

                                        Radio::make('insurance_option')
                                            ->label('Insurance Option')
                                            ->options([
                                                'Full Insurance' => 'Full Insurance',
                                                'Co-Pay' => 'Co-Pay',
                                            ])
                                            ->default('Full Insurance')
                                            ->visible(fn(Forms\Get $get) => $get('payment_type') === 'Insurance')
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set, callable $get) =>
                                                $set('total_amount', match ($state) {
                                                    'Full Insurance' => $get('amount'),
                                                    'Co-Pay' => $get('amount') + ($get('copay_amount') ?? 0),
                                                    default => $get('amount'),
                                                })
                                            ),

                                        FileUpload::make('insurance_document')
                                            ->label('Upload Scanned Insurance Document (PDF)')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->visible(fn(Forms\Get $get) => $get('payment_type') === 'Insurance'),

                                        TextInput::make('copay_amount')
                                            ->label('Co-Pay Amount')
                                            ->numeric()
                                            ->minValue(0)
                                            ->visible(fn(Forms\Get $get) => $get('insurance_option') === 'Co-Pay')
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set, callable $get) =>
                                                $set('total_amount', $get('amount') + ($state ?? 0))
                                            ),

                                        // Total Amount (Last Field)
                                        TextInput::make('total_amount')
                                            ->label('Total Amount')
                                            ->numeric()
                                            ->required()
                                            ->readOnly(),

                                        Toggle::make('is_paid')
                                            ->label('Is Paid')
                                            ->onIcon('heroicon-s-check-circle')
                                            ->offIcon('heroicon-s-x-circle')
                                            ->default(fn($record) => $record ? $record->is_paid : false) // Sets default value to the saved state when editing
                                            ->reactive(),
                                    ])
                                    ->minItems(1)
                                    ->collapsible()
                                    ->createItemButtonLabel('Add Payment Detail'),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }




    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                // Patient Information
                TextColumn::make('patient.hospital_number')
                    ->label('Hospital Number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('patient.name')
                    ->label('Patient Name')
                    ->sortable()
                    ->searchable(),

                // Payment Type
                TextColumn::make('payment_type')
                    ->label('Payment Type')
                    ->getStateUsing(function ($record) {
                        return $record->paymentDetails->pluck('payment_type')->implode(', ');
                    })
                    ->sortable()
                    ->searchable(),

                // Payment Details - Payment Item Names
                TextColumn::make('paymentDetails')
                    ->label('Payment Items')
                    ->getStateUsing(function ($record) {
                        return $record->paymentDetails->pluck('paymentItem.name')->implode(', ');
                    })
                    ->sortable()
                    ->searchable(),

                // Payment Amounts - Show Each Item's Amount
                TextColumn::make('paymentDetailsAmounts1')
                    ->label('Item Amounts')
                    ->getStateUsing(function ($record) {
                        return $record->paymentDetails->pluck('amount')->implode(', ');
                    })
                    ->sortable(),

                // Insurance Information
                TextColumn::make('insurance.name')
                    ->label('Insurance Provider')
                    ->getStateUsing(function ($record) {
                        return $record->paymentDetails->map(function ($detail) {
                            return $detail->insurance->name ?? 'N/A';
                        });
                    })
                    ->sortable()
                    ->searchable(),
                //->visible(fn($record) => $record && $record->payment_type === 'Insurance'),


                // Co-Pay Amount
                TextColumn::make('copay_amount')
                    ->label('Co-Pay Amount')
                    ->getStateUsing(function ($record) {
                        return $record->paymentDetails->pluck('copay_amount') ?? 'N/A';
                    })
                    ->sortable(),


                TextColumn::make('waiver_amount')
                    ->label('Waiver Amount')
                    ->getStateUsing(function ($record) {
                        return $record->paymentDetails->pluck('waiver_amount') ?? 'N/A';
                    })
                    ->sortable(),


                TextColumn::make('payment_mode')
                    ->label('Payment Mode')
                    ->getStateUsing(function ($record) {
                        return $record->paymentDetails->pluck('payment_mode') ?? 'N/A';
                    })
                    ->sortable(),

                TextColumn::make('payment_reference')
                    ->label('Payment Mode')
                    ->getStateUsing(function ($record) {
                        return $record->paymentDetails->pluck('payment_reference') ?? 'N/A';
                    })
                    ->sortable(),
                // ->visible(fn($record) => $record && $record->payment_type === 'Out of Pocket'),

                // Total Amount
                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->getStateUsing(function ($record) {
                        return $record->paymentDetails->pluck('total_amount');
                    })
                    ->sortable(),


                // Date Created
                TextColumn::make('created_at')
                    ->label('Date Created')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                // Add any table filters as necessary
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
