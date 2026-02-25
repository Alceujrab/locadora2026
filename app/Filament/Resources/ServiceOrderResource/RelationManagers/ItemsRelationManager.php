<?php

namespace App\Filament\Resources\ServiceOrderResource\RelationManagers;

use Filament\Actions;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Itens e Servicos';

    protected static ?string $modelLabel = 'Item da OS';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('type')->label('Tipo')->options([
                    'peca' => 'Peca/Produto',
                    'mao_de_obra' => 'Mao de Obra (Servico)',
                ])->required()->default('peca'),
                Forms\Components\TextInput::make('description')->label('Descricao do Item/Servico')->required()->maxLength(255),
                Forms\Components\TextInput::make('quantity')->label('Quantidade')->numeric()->default(1)->required()->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $price = (float) $get('unit_price');
                        $qty = (float) $state;
                        $set('total', $price * $qty);
                    }),
                Forms\Components\TextInput::make('unit_price')->label('Preco Unitario (R$)')->numeric()->prefix('R$')->required()->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $qty = (float) $get('quantity');
                        $price = (float) $state;
                        $set('total', $price * $qty);
                    }),
                Forms\Components\TextInput::make('total')->label('Total (R$)')->numeric()->prefix('R$')->disabled()->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge()->color(fn (string $state): string => match ($state) {
                    'peca' => 'primary',
                    'mao_de_obra' => 'success',
                }),
                Tables\Columns\TextColumn::make('description')->label('Descricao'),
                Tables\Columns\TextColumn::make('quantity')->label('Qtd.'),
                Tables\Columns\TextColumn::make('unit_price')->label('Vlr. Unitario')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.')),
                Tables\Columns\TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make()->after(function (RelationManager $livewire) {
                    $livewire->getOwnerRecord()->recalculateTotal();
                }),
            ])
            ->actions([
                Actions\EditAction::make()->after(function (RelationManager $livewire) {
                    $livewire->getOwnerRecord()->recalculateTotal();
                }),
                Actions\DeleteAction::make()->after(function (RelationManager $livewire) {
                    $livewire->getOwnerRecord()->recalculateTotal();
                }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()->after(function (RelationManager $livewire) {
                        $livewire->getOwnerRecord()->recalculateTotal();
                    }),
                ]),
            ]);
    }
}
