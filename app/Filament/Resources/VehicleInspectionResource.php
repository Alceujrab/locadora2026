<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleInspectionResource\RelationManagers;
use App\Models\VehicleInspection;
use App\Services\EvolutionApiService;
use App\Services\VehicleInspectionPdfService;
use Filament\Actions;
use App\Enums\InspectionType;
use App\Filament\Resources\VehicleInspectionResource\Pages;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleInspectionResource extends Resource
{
    protected static ?string $model = VehicleInspection::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $modelLabel = 'Vistoria';

    protected static ?string $pluralModelLabel = 'Vistorias';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detalhes da Vistoria')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('vehicle_id')
                        ->label('Veiculo')
                        ->relationship('vehicle', 'plate')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Components\Select::make('contract_id')
                        ->label('Contrato Vinculado')
                        ->relationship('contract', 'contract_number')
                        ->searchable()
                        ->preload(),
                    Components\Select::make('type')
                        ->label('Tipo de Vistoria')
                        ->options(InspectionType::class)
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    Components\DateTimePicker::make('inspection_date')->label('Data/Hora da Vistoria')->required()->native(false),
                    Components\TextInput::make('mileage')->label('Quilometragem Registrada')->numeric()->required(),
                    Components\TextInput::make('fuel_level')->label('Combustivel (%)')->numeric()->default(100)->maxValue(100)->minValue(0)->suffix('%'),
                    Components\Select::make('status')->label('Status')->options([
                        'rascunho' => 'Rascunho',
                        'finalizado' => 'Finalizado',
                    ])->default('rascunho')->required(),
                ]),
                Components\Select::make('overall_condition')->label('Condicao Geral')->options([
                    'excelente' => 'Excelente',
                    'bom' => 'Bom',
                    'regular' => 'Regular',
                    'ruim' => 'Ruim',
                ])->default('bom')->required()->columnSpanFull(),
                Components\Textarea::make('notes')->label('Observacoes')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('vehicle.plate')->label('Veiculo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contract.contract_number')->label('Contrato')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('inspection_date')->label('Data')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\IconColumn::make('signed_at')->label('Assinada')->boolean()
                    ->trueIcon('heroicon-o-check-badge')->falseIcon('heroicon-o-clock')
                    ->trueColor('success')->falseColor('gray'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'rascunho' => 'warning',
                        'finalizado' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Tipo')->options(InspectionType::class),
                Tables\Filters\SelectFilter::make('status')->options([
                    'rascunho' => 'Rascunho',
                    'finalizado' => 'Finalizado',
                ]),
            ])
            ->actions([
                Actions\Action::make('generate_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (VehicleInspection $record, VehicleInspectionPdfService $service) {
                        $path = $service->generatePdf($record);

                        return response()->streamDownload(
                            fn () => print(Storage::disk('public')->get($path)),
                            basename($path),
                            ['Content-Type' => 'application/pdf']
                        );
                    }),
                Actions\Action::make('download_pdf')
                    ->label('Baixar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (VehicleInspection $record) => $record->pdf_path ? route('inspection.signature.pdf', $record->id) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn (VehicleInspection $record) => ! empty($record->pdf_path)),
                Actions\Action::make('send_signature_whatsapp')
                    ->label('Assinar WhatsApp')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar vistoria para assinatura')
                    ->modalDescription(fn (VehicleInspection $record) => 'Enviar link de assinatura da vistoria #' . $record->id . ' para ' . ($record->contract?->customer?->name ?? 'cliente') . ' via WhatsApp?')
                    ->visible(fn (VehicleInspection $record) => $record->contract?->customer?->phone && ! $record->isSigned())
                    ->action(function (VehicleInspection $record, VehicleInspectionPdfService $pdfService) {
                        $record->load(['vehicle', 'contract.customer', 'contract.branch', 'items']);

                        if (! $record->pdf_path || ! Storage::disk('public')->exists($record->pdf_path)) {
                            $pdfService->generatePdf($record);
                            $record->refresh();
                        }

                        if (! $record->signature_token) {
                            $record->update(['signature_token' => Str::random(64)]);
                            $record->refresh();
                        }

                        $phone = $record->contract->customer->phone;
                        $signUrl = route('inspection.signature.show', $record->id);
                        $message = "📋 *VISTORIA DO VEICULO*\n\n"
                            . 'Vistoria: #' . $record->id . "\n"
                            . 'Contrato: ' . ($record->contract?->contract_number ?? '-') . "\n"
                            . 'Veiculo: ' . ($record->vehicle?->plate ?? '-') . ' - ' . ($record->vehicle?->brand ?? '') . ' ' . ($record->vehicle?->model ?? '') . "\n"
                            . 'Tipo: ' . $record->type->label() . "\n"
                            . 'Data: ' . ($record->inspection_date?->format('d/m/Y H:i') ?? '-') . "\n"
                            . 'KM: ' . number_format((int) $record->mileage, 0, ',', '.') . " km\n\n"
                            . "✍️ *Clique no link abaixo para assinar digitalmente a vistoria:*\n{$signUrl}\n\n"
                            . 'O PDF da vistoria segue em anexo.\n\n'
                            . 'Elite Locadora';

                        $evolution = app(EvolutionApiService::class);
                        $evolution->sendText($phone, $message);

                        if ($record->pdf_path) {
                            $pdfUrl = asset('storage/' . $record->pdf_path);
                            $evolution->sendDocument($phone, $pdfUrl, 'Vistoria-' . $record->id . '.pdf');
                        }

                        Notification::make()
                            ->title('Vistoria enviada para assinatura')
                            ->body('Link e PDF enviados para ' . $phone)
                            ->success()
                            ->send();
                    }),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleInspections::route('/'),
            'create' => Pages\CreateVehicleInspection::route('/create'),
            'edit' => Pages\EditVehicleInspection::route('/{record}/edit'),
        ];
    }
}
