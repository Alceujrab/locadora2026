<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS / Site';

    protected static ?string $modelLabel = 'Postagem de Blog';

    protected static ?string $pluralModelLabel = 'Blog / Noticias';

    protected static ?int $navigationSort = 3;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'postCategory.name'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Conteudo e Estrutura')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('post_category_id')->label('Categoria')->relationship('postCategory', 'name')->searchable()->preload()->required(),
                    Components\TextInput::make('title')
                        ->label('Titulo')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                        ->maxLength(255),
                    Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(Post::class, 'slug', ignoreRecord: true)
                        ->maxLength(255),
                ]),
                Components\RichEditor::make('content')
                    ->label('Conteudo do Post')
                    ->required()
                    ->columnSpanFull(),
            ]),

            Section::make('Midia e Publicacao')->schema([
                Grid::make(2)->schema([
                    Components\FileUpload::make('image')->label('Imagem Principal (Capa)')->directory('blog-images')->image(),
                    Grid::make(1)->schema([
                        Components\Toggle::make('is_published')->label('Publicado')->default(true),
                        Components\TextInput::make('views')->label('Visualizacoes')->numeric()->default(0)->disabled(),
                    ]),
                ]),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Capa')->square(),
                Tables\Columns\TextColumn::make('title')->label('Titulo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('postCategory.name')->label('Categoria')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Publicado')->boolean(),
                Tables\Columns\TextColumn::make('views')->label('Qtd Views')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Data Publicacao')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('post_category_id')->label('Categoria')->relationship('postCategory', 'name'),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
