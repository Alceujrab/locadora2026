<?php

namespace App\Filament\Resources;

use App\Enums\ContractStatus;
use App\Enums\InspectionType;
use App\Enums\ReservationStatus;
use App\Enums\VehicleStatus;
use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
use App\Services\ContractService;
use App\Services\InvoiceService;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $modelLabel = 'Contrato';

    protected static ?string $pluralModelLabel = 'Contratos';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'contract_number';

    public static function getGloballySearchableAttributes(): array
    {
        return ['contract_number', 'customer.name', 'vehicle.plate'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Contrato')->tabs([
                Tabs\Tab::make('Dados Principais')->icon('heroicon-o-document')->schema([
                    Components\TextInput::make('contract_number')->label('Numero do Contrato')->disabled()->dehydrated(false)->maxLength(255),
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\Select::make('customer_id')->label('Cliente')->relationship('customer', 'name')->searchable()->preload()->required(),
                    Components\Select::make('vehicle_id')->label('Veiculo')->relationship('vehicle', 'plate')->searchable()->preload()->required(),
                    Components\Select::make('reservation_id')->label('Reserva Vinculada')->relationship('reservation', 'id')->searchable()->preload(),
                    Components\Select::make('template_id')->label('Template do Contrato')->relationship('template', 'name')->searchable()->preload(),
                    Components\Select::make('status')->label('Status')->options(ContractStatus::class)->default(ContractStatus::DRAFT)->required(),
                    Components\Textarea::make('notes')->label('Observacoes')->columnSpanFull(),
                ])->columns(3),

                Tabs\Tab::make('Datas e KM')->icon('heroicon-o-clock')->schema([
                    Components\DateTimePicker::make('pickup_date')->label('Data/Hora Retirada')->required(),
                    Components\TextInput::make('pickup_mileage')->label('KM Retirada')->numeric(),
                    Components\DateTimePicker::make('return_date')->label('Data/Hora Devolucao Prevista')->required(),
                    Components\TextInput::make('return_mileage')->label('KM Devolucao')->numeric(),
                    Components\DateTimePicker::make('actual_return_date')->label('Data/Hora Devolucao Efetiva'),
                ])->columns(2),

                Tabs\Tab::make('Valores e Pagamentos')->icon('heroicon-o-currency-dollar')->schema([
                    Components\TextInput::make('daily_rate')->label('Valor da Diaria (R$)')->numeric()->prefix('R$')->required(),
                    Components\TextInput::make('total_days')->label('Total de Dias')->numeric()->required(),
                    Components\TextInput::make('extras_total')->label('Total Extras (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('caution_amount')->label('Caucao (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('discount')->label('Desconto (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('additional_charges')->label('Acrescimos (R$)')->numeric()->prefix('R$')->default(0),
                    Components\Textarea::make('additional_charges_description')->label('Descricao de Acrescimos')->columnSpanFull(),
                    Components\TextInput::make('total')->label('Valor Total (R$)')->numeric()->prefix('R$')->required(),
                ])->columns(3),

                Tabs\Tab::make('Assinatura')->icon('heroicon-o-pencil-square')->schema([
                    Components\DateTimePicker::make('signed_at')->label('Data da Assinatura')->disabled(),
                    Components\TextInput::make('signature_ip')->label('IP da Assinatura')->disabled(),
                    Components\TextInput::make('signature_method')->label('Metodo')->disabled(),
                    Components\TextInput::make('signature_hash')->label('Hash')->disabled()->columnSpanFull(),
                ])->columns(3),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')->label('Contrato')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('vehicle.plate')->label('Veiculo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pickup_date')->label('Retirada')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('return_date')->label('Devolucao')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\IconColumn::make('signed_at')->label('Assinado')->boolean()
                    ->trueIcon('heroicon-o-check-badge')->falseIcon('heroicon-o-clock')
                    ->trueColor('success')->falseColor('warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(ContractStatus::class),
                Tables\Filters\SelectFilter::make('branch_id')->label('Filial')->relationship('branch', 'name'),
            ])
            ->actions(array_merge([
                Actions\EditAction::make(),
            ], self::getCustomActions()))
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Reusable actions to be shown on Table and Edit page Header.
     */
    public static function getCustomActions(): array
    {
        return [
            Actions\Action::make('checkout')
                ->label('Retirada')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Retirada do Veículo')
                ->modalDescription('Deseja ativar este contrato e entregar o veículo ao cliente? Isto criará a vistoria de saída.')
                ->visible(fn (Contract $record) => in_array($record->status, [ContractStatus::DRAFT, ContractStatus::AWAITING_SIGNATURE]))
                ->action(function (Contract $record) {
                    $inspection = \App\Models\VehicleInspection::firstOrCreate([
                        'contract_id' => $record->id,
                        'type' => InspectionType::CHECKOUT,
                    ], [
                        'vehicle_id' => $record->vehicle_id,
                        'inspector_user_id' => auth()->id() ?? 1,
                        'status' => 'rascunho',
                        'inspection_date' => now(),
                        'mileage' => $record->vehicle->mileage ?? 0,
                        'fuel_level' => 100,
                        'overall_condition' => 'Bom',
                    ]);

                    $record->update(['status' => ContractStatus::ACTIVE]);
                    if ($record->reservation) {
                        $record->reservation->update(['status' => ReservationStatus::IN_PROGRESS]);
                    }
                    $record->vehicle->update(['status' => VehicleStatus::RENTED]);

                    Notification::make()->title('Retirada realizada com sucesso!')->success()->send();
                }),

            Actions\Action::make('checkin')
                ->label('Devolução')
                ->icon('heroicon-o-arrow-left-on-rectangle')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Devolução do Veículo')
                ->modalDescription('Deseja finalizar o contrato e receber o veículo de volta? Isto criará a vistoria de retorno.')
                ->visible(fn (Contract $record) => $record->status === ContractStatus::ACTIVE)
                ->action(function (Contract $record) {
                    $inspection = \App\Models\VehicleInspection::firstOrCreate([
                        'contract_id' => $record->id,
                        'type' => InspectionType::RETURN,
                    ], [
                        'vehicle_id' => $record->vehicle_id,
                        'inspector_user_id' => auth()->id() ?? 1,
                        'status' => 'rascunho',
                        'inspection_date' => now(),
                        'mileage' => $record->vehicle->mileage ?? $record->pickup_mileage ?? 0,
                        'fuel_level' => 100,
                        'overall_condition' => 'Bom',
                    ]);

                    $record->update([
                        'status' => ContractStatus::FINISHED,
                        'actual_return_date' => now(),
                    ]);
                    if ($record->reservation) {
                        $record->reservation->update(['status' => ReservationStatus::COMPLETED]);
                    }
                    $record->vehicle->update(['status' => VehicleStatus::AVAILABLE]);

                    Notification::make()->title('Devolução realizada com sucesso!')->success()->send();
                }),

            Actions\Action::make('generateInvoices')
                ->label('Gerar Fatura')
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Gerar Fatura')
                ->modalDescription('Deseja processar a fatura deste contrato agora?')
                ->visible(fn (Contract $record) => in_array($record->status, [ContractStatus::ACTIVE, ContractStatus::FINISHED]) && ! $record->invoices()->exists())
                ->action(function (Contract $record, InvoiceService $service) {
                    $invoices = $service->generateForContract($record, 1, 5);
                    if (count($invoices) > 0) {
                        Notification::make()->title('Fatura gerada com sucesso!')->success()->send();
                    } else {
                        Notification::make()->title('Erro ao gerar fatura.')->danger()->send();
                    }
                }),

            Actions\Action::make('generatePdf')
                ->label('Gerar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->visible(fn (Contract $record) => $record->template_id !== null)
                ->action(function (Contract $record, ContractService $service) {
                    $result = $service->generatePdf($record);
                    if ($result) {
                        Notification::make()->title('PDF gerado com sucesso!')->success()->send();
                    } else {
                        Notification::make()->title('Erro ao gerar PDF. Verifique o template.')->danger()->send();
                    }
                }),

            Actions\Action::make('downloadPdf')
                ->label('Baixar PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn (Contract $record) => $record->pdf_path ? asset('storage/'.$record->pdf_path) : '#')
                ->openUrlInNewTab()
                ->visible(fn (Contract $record) => ! empty($record->pdf_path)),

            // ENVIAR PARA ASSINATURA VIA WHATSAPP
            Actions\Action::make('sendSignatureWhatsApp')
                ->label('Assinar WhatsApp')
                ->icon('heroicon-o-pencil-square')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Enviar Contrato para Assinatura')
                ->modalDescription(fn (Contract $record) => "Enviar link de assinatura do contrato {$record->contract_number} para {$record->customer?->name} via WhatsApp?")
                ->visible(fn (Contract $record) => ! $record->isSigned() && $record->pdf_path && $record->customer?->phone)
                ->action(function (Contract $record) {
                    // Garantir status aguardando_assinatura
                    if ($record->status === ContractStatus::DRAFT) {
                        $record->update(['status' => ContractStatus::AWAITING_SIGNATURE]);
                    }

                    // Garantir token de assinatura
                    if (! $record->signature_token) {
                        $record->update(['signature_token' => \Illuminate\Support\Str::random(64)]);
                    }

                    $phone = $record->customer->phone;
                    $signUrl = route('contract.signature.show', $record->id);
                    $total = 'R$ ' . number_format((float) $record->total, 2, ',', '.');

                    $message = "📋 *CONTRATO DE LOCAÇÃO*\n\n"
                        . "Contrato: {$record->contract_number}\n"
                        . "Cliente: {$record->customer->name}\n"
                        . "Veículo: {$record->vehicle?->plate} - {$record->vehicle?->brand} {$record->vehicle?->model}\n"
                        . "Período: {$record->pickup_date?->format('d/m/Y')} a {$record->return_date?->format('d/m/Y')}\n"
                        . "Total: {$total}\n\n"
                        . "✍️ *Clique no link abaixo para assinar digitalmente:*\n{$signUrl}\n\n"
                        . "Após assinar, você poderá baixar o contrato assinado em PDF.\n\n"
                        . "Elite Locadora";

                    $evolution = app(\App\Services\EvolutionApiService::class);
                    $evolution->sendText($phone, $message);

                    // Enviar PDF do contrato
                    if ($record->pdf_path) {
                        $pdfUrl = asset('storage/' . $record->pdf_path);
                        $evolution->sendDocument($phone, $pdfUrl, 'Contrato-' . $record->contract_number . '.pdf');
                    }

                    Notification::make()
                        ->title('Contrato enviado para assinatura!')
                        ->body("Enviado para {$phone} com link de assinatura e PDF")
                        ->success()
                        ->send();
                }),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
