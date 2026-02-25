<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?string $modelLabel = 'Fatura';

    protected static ?string $pluralModelLabel = 'Faturas';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['invoice_number', 'customer.name', 'contract.contract_number'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Fatura')->tabs([
                Tabs\Tab::make('Dados Principais')->icon('heroicon-o-information-circle')->schema([
                    Components\TextInput::make('invoice_number')->label('Numero da Fatura')->disabled()->dehydrated(false)->maxLength(255),
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\Select::make('customer_id')->label('Cliente')->relationship('customer', 'name')->searchable()->preload()->required(),
                    Components\Select::make('contract_id')->label('Contrato Vinculado')->relationship('contract', 'contract_number')->searchable()->preload(),
                    Components\DatePicker::make('due_date')->label('Data de Vencimento')->required()->native(false),
                    Components\Select::make('status')->label('Status')->options(InvoiceStatus::class)->default(InvoiceStatus::OPEN)->required(),
                ])->columns(3),

                Tabs\Tab::make('Valores e Multas')->icon('heroicon-o-currency-dollar')->schema([
                    Components\TextInput::make('amount')->label('Valor Base (R$)')->numeric()->prefix('R$')->required(),
                    Components\TextInput::make('penalty_amount')->label('Multa (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('interest_amount')->label('Juros (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('discount')->label('Desconto (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('total')->label('Total a Pagar (R$)')->numeric()->prefix('R$')->required(),
                    Components\TextInput::make('installment_number')->label('Numero da Parcela')->numeric(),
                    Components\TextInput::make('total_installments')->label('Total de Parcelas')->numeric(),
                ])->columns(3),

                Tabs\Tab::make('Pagamento')->icon('heroicon-o-banknotes')->schema([
                    Components\DateTimePicker::make('paid_at')->label('Data de Pagamento')->native(false),
                    Components\Select::make('payment_method')->label('Metodo de Pagamento')->options(PaymentMethod::class),
                    Components\TextInput::make('mp_payment_id')->label('ID MercadoPago')->maxLength(255),
                    Components\Textarea::make('notes')->label('Observacoes da Fatura')->columnSpanFull(),
                ])->columns(3),

                Tabs\Tab::make('Nota Fiscal (NFS-e)')->icon('heroicon-o-document-text')->schema([
                    Components\TextInput::make('nfse_number')->label('Numero NFS-e')->maxLength(255),
                    Components\FileUpload::make('nfse_pdf_path')->label('Arquivo PDF (NFS-e)')->directory('nfse-pdfs')->acceptedFileTypes(['application/pdf']),
                    Components\FileUpload::make('nfse_xml_path')->label('Arquivo XML (NFS-e)')->directory('nfse-xmls')->acceptedFileTypes(['text/xml', 'application/xml']),
                ])->columns(3),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('Fatura')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contract.contract_number')->label('Contrato')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('due_date')->label('Vencimento')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\IconColumn::make('sent_at')->label('Enviada')->boolean()->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')->toggleable(),
                Tables\Columns\IconColumn::make('confirmed_at')->label('Confirmada')->boolean()->trueIcon('heroicon-o-check-badge')->falseIcon('heroicon-o-clock')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(InvoiceStatus::class),
                Tables\Filters\SelectFilter::make('branch_id')->label('Filial')->relationship('branch', 'name'),
            ])
            ->actions([
                Actions\EditAction::make(),

                // Gerar PDF da fatura
                Actions\Action::make('generate_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (Invoice $record) {
                        $path = \App\Http\Controllers\InvoiceConfirmationController::generatePdf($record);

                        $record->refresh();

                        return response()->streamDownload(
                            fn () => print(\Illuminate\Support\Facades\Storage::disk('public')->get($path)),
                            'Fatura-' . $record->invoice_number . '.pdf',
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                // Enviar fatura por WhatsApp
                Actions\Action::make('send_invoice_whatsapp')
                    ->label('Enviar Fatura')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar Fatura por WhatsApp')
                    ->modalDescription(fn (Invoice $record) => "A fatura {$record->invoice_number} de R$ " . number_format((float) $record->total, 2, ',', '.') . " sera enviada com PDF ao cliente.")
                    ->visible(fn (Invoice $record) => $record->customer_id && $record->customer?->phone)
                    ->action(function (Invoice $record) {
                        // Gerar PDF se não existir
                        if (! $record->pdf_path || ! \Illuminate\Support\Facades\Storage::disk('public')->exists($record->pdf_path)) {
                            \App\Http\Controllers\InvoiceConfirmationController::generatePdf($record);
                            $record->refresh();
                        }

                        $phone = $record->customer->phone;
                        $confirmUrl = url("/fatura/{$record->id}");
                        $charge = 'R$ ' . number_format((float) $record->total, 2, ',', '.');

                        $message = "💰 *FATURA {$record->invoice_number}*\n\n"
                            . "Cliente: {$record->customer->name}\n"
                            . "Valor: {$charge}\n"
                            . "Vencimento: {$record->due_date?->format('d/m/Y')}\n\n"
                            . "📋 Acesse o link abaixo para ver os detalhes e confirmar o recebimento:\n{$confirmUrl}\n\n"
                            . "O PDF da fatura esta em anexo.\n\n"
                            . "Elite Locadora";

                        $evolution = app(\App\Services\EvolutionApiService::class);
                        $evolution->sendText($phone, $message);

                        // Enviar PDF
                        $pdfUrl = asset('storage/' . $record->pdf_path);
                        $evolution->sendDocument($phone, $pdfUrl, 'Fatura-' . $record->invoice_number . '.pdf');

                        $record->update(['sent_at' => now()]);

                        \Filament\Notifications\Notification::make()
                            ->title('Fatura enviada!')
                            ->body("Enviada para {$phone} com PDF em anexo")
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
