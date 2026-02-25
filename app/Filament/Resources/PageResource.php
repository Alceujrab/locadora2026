<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS / Site';

    protected static ?string $modelLabel = 'Pagina';

    protected static ?string $pluralModelLabel = 'Paginas Internas';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Conteudo da Pagina Customizada')->schema([
                Grid::make(3)->schema([
                    Components\TextInput::make('title')
                        ->label('Titulo')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                        ->maxLength(255),
                    Components\TextInput::make('slug')
                        ->label('Slug da URL')
                        ->required()
                        ->unique(Page::class, 'slug', ignoreRecord: true)
                        ->maxLength(255),
                    Components\Toggle::make('is_published')->label('Publicada')->default(true),
                ]),
                Components\RichEditor::make('content')
                    ->label('Conteudo')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'attachFiles', 'blockquote', 'bold', 'bulletList',
                        'codeBlock', 'h2', 'h3', 'italic', 'link',
                        'orderedList', 'redo', 'strike', 'underline', 'undo',
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Titulo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Publicado')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->label('Atualizado')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                //
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
            ->defaultSort('title');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
