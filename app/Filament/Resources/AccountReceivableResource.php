<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\AccountReceivableResource\Pages;
use App\Models\AccountReceivable;
use App\Services\EvolutionApiService;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AccountReceivableResource extends Resource
{
    protected static ?string $model = AccountReceivable::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?string $modelLabel = 'Conta a Receber';

    protected static ?string $pluralModelLabel = 'Contas a Receber';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Origem da Receita')->schema([
                Grid::make(2)->schema([
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\Select::make('customer_id')->label('Cliente/Sacado')->relationship('customer', 'name')->searchable()->preload()->required(),
                    Components\Select::make('contract_id')->label('Contrato Vinculado')->relationship('contract', 'contract_number')->searchable()->preload(),
                    Components\Select::make('invoice_id')->label('Fatura')->relationship('invoice', 'invoice_number')->searchable()->preload(),
                ]),
                Components\TextInput::make('description')->label('Descricao')->required()->maxLength(255),
            ]),

            Section::make('Valores e Datas')->schema([
                Grid::make(4)->schema([
                    Components\TextInput::make('amount')->label('Valor Total (R$)')->numeric()->prefix('R$')->required(),
                    Components\TextInput::make('paid_amount')->label('Valor Pago (R$)')->numeric()->prefix('R$')->default(0)->disabled()->dehydrated(),
                    Components\DatePicker::make('due_date')->label('Vencimento')->required()->native(false),
                    Components\Select::make('status')->label('Status')->options([
                        'pendente' => 'Pendente',
                        'parcial' => 'Pago Parcial',
                        'recebido' => 'Recebido',
                        'inadimplente' => 'Inadimplente',
                        'cancelado' => 'Cancelado',
                    ])->default('pendente')->required(),
                ]),
            ]),

            Section::make('Dados do Pagamento')->schema([
                Grid::make(3)->schema([
                    Components\DateTimePicker::make('received_at')->label('Data/Hora do Recebimento')->native(false),
                    Components\Select::make('payment_method')->label('Forma de Pagamento')->options([
                        'pix' => 'PIX',
                        'cartao_credito' => 'Cartao de Credito',
                        'cartao_debito' => 'Cartao de Debito',
                        'transferencia' => 'Transferencia Bancaria',
                        'boleto' => 'Boleto',
                        'dinheiro' => 'Dinheiro',
                        'cheque' => 'Cheque',
                        'outro' => 'Outro',
                    ]),
                    Components\TextInput::make('payer_name')->label('Pagador (Nome)')->maxLength(255),
                ]),
                Grid::make(2)->schema([
                    Components\TextInput::make('payment_bank')->label('Banco')->maxLength(255),
                    Components\TextInput::make('payment_reference')->label('Comprovante / Referencia')->maxLength(255),
                ]),
                Components\FileUpload::make('payment_proof_path')->label('Comprovante de Pagamento (anexo)')->directory('payment-proofs')->acceptedFileTypes(['image/*', 'application/pdf'])->maxSize(5120),
                Components\Textarea::make('notes')->label('Observacoes')->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')->label('Descricao')->searchable()->sortable()->limit(40),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('invoice.invoice_number')->label('Fatura')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('due_date')->label('Vencimento')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Valor')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')->label('Pago')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable()
                    ->color(fn (AccountReceivable $record): string => (float) $record->paid_amount >= (float) $record->amount ? 'success' : ((float) $record->paid_amount > 0 ? 'warning' : 'gray')),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendente' => 'warning',
                        'parcial' => 'info',
                        'recebido' => 'success',
                        'inadimplente' => 'danger',
                        'cancelado' => 'gray',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('payment_method')->label('Forma Pgto')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pendente' => 'Pendente',
                    'parcial' => 'Pago Parcial',
                    'recebido' => 'Recebido',
                    'inadimplente' => 'Inadimplente',
                    'cancelado' => 'Cancelado',
                ]),
                Tables\Filters\SelectFilter::make('customer_id')->label('Cliente')->relationship('customer', 'name'),
            ])
            ->actions([
                Actions\EditAction::make(),

                // REGISTRAR PAGAMENTO (total ou parcial)
                Actions\Action::make('registrar_pagamento')
                    ->label('Pagar')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (AccountReceivable $record) => in_array($record->status, ['pendente', 'parcial', 'inadimplente']))
                    ->form([
                        Components\Select::make('tipo_pagamento')->label('Tipo')->options([
                            'total' => 'Pagamento Total',
                            'parcial' => 'Pagamento Parcial',
                        ])->default('total')->required()->live(),
                        Components\TextInput::make('valor_pago')->label('Valor (R$)')->numeric()->prefix('R$')->required()
                            ->default(fn (AccountReceivable $record) => $record->remaining),
                        Components\Select::make('forma_pagamento')->label('Forma de Pagamento')->options([
                            'pix' => 'PIX',
                            'cartao_credito' => 'Cartao de Credito',
                            'cartao_debito' => 'Cartao de Debito',
                            'transferencia' => 'Transferencia Bancaria',
                            'boleto' => 'Boleto',
                            'dinheiro' => 'Dinheiro',
                            'cheque' => 'Cheque',
                            'outro' => 'Outro',
                        ])->required(),
                        Components\TextInput::make('pagador')->label('Pagador (Nome)')->maxLength(255),
                        Components\TextInput::make('banco')->label('Banco')->maxLength(255),
                        Components\TextInput::make('comprovante')->label('Comprovante / Referencia')->maxLength(255),
                        Components\FileUpload::make('anexo_comprovante')->label('Anexar Comprovante (imagem/PDF)')->directory('payment-proofs')->acceptedFileTypes(['image/*', 'application/pdf'])->maxSize(5120),
                        Components\DateTimePicker::make('data_pagamento')->label('Data/Hora do Pagamento')->default(now())->required()->native(false),
                        Components\Toggle::make('enviar_confirmacao')->label('Enviar confirmacao por WhatsApp')->default(true),
                    ])
                    ->modalHeading('Registrar Pagamento')
                    ->modalDescription(fn (AccountReceivable $record) => "Saldo: R$ " . number_format($record->remaining, 2, ',', '.') . " de R$ " . number_format((float) $record->amount, 2, ',', '.'))
                    ->action(function (AccountReceivable $record, array $data) {
                        $valorPago = (float) $data['valor_pago'];
                        $novoPago = (float) $record->paid_amount + $valorPago;
                        $total = (float) $record->amount;
                        $novoStatus = $novoPago >= $total ? 'recebido' : 'parcial';

                        $methodLabels = [
                            'pix' => 'PIX', 'cartao_credito' => 'Cartao de Credito',
                            'cartao_debito' => 'Cartao de Debito', 'transferencia' => 'Transferencia',
                            'boleto' => 'Boleto', 'dinheiro' => 'Dinheiro',
                            'cheque' => 'Cheque', 'outro' => 'Outro',
                        ];

                        $record->update([
                            'paid_amount' => $novoPago,
                            'status' => $novoStatus,
                            'payment_method' => $data['forma_pagamento'],
                            'payer_name' => $data['pagador'] ?? null,
                            'payment_bank' => $data['banco'] ?? null,
                            'payment_reference' => $data['comprovante'] ?? null,
                            'payment_proof_path' => $data['anexo_comprovante'] ?? $record->payment_proof_path,
                            'received_at' => $data['data_pagamento'],
                            'notes' => ($record->notes ? $record->notes . "\n" : '')
                                . "[" . now()->format('d/m H:i') . "] Pgto R$ " . number_format($valorPago, 2, ',', '.') . " via " . ($methodLabels[$data['forma_pagamento']] ?? $data['forma_pagamento']),
                        ]);

                        // Enviar confirmação por WhatsApp
                        if (($data['enviar_confirmacao'] ?? false) && $record->customer?->phone) {
                            $phone = $record->customer->phone;
                            $metodo = $methodLabels[$data['forma_pagamento']] ?? $data['forma_pagamento'];

                            $message = "✅ *CONFIRMACAO DE PAGAMENTO*\n\n"
                                . "Cliente: {$record->customer->name}\n"
                                . "Descricao: {$record->description}\n"
                                . "Valor pago: R$ " . number_format($valorPago, 2, ',', '.') . "\n"
                                . "Forma: {$metodo}\n"
                                . ($data['banco'] ? "Banco: {$data['banco']}\n" : '')
                                . ($data['comprovante'] ? "Ref: {$data['comprovante']}\n" : '')
                                . "Data: " . \Carbon\Carbon::parse($data['data_pagamento'])->format('d/m/Y H:i') . "\n\n";

                            if ($novoStatus === 'recebido') {
                                $message .= "💚 Pagamento QUITADO. Obrigado!\n\n";
                            } else {
                                $restante = $total - $novoPago;
                                $message .= "⚠️ Pagamento PARCIAL. Saldo restante: R$ " . number_format($restante, 2, ',', '.') . "\n\n";
                            }

                            $message .= "Elite Locadora";

                            try {
                                $evolution = app(EvolutionApiService::class);
                                $evolution->sendText($phone, $message);
                            } catch (\Exception $e) {
                                // silently fail
                            }
                        }

                        $statusLabel = $novoStatus === 'recebido' ? 'QUITADO' : 'PARCIAL';
                        \Filament\Notifications\Notification::make()
                            ->title("Pagamento registrado - {$statusLabel}")
                            ->body("R$ " . number_format($valorPago, 2, ',', '.') . " recebido. Total pago: R$ " . number_format($novoPago, 2, ',', '.'))
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
            ->defaultSort('due_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountReceivables::route('/'),
            'create' => Pages\CreateAccountReceivable::route('/create'),
            'edit' => Pages\EditAccountReceivable::route('/{record}/edit'),
        ];
    }
}
