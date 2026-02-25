<?php

namespace App\Filament\Resources;

use Filament\Actions;
use App\Enums\ServiceOrderStatus;
use App\Filament\Resources\ServiceOrderResource\Pages;
use App\Filament\Resources\ServiceOrderResource\RelationManagers;
use App\Models\ServiceOrder;
use App\Services\EvolutionApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceOrderResource extends Resource
{
    protected static ?string $model = ServiceOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'Servicos';

    protected static ?string $modelLabel = 'Ordem de Servico';

    protected static ?string $pluralModelLabel = 'Ordens de Servico';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['vehicle.plate', 'supplier.name', 'description', 'requested_by'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // Seção 1: Dados da OS
            Section::make('Dados da OS')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\Select::make('vehicle_id')->label('Veiculo')->relationship('vehicle', 'plate')->searchable()->preload()->required(),
                    Components\Select::make('supplier_id')->label('Fornecedor/Oficina')->relationship('supplier', 'name')->searchable()->preload(),
                ]),
                Grid::make(3)->schema([
                    Components\Select::make('type')->label('Tipo de Servico')->options([
                        'preventiva' => 'Preventiva',
                        'corretiva' => 'Corretiva',
                        'estetica' => 'Estetica',
                        'sinistro' => 'Sinistro',
                        'outro' => 'Outro',
                    ])->required()->default('corretiva'),
                    Components\DateTimePicker::make('opened_at')->label('Data de Abertura')->native(false)->required()->default(now()),
                    Components\Select::make('status')->label('Status')->options(ServiceOrderStatus::class)->default(ServiceOrderStatus::OPEN)->required(),
                ]),
                Components\Textarea::make('description')->label('Problema / Descricao do Servico')->required()->rows(3)->columnSpanFull(),
            ])->icon('heroicon-o-document-text'),

            // Seção 2: Informações Operacionais
            Section::make('Informacoes Operacionais')->schema([
                Grid::make(2)->schema([
                    Components\TextInput::make('requested_by')->label('Quem Solicitou o Servico')->maxLength(255)->placeholder('Nome de quem solicitou'),
                    Components\TextInput::make('vehicle_city')->label('Cidade do Veiculo')->maxLength(255)->placeholder('Cidade onde o veiculo se encontra'),
                ]),
                Grid::make(2)->schema([
                    Components\TextInput::make('driver_phone')->label('Telefone do Motorista')->tel()->maxLength(20)->placeholder('(00) 00000-0000'),
                    Components\Select::make('customer_id')->label('Locatario Vinculado')->relationship('customer', 'name')->searchable()->preload()->helperText('Para envio de assinatura digital'),
                ]),
                Grid::make(2)->schema([
                    Components\Select::make('opened_by')->label('Funcionario que Abriu')->relationship('openedByUser', 'name')->searchable()->preload()->default(fn () => Auth::id()),
                    Components\Select::make('created_by')->label('Criado por')->relationship('createdBy', 'name')->searchable()->preload()->default(fn () => Auth::id()),
                ]),
                Components\Textarea::make('procedure_adopted')->label('Procedimento Adotado')->rows(3)->placeholder('Descreva o procedimento que sera adotado...')->columnSpanFull(),
            ])->icon('heroicon-o-clipboard-document-list')->collapsible(),

            // Seção 3: Evidências (Fotos e Vídeos)
            Section::make('Evidencias do Problema')->schema([
                Components\FileUpload::make('attachments')
                    ->label('Fotos e Videos')
                    ->multiple()
                    ->directory('so-attachments')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/quicktime'])
                    ->maxSize(51200) // 50MB
                    ->maxFiles(10)
                    ->imagePreviewHeight('200')
                    ->columnSpanFull()
                    ->helperText('Aceita JPG, PNG, WebP e videos MP4 (max 50MB por arquivo, max 10 arquivos)'),
            ])->icon('heroicon-o-camera')->collapsible()->collapsed(),

            // Seção 4: Fechamento e Totais
            Section::make('Fechamento e Totais')->schema([
                Grid::make(3)->schema([
                    Components\DateTimePicker::make('completed_at')->label('Data de Conclusao')->native(false),
                    Components\TextInput::make('nf_number')->label('Numero NF/Recibo')->maxLength(255),
                    Components\FileUpload::make('nf_path')->label('Arquivo NF (PDF)')->directory('so-nfs')->acceptedFileTypes(['application/pdf']),
                ]),
                Grid::make(4)->schema([
                    Components\TextInput::make('items_total')->label('Total Pecas (R$)')->numeric()->prefix('R$')->disabled()->default(0),
                    Components\TextInput::make('labor_total')->label('Total Mao de Obra (R$)')->numeric()->prefix('R$')->disabled()->default(0),
                    Components\TextInput::make('total')->label('Total Geral OS (R$)')->numeric()->prefix('R$')->disabled()->default(0),
                    Components\TextInput::make('customer_charge')->label('Valor Cobrado do Cliente (R$)')->numeric()->prefix('R$')->default(0)
                        ->helperText('Valor que sera faturado ao cliente (pode ser diferente do total da OS)'),
                ]),
                Components\Textarea::make('notes')->label('Observacoes da OS')->columnSpanFull(),
                Components\Textarea::make('closing_notes')->label('Observacoes de Fechamento')->columnSpanFull()->helperText('Preencher ao fechar a OS'),
            ])->icon('heroicon-o-calculator')->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('OS #')->sortable(),
                Tables\Columns\TextColumn::make('vehicle.plate')->label('Veiculo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Oficina')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('requested_by')->label('Solicitante')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('opened_at')->label('Abertura')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('total')->label('Total OS')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('customer_charge')->label('Cobrado')->formatStateUsing(fn ($state) => $state > 0 ? 'R$ '.number_format((float) $state, 2, ',', '.') : '-')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(ServiceOrderStatus::class),
                Tables\Filters\SelectFilter::make('type')->label('Tipo')->options([
                    'preventiva' => 'Preventiva',
                    'corretiva' => 'Corretiva',
                    'estetica' => 'Estetica',
                    'sinistro' => 'Sinistro',
                    'outro' => 'Outro',
                ]),
                Tables\Filters\SelectFilter::make('vehicle_id')->label('Veiculo')->relationship('vehicle', 'plate')->searchable(),
                Tables\Filters\SelectFilter::make('supplier_id')->label('Oficina')->relationship('supplier', 'name')->searchable(),
            ])
            ->actions([
                Actions\EditAction::make(),

                // Gerar PDF
                Actions\Action::make('generate_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (ServiceOrder $record) {
                        $record->load(['branch', 'vehicle', 'supplier', 'customer', 'items', 'openedByUser', 'createdBy']);

                        $logoBase64 = null;
                        $logoPath = public_path('images/logo-elite.png');
                        if (file_exists($logoPath)) {
                            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                        }

                        $authSig = null;
                        if ($record->authorization_signature_image && Storage::disk('public')->exists($record->authorization_signature_image)) {
                            $authSig = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($record->authorization_signature_image));
                        }

                        $compSig = null;
                        if ($record->completion_signature_image && Storage::disk('public')->exists($record->completion_signature_image)) {
                            $compSig = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($record->completion_signature_image));
                        }

                        $pdf = Pdf::loadView('pdf.service-order-pdf', [
                            'order' => $record,
                            'logoBase64' => $logoBase64,
                            'authSignatureBase64' => $authSig,
                            'completionSignatureBase64' => $compSig,
                        ]);

                        $filename = 'os-' . $record->id . '-' . now()->format('YmdHis') . '.pdf';
                        $path = 'so-pdfs/' . $filename;
                        Storage::disk('public')->put($path, $pdf->output());
                        $record->update(['pdf_path' => $path]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                // ===== ENVIAR AUTORIZAÇÃO (1ª assinatura) =====
                Actions\Action::make('send_authorization')
                    ->label('Enviar Autorizacao')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar para Autorizacao')
                    ->modalDescription('O cliente recebera a OS via WhatsApp para AUTORIZAR a abertura dos servicos.')
                    ->visible(fn (ServiceOrder $record) => $record->customer_id && $record->customer?->phone && ! $record->isAuthorized())
                    ->action(function (ServiceOrder $record) {
                        $record->load(['customer', 'vehicle', 'branch']);

                        $signatureUrl = url("/os/{$record->id}/assinatura");
                        $phone = $record->customer->phone;
                        $plate = $record->vehicle->plate ?? 'N/A';

                        $message = "🔧 *AUTORIZACAO DE ABERTURA - OS #{$record->id}*\n\n"
                            . "Veiculo: {$plate}\n"
                            . "Problema: {$record->description}\n\n"
                            . "📋 Para AUTORIZAR a abertura da OS, acesse:\n{$signatureUrl}\n\n"
                            . "Apos sua autorizacao, os servicos serao iniciados.\n\n"
                            . "Elite Locadora";

                        $evolution = app(EvolutionApiService::class);
                        $evolution->sendText($phone, $message);

                        $record->update(['status' => ServiceOrderStatus::AWAITING_AUTHORIZATION]);

                        \Filament\Notifications\Notification::make()
                            ->title('Autorizacao enviada!')
                            ->body("Enviada para {$phone} - Aguardando assinatura do cliente")
                            ->success()
                            ->send();
                    }),

                // ===== ENVIAR APROVAÇÃO (2ª assinatura) =====
                Actions\Action::make('send_approval')
                    ->label('Enviar Aprovacao')
                    ->icon('heroicon-o-check-badge')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar para Aprovacao Final')
                    ->modalDescription('O cliente recebera a OS concluida via WhatsApp para APROVAR os servicos realizados.')
                    ->visible(fn (ServiceOrder $record) => $record->customer_id && $record->customer?->phone && $record->isAuthorized() && ! $record->isApproved())
                    ->action(function (ServiceOrder $record) {
                        $record->load(['customer', 'vehicle', 'branch']);

                        $signatureUrl = url("/os/{$record->id}/assinatura");
                        $phone = $record->customer->phone;
                        $plate = $record->vehicle->plate ?? 'N/A';
                        $charge = $record->customer_charge > 0 ? 'R$ ' . number_format($record->customer_charge, 2, ',', '.') : 'Sem cobranca';

                        $message = "✅ *SERVICO CONCLUIDO - OS #{$record->id}*\n\n"
                            . "Veiculo: {$plate}\n"
                            . "Total OS: R$ " . number_format($record->total, 2, ',', '.') . "\n"
                            . "Valor a cobrar: {$charge}\n\n"
                            . "📋 Para APROVAR a conclusao, acesse:\n{$signatureUrl}\n\n"
                            . "Apos sua aprovacao, a fatura sera gerada.\n\n"
                            . "Elite Locadora";

                        $evolution = app(EvolutionApiService::class);
                        $evolution->sendText($phone, $message);

                        // Enviar PDF atualizado
                        if ($record->pdf_path && Storage::disk('public')->exists($record->pdf_path)) {
                            $pdfUrl = asset('storage/' . $record->pdf_path);
                            $evolution->sendDocument($phone, $pdfUrl, 'OS-' . $record->id . '.pdf');
                        }

                        $record->update(['status' => ServiceOrderStatus::AWAITING_APPROVAL]);

                        \Filament\Notifications\Notification::make()
                            ->title('Aprovacao enviada!')
                            ->body("Enviada para {$phone} - Aguardando aprovacao do cliente")
                            ->success()
                            ->send();
                    }),

                // ===== GERAR FATURA =====
                Actions\Action::make('generate_invoice')
                    ->label('Gerar Fatura')
                    ->icon('heroicon-o-banknotes')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Gerar Fatura do Cliente')
                    ->modalDescription(fn (ServiceOrder $record) => "Sera gerada uma fatura de R$ " . number_format((float) $record->customer_charge, 2, ',', '.') . " para o cliente.")
                    ->visible(fn (ServiceOrder $record) => $record->isApproved() && $record->customer_charge > 0 && ! $record->isInvoiced())
                    ->action(function (ServiceOrder $record) {
                        $record->load(['customer', 'vehicle', 'branch']);

                        // Criar Invoice
                        $invoice = \App\Models\Invoice::create([
                            'branch_id' => $record->branch_id,
                            'customer_id' => $record->customer_id,
                            'invoice_number' => 'OS-' . str_pad($record->id, 5, '0', STR_PAD_LEFT),
                            'due_date' => now()->addDays(7),
                            'amount' => $record->customer_charge,
                            'total' => $record->customer_charge,
                            'status' => \App\Enums\InvoiceStatus::OPEN,
                            'notes' => "Fatura ref. OS #{$record->id} - Veiculo {$record->vehicle?->plate}",
                        ]);

                        // Criar Conta a Receber
                        \App\Models\AccountReceivable::create([
                            'branch_id' => $record->branch_id,
                            'customer_id' => $record->customer_id,
                            'invoice_id' => $invoice->id,
                            'description' => "OS #{$record->id} - {$record->vehicle?->plate} - {$record->description}",
                            'amount' => $record->customer_charge,
                            'due_date' => now()->addDays(7),
                            'status' => 'pendente',
                        ]);

                        $record->update([
                            'invoice_id' => $invoice->id,
                            'status' => ServiceOrderStatus::INVOICED,
                        ]);

                        // Gerar PDF da fatura
                        \App\Http\Controllers\InvoiceConfirmationController::generatePdf($invoice);
                        $invoice->refresh();

                        // Enviar fatura por WhatsApp
                        if ($record->customer?->phone) {
                            $phone = $record->customer->phone;
                            $charge = 'R$ ' . number_format($record->customer_charge, 2, ',', '.');
                            $confirmUrl = url("/fatura/{$invoice->id}");

                            $message = "💰 *FATURA {$invoice->invoice_number}*\n\n"
                                . "Ref: OS #{$record->id} - {$record->vehicle?->plate}\n"
                                . "Valor: {$charge}\n"
                                . "Vencimento: " . now()->addDays(7)->format('d/m/Y') . "\n\n"
                                . "📋 Acesse o link para ver detalhes e confirmar o recebimento:\n{$confirmUrl}\n\n"
                                . "O PDF da fatura esta em anexo.\n\n"
                                . "Elite Locadora";

                            $evolution = app(EvolutionApiService::class);
                            $evolution->sendText($phone, $message);

                            // Enviar PDF
                            if ($invoice->pdf_path) {
                                $pdfUrl = asset('storage/' . $invoice->pdf_path);
                                $evolution->sendDocument($phone, $pdfUrl, 'Fatura-' . $invoice->invoice_number . '.pdf');
                            }

                            $invoice->update(['sent_at' => now()]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Fatura gerada e enviada!')
                            ->body("Fatura {$invoice->invoice_number} de R$ " . number_format($record->customer_charge, 2, ',', '.') . " criada + PDF + WhatsApp")
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceOrders::route('/'),
            'create' => Pages\CreateServiceOrder::route('/create'),
            'edit' => Pages\EditServiceOrder::route('/{record}/edit'),
        ];
    }
}
