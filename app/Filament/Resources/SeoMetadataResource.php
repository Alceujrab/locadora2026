<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\SeoMetadataResource\Pages;
use App\Models\SeoMetadata;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SeoMetadataResource extends Resource
{
    protected static ?string $model = SeoMetadata::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS / Site';

    protected static ?string $modelLabel = 'Metadado SEO';

    protected static ?string $pluralModelLabel = 'Metadados SEO';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Configuracao de Rota')->schema([
                Components\TextInput::make('url')
                    ->label('URL Relativa (ex: /sobre-nos)')
                    ->required()
                    ->unique(SeoMetadata::class, 'url', ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
                Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ]),

            Section::make('Tags SEO')->schema([
                Components\TextInput::make('title')->label('Tag <title> (SEO Title)')->maxLength(255)->required(),
                Components\Textarea::make('description')->label('Meta Description')->maxLength(500),
                Components\TextInput::make('keywords')->label('Keywords (separadas por virgula)')->maxLength(255),
            ]),

            Section::make('Open Graph (Redes Sociais)')->schema([
                Components\FileUpload::make('og_image')->label('Imagem Customizada de Compartilhamento (og:image)')->directory('seo-images')->image(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url')->label('URL / Rota')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->label('SEO Title')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
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
            ->defaultSort('url', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeoMetadata::route('/'),
            'create' => Pages\CreateSeoMetadata::route('/create'),
            'edit' => Pages\EditSeoMetadata::route('/{record}/edit'),
        ];
    }
}
