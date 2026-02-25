<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Enums\InspectionType;
use App\Filament\Resources\VehicleInspectionResource\Pages;
use App\Filament\Resources\VehicleInspectionResource\RelationManagers;
use App\Models\VehicleInspection;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

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
                    Components\TextInput::make('fuel_level')->label('Combustivel (%)')->numeric()->maxValue(100)->minValue(0)->suffix('%'),
                    Components\Select::make('status')->label('Status')->options([
                        'rascunho' => 'Rascunho',
                        'finalizado' => 'Finalizado',
                    ])->default('rascunho')->required(),
                ]),
                Components\Textarea::make('overall_condition')->label('Condicao Geral')->columnSpanFull(),
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
                Actions\EditAction::make(),
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
