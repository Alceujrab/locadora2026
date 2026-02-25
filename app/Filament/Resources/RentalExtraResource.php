<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\RentalExtraResource\Pages;
use App\Models\RentalExtra;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RentalExtraResource extends Resource
{
    protected static ?string $model = RentalExtra::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $modelLabel = 'Opcional/Extra';

    protected static ?string $pluralModelLabel = 'Opcionais/Extras';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detalhes do Opcional')->schema([
                Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                Components\TextInput::make('name')->label('Nome do Servico/Item')->required()->maxLength(255),
                Components\Select::make('type')->label('Tipo de Cobranca')->options([
                    'diario' => 'Por Diaria',
                    'fixo' => 'Valor Fixo (Unico)',
                ])->required()->default('diario'),
                Components\TextInput::make('daily_rate')->label('Valor (R$)')->numeric()->prefix('R$')->required(),
                Components\Textarea::make('description')->label('Descricao')->columnSpanFull(),
                Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('branch.name')->label('Filial')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('daily_rate')->label('Valor')
                    ->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')->label('Filial')->relationship('branch', 'name'),
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
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalExtras::route('/'),
            'create' => Pages\CreateRentalExtra::route('/create'),
            'edit' => Pages\EditRentalExtra::route('/{record}/edit'),
        ];
    }
}
