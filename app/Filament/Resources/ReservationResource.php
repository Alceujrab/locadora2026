<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Enums\ReservationStatus;
use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Reservation;
use App\Models\RentalExtra;
use App\Models\ReservationExtra;
use Filament\Forms\Components;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $modelLabel = 'Reserva';

    protected static ?string $pluralModelLabel = 'Reservas';

    protected static ?int $navigationSort = 2;

    public static function getGloballySearchableAttributes(): array
    {
        return ['customer.name', 'vehicle.plate', 'id'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Reserva')->tabs([
                Tabs\Tab::make('Dados Principais')->icon('heroicon-o-information-circle')->schema([
                    Grid::make(2)->schema([
                        Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                        Components\Select::make('customer_id')->label('Cliente')->relationship('customer', 'name')->searchable()->preload()->required(),
                    ]),
                    Grid::make(2)->schema([
                        Components\Select::make('category_id')->label('Categoria')->relationship('category', 'name')->searchable()->preload(),
                        Components\Select::make('vehicle_id')->label('Veiculo')->relationship('vehicle', 'plate')->searchable()->preload()->required(),
                    ]),
                    Components\Select::make('status')->label('Status')->options(ReservationStatus::class)->default(ReservationStatus::PENDING)->required(),
                ]),

                Tabs\Tab::make('Periodo e Locais')->icon('heroicon-o-clock')->schema([
                    Grid::make(2)->schema([
                        Components\DateTimePicker::make('pickup_date')
                            ->label('Data/Hora Retirada')
                            ->required()
                            ->native(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                static::recalculate($get, $set);
                            }),
                        Components\DateTimePicker::make('return_date')
                            ->label('Data/Hora Devolucao')
                            ->required()
                            ->native(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                static::recalculate($get, $set);
                            }),
                    ]),
                    Grid::make(2)->schema([
                        Components\Select::make('pickup_branch_id')->label('Local de Retirada')->relationship('pickupBranch', 'name')->searchable()->preload(),
                        Components\Select::make('return_branch_id')->label('Local de Devolucao')->relationship('returnBranch', 'name')->searchable()->preload(),
                    ]),
                ]),

                Tabs\Tab::make('Valores')->icon('heroicon-o-currency-dollar')->schema([
                    Grid::make(3)->schema([
                        Components\TextInput::make('daily_rate')
                            ->label('Diaria (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                static::recalculate($get, $set);
                            }),
                        Components\TextInput::make('total_days')
                            ->label('Qtd Dias')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Components\TextInput::make('subtotal')
                            ->label('Subtotal Diarias (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->disabled()
                            ->dehydrated(),
                    ]),
                    Grid::make(3)->schema([
                        Components\TextInput::make('extras_total')
                            ->label('Total Extras (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),
                        Components\TextInput::make('discount')
                            ->label('Desconto (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                static::recalculate($get, $set);
                            }),
                        Components\TextInput::make('total')
                            ->label('Total Geral (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->disabled()
                            ->dehydrated(),
                    ]),
                ]),

                Tabs\Tab::make('Opcionais / Extras')->icon('heroicon-o-puzzle-piece')->schema([
                    Components\Repeater::make('extras')
                        ->label('')
                        ->relationship()
                        ->schema([
                            Grid::make(4)->schema([
                                Components\Select::make('rental_extra_id')
                                    ->label('Opcional')
                                    ->options(RentalExtra::query()->where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        if ($state) {
                                            $extra = RentalExtra::find($state);
                                            if ($extra) {
                                                $set('unit_price', $extra->daily_rate);
                                                $qty = (int) ($get('quantity') ?: 1);
                                                $set('total', $extra->daily_rate * $qty);
                                            }
                                        }
                                    }),
                                Components\TextInput::make('quantity')
                                    ->label('Qtd')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('total', (float) ($get('unit_price') ?: 0) * (int) ($get('quantity') ?: 1));
                                    }),
                                Components\TextInput::make('unit_price')
                                    ->label('Valor Unit.')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->required(),
                                Components\TextInput::make('total')
                                    ->label('Total')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                        ])
                        ->addActionLabel('+ Adicionar Opcional')
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ]),

                Tabs\Tab::make('Observacoes')->icon('heroicon-o-chat-bubble-left')->schema([
                    Components\Textarea::make('notes')->label('Observacoes Internas')->columnSpanFull(),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    protected static function recalculate(Get $get, Set $set): void
    {
        $pickup = $get('pickup_date');
        $return = $get('return_date');
        $daily = (float) ($get('daily_rate') ?: 0);
        $discount = (float) ($get('discount') ?: 0);

        if ($pickup && $return) {
            $start = \Carbon\Carbon::parse($pickup);
            $end = \Carbon\Carbon::parse($return);
            $days = max(1, (int) ceil($start->diffInHours($end) / 24));
            $set('total_days', $days);
            $subtotal = $daily * $days;
            $set('subtotal', number_format($subtotal, 2, '.', ''));

            $extrasTotal = (float) ($get('extras_total') ?: 0);
            $total = $subtotal + $extrasTotal - $discount;
            $set('total', number_format($total, 2, '.', ''));
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('vehicle.plate')->label('Veiculo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pickup_date')->label('Retirada')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('return_date')->label('Devolucao')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('total_days')->label('Dias')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(ReservationStatus::class),
                Tables\Filters\SelectFilter::make('branch_id')->label('Filial')->relationship('branch', 'name'),
            ])
            ->actions([
                Actions\EditAction::make(),

                // PDF DA RESERVA
                Actions\Action::make('pdf_reserva')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (Reservation $record) {
                        $record->load(['customer', 'vehicle', 'branch', 'extras.rentalExtra', 'pickupBranch', 'returnBranch']);

                        $logoBase64 = null;
                        $logoPath = public_path('images/logo-elite.png');
                        if (file_exists($logoPath)) {
                            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                        }

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reservation-pdf', [
                            'reservation' => $record,
                            'logoBase64' => $logoBase64,
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'Reserva-' . $record->id . '.pdf',
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                // ENVIAR WHATSAPP
                Actions\Action::make('send_whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar Reserva por WhatsApp')
                    ->visible(fn (Reservation $record) => $record->customer?->phone)
                    ->action(function (Reservation $record) {
                        $record->load(['customer', 'vehicle', 'extras.rentalExtra']);
                        $phone = $record->customer->phone;
                        $total = 'R$ ' . number_format((float) $record->total, 2, ',', '.');

                        $message = "📋 *RESERVA #{$record->id}*\n\n"
                            . "Cliente: {$record->customer->name}\n"
                            . "Veiculo: {$record->vehicle?->plate} - {$record->vehicle?->brand} {$record->vehicle?->model}\n"
                            . "Retirada: {$record->pickup_date?->format('d/m/Y H:i')}\n"
                            . "Devolucao: {$record->return_date?->format('d/m/Y H:i')}\n"
                            . "Dias: {$record->total_days}\n"
                            . "Total: {$total}\n\n"
                            . "Elite Locadora";

                        $evolution = app(\App\Services\EvolutionApiService::class);
                        $evolution->sendText($phone, $message);

                        \Filament\Notifications\Notification::make()
                            ->title('Reserva enviada!')
                            ->body("Enviada para {$phone}")
                            ->success()
                            ->send();
                    }),

                // GERAR FATURA
                Actions\Action::make('gerar_fatura')
                    ->label('Fatura')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Gerar Fatura da Reserva')
                    ->modalDescription(fn (Reservation $record) => "Criar fatura de R$ " . number_format((float) $record->total, 2, ',', '.') . " para {$record->customer?->name}?")
                    ->visible(fn (Reservation $record) => ! Invoice::where('notes', 'LIKE', "%Reserva #{$record->id}%")->exists())
                    ->action(function (Reservation $record) {
                        $record->load(['customer', 'branch', 'vehicle', 'extras.rentalExtra']);

                        $invoiceNumber = 'FAT-' . date('Y') . '-' . str_pad(Invoice::whereYear('created_at', date('Y'))->count() + 1, 5, '0', STR_PAD_LEFT);

                        $notes = "Reserva #{$record->id}\n"
                            . "Veiculo: {$record->vehicle?->plate} - {$record->vehicle?->brand} {$record->vehicle?->model}\n"
                            . "Periodo: {$record->pickup_date?->format('d/m/Y H:i')} a {$record->return_date?->format('d/m/Y H:i')}\n"
                            . "Dias: {$record->total_days} | Diaria: R$ " . number_format((float) $record->daily_rate, 2, ',', '.');

                        $invoice = Invoice::create([
                            'branch_id' => $record->branch_id,
                            'customer_id' => $record->customer_id,
                            'invoice_number' => $invoiceNumber,
                            'due_date' => now()->addDays(3),
                            'amount' => $record->subtotal,
                            'discount' => $record->discount ?? 0,
                            'total' => $record->total,
                            'status' => \App\Enums\InvoiceStatus::OPEN,
                            'notes' => $notes,
                        ]);

                        // Item: Diárias
                        \App\Models\InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'description' => "Locacao {$record->vehicle?->plate} - {$record->vehicle?->brand} {$record->vehicle?->model} ({$record->pickup_date?->format('d/m')} a {$record->return_date?->format('d/m')})",
                            'quantity' => $record->total_days,
                            'unit_price' => $record->daily_rate,
                            'total' => $record->subtotal,
                        ]);

                        // Itens: Extras
                        foreach ($record->extras as $extra) {
                            \App\Models\InvoiceItem::create([
                                'invoice_id' => $invoice->id,
                                'description' => $extra->rentalExtra?->name ?? 'Extra',
                                'quantity' => $extra->quantity,
                                'unit_price' => $extra->unit_price,
                                'total' => $extra->total,
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Fatura gerada!')
                            ->body("Fatura {$invoiceNumber} com " . (1 + $record->extras->count()) . " itens")
                            ->success()
                            ->send();
                    }),

                // GERAR CONTRATO
                Actions\Action::make('gerar_contrato')
                    ->label('Contrato')
                    ->icon('heroicon-o-document-check')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Gerar Contrato da Reserva')
                    ->visible(fn (Reservation $record) => ! Contract::where('reservation_id', $record->id)->exists())
                    ->form([
                        Components\Select::make('template_id')
                            ->label('Template do Contrato')
                            ->options(\App\Models\ContractTemplate::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->action(function (Reservation $record, array $data) {
                        $record->load(['customer', 'vehicle', 'branch', 'extras.rentalExtra']);

                        $contract = Contract::create([
                            'branch_id' => $record->branch_id,
                            'reservation_id' => $record->id,
                            'customer_id' => $record->customer_id,
                            'vehicle_id' => $record->vehicle_id,
                            'template_id' => $data['template_id'],
                            'contract_number' => Contract::generateContractNumber(),
                            'pickup_date' => $record->pickup_date,
                            'return_date' => $record->return_date,
                            'daily_rate' => $record->daily_rate,
                            'total_days' => $record->total_days,
                            'extras_total' => $record->extras_total,
                            'discount' => $record->discount,
                            'total' => $record->total,
                            'status' => \App\Enums\ContractStatus::DRAFT,
                            'signature_token' => \Illuminate\Support\Str::random(64),
                            'created_by' => auth()->id(),
                        ]);

                        // Copiar extras para o contrato
                        foreach ($record->extras as $extra) {
                            \App\Models\ContractExtra::create([
                                'contract_id' => $contract->id,
                                'rental_extra_id' => $extra->rental_extra_id,
                                'quantity' => $extra->quantity,
                                'unit_price' => $extra->unit_price,
                                'total' => $extra->total,
                            ]);
                        }

                        $record->update(['status' => ReservationStatus::CONFIRMED]);

                        \Filament\Notifications\Notification::make()
                            ->title('Contrato gerado!')
                            ->body("Contrato {$contract->contract_number} criado. Acesse Contratos para enviar para assinatura.")
                            ->success()
                            ->send();
                    }),

                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
