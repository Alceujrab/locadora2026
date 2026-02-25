<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS / Site';

    protected static ?string $modelLabel = 'Depoimento';

    protected static ?string $pluralModelLabel = 'Depoimentos';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Autor e Depoimento')->schema([
                Grid::make(2)->schema([
                    Components\TextInput::make('name')->label('Nome do Autor')->required()->maxLength(255),
                    Components\TextInput::make('company')->label('Empresa / Cargo')->maxLength(255),
                ]),
                Components\Textarea::make('content')->label('Texto do Depoimento')->required()->columnSpanFull(),
                Grid::make(3)->schema([
                    Components\Select::make('rating')->label('Avaliacao (Estrelas)')->options([
                        1 => '1 Estrela ',
                        2 => '2 Estrelas ',
                        3 => '3 Estrelas ',
                        4 => '4 Estrelas ',
                        5 => '5 Estrelas ',
                    ])->default(5)->required(),
                    Components\FileUpload::make('avatar')->label('Foto (Avatar)')->directory('testimonials')->image()->avatar(),
                    Components\Toggle::make('is_active')->label('Ativo/Visivel')->default(true),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')->label('Foto')->circular(),
                Tables\Columns\TextColumn::make('name')->label('Autor')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('company')->label('Empresa/Cargo')->searchable(),
                Tables\Columns\TextColumn::make('rating')->label('Avaliacao')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state)),
                Tables\Columns\IconColumn::make('is_active')->label('Visivel')->boolean(),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
