<?php

namespace App\Filament\Resources;

use App\Enums\VehicleStatus;
use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestao de Frota';

    protected static ?string $modelLabel = 'Veiculo';

    protected static ?string $pluralModelLabel = 'Veiculos';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'plate';

    public static function getGloballySearchableAttributes(): array
    {
        return ['plate', 'brand', 'model', 'renavam', 'chassis'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Tabs::make('Veiculo')->tabs([
                \Filament\Schemas\Components\Tabs\Tab::make('Dados do Veiculo')->icon('heroicon-o-truck')->schema([
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\Select::make('category_id')->label('Categoria')->relationship('category', 'name')->searchable()->preload(),
                    Components\TextInput::make('brand')->label('Marca')->required(),
                    Components\TextInput::make('model')->label('Modelo')->required(),
                    Components\TextInput::make('year_manufacture')->label('Ano Fabricacao')->numeric(),
                    Components\TextInput::make('year_model')->label('Ano Modelo')->numeric(),
                    Components\TextInput::make('color')->label('Cor'),
                    Components\Select::make('fuel')->label('Combustivel')->options([
                        'gasolina' => 'Gasolina', 'etanol' => 'Etanol', 'flex' => 'Flex',
                        'diesel' => 'Diesel', 'eletrico' => 'Eletrico', 'hibrido' => 'Hibrido',
                    ]),
                    Components\Select::make('transmission')->label('Cambio')->options([
                        'manual' => 'Manual', 'automatico' => 'Automatico', 'cvt' => 'CVT', 'automatizado' => 'Automatizado',
                    ]),
                    Components\TextInput::make('doors')->label('Portas')->numeric(),
                    Components\TextInput::make('seats')->label('Lugares')->numeric(),
                    Components\TextInput::make('trunk_capacity')->label('Porta-malas (L)')->numeric(),
                ])->columns(4),

                \Filament\Schemas\Components\Tabs\Tab::make('Documentacao')->icon('heroicon-o-document-text')->schema([
                    Components\TextInput::make('plate')->label('Placa')->required()->unique(ignoreRecord: true),
                    Components\TextInput::make('renavam')->label('RENAVAM'),
                    Components\TextInput::make('chassis')->label('Chassi'),
                    Components\TextInput::make('mileage')->label('Quilometragem')->numeric()->suffix('km'),
                    Components\DatePicker::make('ipva_due_date')->label('Vencimento IPVA'),
                    Components\DatePicker::make('licensing_due_date')->label('Vencimento Licenciamento'),
                    Components\DatePicker::make('insurance_expiry_date')->label('Vencimento Seguro'),
                ])->columns(3),

                \Filament\Schemas\Components\Tabs\Tab::make('Valores')->icon('heroicon-o-currency-dollar')->schema([
                    Components\TextInput::make('daily_rate_override')->label('Diaria Personalizada')->numeric()->prefix('R$')
                        ->helperText('Deixe vazio para usar o valor da categoria'),
                    Components\TextInput::make('weekly_rate_override')->label('Semanal Personalizada')->numeric()->prefix('R$'),
                    Components\TextInput::make('monthly_rate_override')->label('Mensal Personalizada')->numeric()->prefix('R$'),
                    Components\TextInput::make('purchase_value')->label('Valor de Compra')->numeric()->prefix('R$'),
                    Components\TextInput::make('fipe_value')->label('Valor FIPE')->numeric()->prefix('R$'),
                    Components\TextInput::make('insurance_value')->label('Valor Seguro')->numeric()->prefix('R$'),
                    Components\DatePicker::make('purchase_date')->label('Data de Compra'),
                ])->columns(3),

                \Filament\Schemas\Components\Tabs\Tab::make('Status & Obs')->icon('heroicon-o-cog-6-tooth')->schema([
                    Components\Select::make('status')->label('Status')->options(VehicleStatus::class)->required(),
                    Components\Toggle::make('is_featured')->label('Destaque'),
                    Components\Textarea::make('description')->label('Descricao')->columnSpanFull(),
                    Components\Textarea::make('notes')->label('Observacoes')->columnSpanFull(),
                ])->columns(2),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plate')->label('Placa')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('brand')->label('Marca')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('model')->label('Modelo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('year_model')->label('Ano')->sortable(),
                Tables\Columns\TextColumn::make('color')->label('Cor'),
                Tables\Columns\TextColumn::make('category.name')->label('Categoria')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('mileage')->label('KM')->formatStateUsing(fn ($state) => number_format((float) $state, 0, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('branch.name')->label('Filial')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(VehicleStatus::class),
                Tables\Filters\SelectFilter::make('category_id')->label('Categoria')->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('branch_id')->label('Filial')->relationship('branch', 'name'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Destaque'),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\Action::make('mark_available')
                        ->label('Marcar Disponivel')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Vehicle $record) => $record->status !== VehicleStatus::AVAILABLE && $record->status !== VehicleStatus::RENTED)
                        ->requiresConfirmation()
                        ->action(function (Vehicle $record) {
                            $record->update(['status' => VehicleStatus::AVAILABLE]);
                            Notification::make()->title('Veiculo agora esta disponivel!')->success()->send();
                        }),
                    Actions\Action::make('send_maintenance')
                        ->label('Enviar p/ Manutencao')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('warning')
                        ->visible(fn (Vehicle $record) => $record->status === VehicleStatus::AVAILABLE)
                        ->form([
                            \Filament\Forms\Components\Textarea::make('reason')->label('Motivo da Manutencao')->required(),
                        ])
                        ->action(function (Vehicle $record, array $data) {
                            $record->update(['status' => VehicleStatus::MAINTENANCE]);
                            \App\Models\MaintenanceAlert::create([
                                'vehicle_id' => $record->id,
                                'type' => 'Corretiva',
                                'description' => $data['reason'],
                            ]);
                            Notification::make()->title('Enviado para manutencao!')->success()->send();
                        }),
                    Actions\Action::make('mark_inactive')
                        ->label('Inativar Veiculo')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->visible(fn (Vehicle $record) => $record->status !== VehicleStatus::INACTIVE && $record->status !== VehicleStatus::RENTED)
                        ->requiresConfirmation()
                        ->action(function (Vehicle $record) {
                            $record->update(['status' => VehicleStatus::INACTIVE]);
                            Notification::make()->title('Veiculo inativado!')->success()->send();
                        }),
                ])->label('Acoes Rapidas')->icon('heroicon-m-bolt')->color('primary'),
                
                Actions\Action::make('dashboard')
                    ->label('Dashboard')
                    ->icon('heroicon-o-chart-pie')
                    ->color('info')
                    ->url(fn (Vehicle $record): string => static::getUrl('view', ['record' => $record])),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('plate');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'view' => Pages\ViewVehicle::route('/{record}'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
