<?php

namespace App\Filament\Resources\ServiceOrderResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'orderNotes';

    protected static ?string $title = 'Anotacoes';

    protected static ?string $modelLabel = 'Anotacao';

    protected static ?string $pluralModelLabel = 'Anotacoes';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Components\Hidden::make('user_id')->default(fn () => Auth::id()),
            Components\Textarea::make('content')
                ->label('Anotacao')
                ->required()
                ->rows(3)
                ->columnSpanFull()
                ->placeholder('Escreva sua anotacao aqui...'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Autor')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('content')
                    ->label('Anotacao')
                    ->limit(100)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                Actions\CreateAction::make()->label('Nova Anotacao'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
