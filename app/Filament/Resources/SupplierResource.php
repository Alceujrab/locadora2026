<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Cadastros';

    protected static ?string $modelLabel = 'Fornecedor';

    protected static ?string $pluralModelLabel = 'Fornecedores';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Section::make('Dados do Fornecedor')->schema([
                Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload(),
                Components\Select::make('type')->label('Tipo')->options([
                    'oficina' => 'Oficina', 'pecas' => 'Pecas', 'seguro' => 'Seguro',
                    'combustivel' => 'Combustivel', 'outros' => 'Outros',
                ])->required(),
                Components\TextInput::make('name')->label('Nome / Razao Social')->required(),
                Components\TextInput::make('cnpj')->label('CNPJ'),
                Components\TextInput::make('contact_name')->label('Contato'),
                Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ])->columns(3),
            \Filament\Schemas\Components\Section::make('Contato')->schema([
                Components\TextInput::make('phone')->label('Telefone')->tel(),
                Components\TextInput::make('email')->label('E-mail')->email(),
            ])->columns(2),
            \Filament\Schemas\Components\Section::make('Endereco')->schema([
                Components\TextInput::make('address_zip')->label('CEP'),
                Components\TextInput::make('address_street')->label('Rua'),
                Components\TextInput::make('address_number')->label('Numero'),
                Components\TextInput::make('address_complement')->label('Complemento'),
                Components\TextInput::make('address_neighborhood')->label('Bairro'),
                Components\TextInput::make('address_city')->label('Cidade'),
                Components\TextInput::make('address_state')->label('UF')->maxLength(2),
            ])->columns(3),
            \Filament\Schemas\Components\Section::make('Informacoes Adicionais')->schema([
                Components\Textarea::make('specialties')->label('Especialidades'),
                Components\Textarea::make('notes')->label('Observacoes'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('cnpj')->label('CNPJ')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefone'),
                Tables\Columns\TextColumn::make('email')->label('E-mail'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Tipo')->options([
                    'oficina' => 'Oficina', 'pecas' => 'Pecas', 'seguro' => 'Seguro',
                    'combustivel' => 'Combustivel', 'outros' => 'Outros',
                ]),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
