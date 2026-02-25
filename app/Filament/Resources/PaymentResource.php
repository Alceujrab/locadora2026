<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Enums\PaymentMethod;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?string $modelLabel = 'Pagamento';

    protected static ?string $pluralModelLabel = 'Pagamentos';

    protected static ?int $navigationSort = 2;

    public static function getGloballySearchableAttributes(): array
    {
        return ['invoice.invoice_number', 'transaction_id', 'mp_payment_id'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informacoes do Pagamento')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('invoice_id')
                        ->label('Fatura Vinculada')
                        ->relationship('invoice', 'invoice_number')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Components\TextInput::make('amount')->label('Valor (R$)')->numeric()->prefix('R$')->required(),
                    Components\Select::make('method')->label('Metodo de Pagamento')->options(PaymentMethod::class)->required(),
                    Components\DateTimePicker::make('paid_at')->label('Data do Pagamento')->native(false),
                    Components\TextInput::make('transaction_id')->label('ID de Transacao (Interno)')->maxLength(255),
                    Components\TextInput::make('mp_payment_id')->label('ID MercadoPago')->maxLength(255),
                ]),
                Components\Select::make('mp_status')->label('Status Gateway')->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'authorized' => 'Authorized',
                    'in_process' => 'In Process',
                    'in_mediation' => 'In Mediation',
                    'rejected' => 'Rejected',
                    'cancelled' => 'Cancelled',
                    'refunded' => 'Refunded',
                    'charged_back' => 'Charged Back',
                ]),
                Components\Textarea::make('notes')->label('Observacoes')->columnSpanFull(),
            ]),

            Section::make('Estorno / Reembolso')->schema([
                Grid::make(2)->schema([
                    Components\DateTimePicker::make('refunded_at')->label('Data do Estorno')->native(false),
                    Components\TextInput::make('refund_amount')->label('Valor Estornado (R$)')->numeric()->prefix('R$'),
                ]),
            ])->collapsed(),

            Section::make('Boleto / Pix (Apenas Leitura)')->schema([
                Grid::make(2)->schema([
                    Components\TextInput::make('pix_qr_code')->label('Pix QR Code (Link)')->disabled(),
                    Components\Textarea::make('pix_qr_code_base64')->label('Pix QR Code (Base64)')->disabled(),
                    Components\TextInput::make('boleto_url')->label('Boleto (Link)')->disabled(),
                    Components\Textarea::make('boleto_barcode')->label('Codigo de Barras Boleto')->disabled(),
                ]),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.invoice_number')->label('Fatura')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('method')->label('Metodo')->badge(),
                Tables\Columns\TextColumn::make('amount')->label('Valor')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('paid_at')->label('Data Pgto')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('mp_status')->label('Status MP')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending', 'in_process' => 'warning',
                        'rejected', 'cancelled', 'charged_back' => 'danger',
                        'refunded' => 'gray',
                        default => 'primary',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('method')->label('Metodo')->options(PaymentMethod::class),
            ])
            ->actions([
                Actions\EditAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
