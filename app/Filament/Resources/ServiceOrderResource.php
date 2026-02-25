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
                    Components\Select::make('customer_id')->label('Locatario Vinculado')->relationship('customer', 'full_name')->searchable()->preload()->helperText('Para envio de assinatura digital'),
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
                Grid::make(3)->schema([
                    Components\TextInput::make('items_total')->label('Total Pecas (R$)')->numeric()->prefix('R$')->disabled()->dehydrated()->default(0),
                    Components\TextInput::make('labor_total')->label('Total Mao de Obra (R$)')->numeric()->prefix('R$')->disabled()->dehydrated()->default(0),
                    Components\TextInput::make('total')->label('Total Geral (R$)')->numeric()->prefix('R$')->disabled()->dehydrated()->default(0),
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
                Tables\Columns\TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
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

                        $pdf = Pdf::loadView('pdf.service-order-pdf', ['order' => $record]);
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

                // Enviar WhatsApp
                Actions\Action::make('send_whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar OS por WhatsApp')
                    ->modalDescription('A OS sera enviada como PDF para o locatario assinar digitalmente.')
                    ->visible(fn (ServiceOrder $record) => $record->customer_id && $record->customer?->phone)
                    ->action(function (ServiceOrder $record) {
                        $record->load(['customer', 'vehicle', 'branch']);

                        // Gerar PDF se não existir
                        if (! $record->pdf_path || ! Storage::disk('public')->exists($record->pdf_path)) {
                            $pdf = Pdf::loadView('pdf.service-order-pdf', ['order' => $record]);
                            $filename = 'os-' . $record->id . '-' . now()->format('YmdHis') . '.pdf';
                            $path = 'so-pdfs/' . $filename;
                            Storage::disk('public')->put($path, $pdf->output());
                            $record->update(['pdf_path' => $path]);
                        }

                        // Gerar token de assinatura
                        if (! $record->signature_token) {
                            $record->update(['signature_token' => Str::random(40)]);
                        }

                        $signatureUrl = url("/os/{$record->id}/assinatura");
                        $phone = $record->customer->phone;
                        $vehiclePlate = $record->vehicle->plate ?? 'N/A';

                        $message = "🔧 *ORDEM DE SERVICO #{$record->id}*\n\n"
                            . "Veiculo: {$vehiclePlate}\n"
                            . "Tipo: {$record->type}\n"
                            . "Descricao: {$record->description}\n\n"
                            . "📋 Para visualizar e assinar a OS, acesse:\n{$signatureUrl}\n\n"
                            . "Elite Locadora";

                        $evolution = app(EvolutionApiService::class);

                        // Enviar mensagem de texto
                        $evolution->sendText($phone, $message);

                        // Enviar PDF
                        $pdfUrl = asset('storage/' . $record->pdf_path);
                        $evolution->sendDocument($phone, $pdfUrl, 'OS-' . $record->id . '.pdf');

                        $record->update(['status' => ServiceOrderStatus::AWAITING_SIGNATURE]);

                        \Filament\Notifications\Notification::make()
                            ->title('OS enviada por WhatsApp!')
                            ->body("Enviada para {$phone}")
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
