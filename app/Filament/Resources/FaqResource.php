<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS / Site';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'Perguntas Frequentes';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Conteudo da Pergunta')->schema([
                Components\TextInput::make('question')->label('Pergunta')->required()->maxLength(255)->columnSpanFull(),
                Components\RichEditor::make('answer')->label('Resposta')->required()->columnSpanFull(),
                Components\TextInput::make('position')->label('Ordem de Exibicao')->numeric()->default(0),
                Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')->label('Pergunta')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('position')->label('Ordem')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Atualizado em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->reorderable('position')
            ->defaultSort('position', 'asc')
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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
