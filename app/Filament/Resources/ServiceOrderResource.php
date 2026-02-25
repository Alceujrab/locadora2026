<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Enums\ServiceOrderStatus;
use App\Filament\Resources\ServiceOrderResource\Pages;
use App\Filament\Resources\ServiceOrderResource\RelationManagers;
use App\Models\ServiceOrder;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

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
        return ['vehicle.plate', 'supplier.name', 'description'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detalhes Básicos da OS')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\Select::make('vehicle_id')->label('Veiculo')->relationship('vehicle', 'plate')->searchable()->preload()->required(),
                    Components\Select::make('supplier_id')->label('Fornecedor/Oficina')->relationship('supplier', 'name')->searchable()->preload()->required(),
                ]),
                Grid::make(3)->schema([
                    Components\Select::make('type')->label('Tipo de Servico')->options([
                        'preventiva' => 'Preventiva',
                        'corretiva' => 'Corretiva',
                        'estetica' => 'Estética',
                        'sinistro' => 'Sinistro',
                        'outro' => 'Outro',
                    ])->required()->default('preventiva'),
                    Components\DateTimePicker::make('opened_at')->label('Data de Abertura')->native(false)->required()->default(now()),
                    Components\Select::make('status')->label('Status Atual')->options(ServiceOrderStatus::class)->default(ServiceOrderStatus::OPEN)->required(),
                ]),
                Components\TextInput::make('description')->label('Problema / Descricao')->required()->maxLength(255)->columnSpanFull(),
            ]),

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
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.plate')->label('Veiculo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Oficina')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('opened_at')->label('Abertura')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(ServiceOrderStatus::class),
                Tables\Filters\SelectFilter::make('vehicle_id')->label('Veiculo')->relationship('vehicle', 'plate')->searchable(),
                Tables\Filters\SelectFilter::make('supplier_id')->label('Oficina')->relationship('supplier', 'name')->searchable(),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
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
