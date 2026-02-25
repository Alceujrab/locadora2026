<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\MaintenanceAlertResource\Pages;
use App\Models\MaintenanceAlert;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MaintenanceAlertResource extends Resource
{
    protected static ?string $model = MaintenanceAlert::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static string|\UnitEnum|null $navigationGroup = 'Servicos';

    protected static ?string $modelLabel = 'Alerta de Manutencao';

    protected static ?string $pluralModelLabel = 'Alertas de Manutencao';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Configuracao do Alerta')->schema([
                Grid::make(2)->schema([
                    Components\Select::make('vehicle_id')->label('Veiculo')->relationship('vehicle', 'plate')->searchable()->preload()->required(),
                    Components\TextInput::make('type')->label('Tipo de Servico (Ex: Oleo, Correia)')->required()->maxLength(255),
                ]),
                Components\Textarea::make('description')->label('Descricao / Recomendacao')->columnSpanFull(),
            ]),

            Section::make('Gatilhos (Triggers)')->schema([
                Grid::make(2)->schema([
                    Components\TextInput::make('trigger_km')->label('Avisar a cada X Km')->numeric()->suffix('Km'),
                    Components\TextInput::make('trigger_days')->label('Avisar a cada X Dias')->numeric()->suffix('Dias'),
                ]),
            ]),

            Section::make('Historico do Ultimo Servico')->schema([
                Grid::make(3)->schema([
                    Components\DatePicker::make('last_service_date')->label('Data do Ultimo Servico')->native(false),
                    Components\TextInput::make('last_service_km')->label('Km no Ultimo Servico')->numeric(),
                    Components\DateTimePicker::make('last_triggered_at')->label('Ultimo Alerta Gerado Em')->disabled(),
                ]),
                Components\Toggle::make('is_active')->label('Alerta Ativo')->default(true),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.plate')->label('Veiculo')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('trigger_km')->label('Gatilho KM')->suffix(' Km')->sortable(),
                Tables\Columns\TextColumn::make('trigger_days')->label('Gatilho Tempo')->suffix(' Dias')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
                Tables\Columns\TextColumn::make('last_service_date')->label('Ultimo Servico')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')->label('Veiculo')->relationship('vehicle', 'plate'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceAlerts::route('/'),
            'create' => Pages\CreateMaintenanceAlert::route('/create'),
            'edit' => Pages\EditMaintenanceAlert::route('/{record}/edit'),
        ];
    }
}
