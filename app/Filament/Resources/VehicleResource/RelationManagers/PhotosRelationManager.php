<?php

namespace App\Filament\Resources\VehicleResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    protected static ?string $title = 'Fotos do Veiculo';

    protected static ?string $modelLabel = 'Foto';

    protected static ?string $pluralModelLabel = 'Fotos';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Components\FileUpload::make('path')
                ->label('Foto')
                ->image()
                ->directory('vehicles/photos')
                ->required()
                ->columnSpanFull(),
            Components\TextInput::make('filename')
                ->label('Nome do Arquivo'),
            Components\TextInput::make('position')
                ->label('Posicao')
                ->numeric()
                ->default(0),
            Components\Toggle::make('is_cover')
                ->label('Foto de Capa'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Foto')
                    ->square()
                    ->size(80),
                Tables\Columns\TextColumn::make('filename')
                    ->label('Arquivo'),
                Tables\Columns\TextColumn::make('position')
                    ->label('Posicao')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_cover')
                    ->label('Capa')
                    ->boolean(),
            ])
            ->reorderable('position')
            ->defaultSort('position')
            ->headerActions([
                Actions\CreateAction::make(),
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
}
