<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleCategoryResource\Pages;
use App\Models\VehicleCategory;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleCategoryResource extends Resource
{
    protected static ?string $model = VehicleCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Cadastros';

    protected static ?string $modelLabel = 'Categoria de Veiculo';

    protected static ?string $pluralModelLabel = 'Categorias de Veiculos';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Section::make('Dados da Categoria')->schema([
                Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload(),
                Components\TextInput::make('name')->label('Nome')->required()->maxLength(255),
                Components\Textarea::make('description')->label('Descricao')->columnSpanFull(),
                Components\TextInput::make('icon')->label('Icone')->maxLength(50),
                Components\TextInput::make('sort_order')->label('Ordem')->numeric()->default(0),
                Components\Toggle::make('is_active')->label('Ativa')->default(true),
            ])->columns(3),

            \Filament\Schemas\Components\Section::make('Tarifas')->schema([
                Components\TextInput::make('daily_rate')->label('Diaria (R$)')->numeric()->prefix('R$')->required(),
                Components\TextInput::make('weekly_rate')->label('Semanal (R$)')->numeric()->prefix('R$'),
                Components\TextInput::make('monthly_rate')->label('Mensal (R$)')->numeric()->prefix('R$'),
                Components\TextInput::make('insurance_daily')->label('Seguro Diario (R$)')->numeric()->prefix('R$'),
            ])->columns(4),

            \Filament\Schemas\Components\Section::make('Quilometragem')->schema([
                Components\Select::make('km_type')->label('Tipo de KM')->options(['livre' => 'Livre', 'controlada' => 'Controlada']),
                Components\TextInput::make('km_included')->label('KM Incluidos')->numeric(),
                Components\TextInput::make('km_rate')->label('Valor KM Excedente (R$)')->numeric()->prefix('R$'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('branch.name')->label('Filial')->sortable(),
                Tables\Columns\TextColumn::make('daily_rate')->label('Diaria')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('weekly_rate')->label('Semanal')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.')),
                Tables\Columns\TextColumn::make('monthly_rate')->label('Mensal')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.')),
                Tables\Columns\TextColumn::make('vehicles_count')->label('Veiculos')->counts('vehicles')->formatStateUsing(fn ($state) => (string) $state)->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativa')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status'),
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
            'index' => Pages\ListVehicleCategories::route('/'),
            'create' => Pages\CreateVehicleCategory::route('/create'),
            'edit' => Pages\EditVehicleCategory::route('/{record}/edit'),
        ];
    }
}
