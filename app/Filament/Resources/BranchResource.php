<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Cadastros';

    protected static ?string $modelLabel = 'Filial';

    protected static ?string $pluralModelLabel = 'Filiais';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Section::make('Dados da Filial')->schema([
                Components\TextInput::make('name')->label('Nome')->required()->maxLength(255),
                Components\TextInput::make('cnpj')->label('CNPJ')->maxLength(18),
                Components\TextInput::make('state_registration')->label('Inscricao Estadual')->maxLength(20),
                Components\FileUpload::make('logo')->label('Logo')->image()->directory('branches/logos')->columnSpanFull(),
            ])->columns(3),

            \Filament\Schemas\Components\Section::make('Contato')->schema([
                Components\TextInput::make('phone')->label('Telefone')->tel(),
                Components\TextInput::make('whatsapp')->label('WhatsApp')->tel(),
                Components\TextInput::make('email')->label('E-mail')->email(),
            ])->columns(3),

            \Filament\Schemas\Components\Section::make('Endereco')->schema([
                Components\TextInput::make('address_zip')->label('CEP')->maxLength(9),
                Components\TextInput::make('address_street')->label('Rua')->maxLength(255),
                Components\TextInput::make('address_number')->label('Numero')->maxLength(10),
                Components\TextInput::make('address_complement')->label('Complemento')->maxLength(100),
                Components\TextInput::make('address_neighborhood')->label('Bairro')->maxLength(100),
                Components\TextInput::make('address_city')->label('Cidade')->maxLength(100),
                Components\TextInput::make('address_state')->label('UF')->maxLength(2),
            ])->columns(3),

            \Filament\Schemas\Components\Section::make('Configuracoes')->schema([
                Components\Toggle::make('is_active')->label('Ativa')->default(true),
                Components\Textarea::make('notes')->label('Observacoes')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cnpj')->label('CNPJ')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefone'),
                Tables\Columns\TextColumn::make('email')->label('E-mail'),
                Tables\Columns\TextColumn::make('address_city')->label('Cidade'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativa')->boolean()->sortable(),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
