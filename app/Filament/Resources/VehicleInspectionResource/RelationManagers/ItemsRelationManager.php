<?php

namespace App\Filament\Resources\VehicleInspectionResource\RelationManagers;

use Filament\Actions;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'item_name';

    protected static ?string $title = 'Itens da Vistoria';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('category')
                    ->label('Categoria (Ex: Exterior, Interior, Mecânica)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('item_name')
                    ->label('Item Inspecionado (Ex: Pneu Direito, Banco)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('condition')
                    ->label('Condição')
                    ->options([
                        'ok' => 'Ok',
                        'sujo' => 'Sujo',
                        'riscado' => 'Riscado',
                        'amassado' => 'Amassado',
                        'quebrado' => 'Quebrado',
                        'faltando' => 'Faltando',
                        'outro' => 'Outro',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Textarea::make('damage_description')
                    ->label('Descrição da Avaria')
                    ->visible(fn (Forms\Get $get) => in_array($get('condition'), ['riscado', 'amassado', 'quebrado', 'faltando', 'outro']))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('damage_value')
                    ->label('Custo do Dano (R$)')
                    ->numeric()
                    ->prefix('R$')
                    ->visible(fn (Forms\Get $get) => in_array($get('condition'), ['riscado', 'amassado', 'quebrado', 'faltando', 'outro'])),
                Forms\Components\FileUpload::make('photos')
                    ->label('Fotos')
                    ->image()
                    ->multiple()
                    ->directory('inspection-items')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')->label('Categoria')->sortable(),
                Tables\Columns\TextColumn::make('item_name')->label('Item')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Condição')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ok' => 'success',
                        'sujo' => 'warning',
                        'riscado', 'amassado', 'quebrado', 'faltando', 'outro' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('damage_value')
                    ->label('Custo do Dano')
                    ->formatStateUsing(fn ($state) => $state ? 'R$ '.number_format((float) $state, 2, ',', '.') : '-'),
            ])
            ->filters([
                //
            ])
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
