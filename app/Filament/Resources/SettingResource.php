<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $modelLabel = 'Configuracao';

    protected static ?string $pluralModelLabel = 'Configuracoes do Sistema';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Chave e Valor Global')->schema([
                Grid::make(3)->schema([
                    Components\TextInput::make('key')->label('Chave Identificadora')->required()->maxLength(255),
                    Components\TextInput::make('group')->label('Grupo (Ex: general, api)')->default('general')->required()->maxLength(255),
                    Components\Select::make('type')->label('Tipo de Dado')->options([
                        'string' => 'Texto / String',
                        'integer' => 'Numero / Inteiro',
                        'boolean' => 'Booleano (True/False)',
                        'json' => 'Objeto JSON',
                    ])->default('string')->required(),
                ]),
                Grid::make(2)->schema([
                    Components\Textarea::make('value')->label('Valor Configurado')->required(),
                    Components\Textarea::make('description')->label('Descricao (Como e usada no sistema)'),
                ]),
                Components\Select::make('branch_id')->label('Filial Especifica (Opcional)')->relationship('branch', 'name')->searchable()->preload(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('Chave')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('value')->label('Valor')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('group')->label('Grupo')->searchable()->sortable()->badge(),
                Tables\Columns\TextColumn::make('branch.name')->label('Filial')->searchable()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')->label('Grupo')->options(
                    fn () => Setting::query()->distinct()->pluck('group', 'group')->toArray()
                ),
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
            ->defaultSort('group', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
