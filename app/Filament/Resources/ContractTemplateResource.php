<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\ContractTemplateResource\Pages;
use App\Models\ContractTemplate;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ContractTemplateResource extends Resource
{
    protected static ?string $model = ContractTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $modelLabel = 'Template de Contrato';

    protected static ?string $pluralModelLabel = 'Templates de Contrato';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Configuracoes Gerais')->schema([
                Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                Components\TextInput::make('name')->label('Nome do Template')->required()->maxLength(255),
                Components\Toggle::make('is_default')->label('Define como Padrao')->default(false),
                Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ])->columns(2),

            Section::make('Conteudo')->schema([
                Components\RichEditor::make('content')
                    ->label('Conteudo do Documento')
                    ->columnSpanFull()
                    ->required()
                    ->toolbarButtons([
                        'attachFiles', 'blockquote', 'bold', 'bulletList',
                        'codeBlock', 'h2', 'h3', 'italic', 'link',
                        'orderedList', 'redo', 'strike', 'underline', 'undo',
                    ]),
                Components\KeyValue::make('variables')
                    ->label('Variaveis Disponiveis')
                    ->keyLabel('Nome da Variavel')
                    ->valueLabel('Descricao')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('branch.name')->label('Filial')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_default')->label('Padrao')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y')->sortable(),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractTemplates::route('/'),
            'create' => Pages\CreateContractTemplate::route('/create'),
            'edit' => Pages\EditContractTemplate::route('/{record}/edit'),
        ];
    }
}
