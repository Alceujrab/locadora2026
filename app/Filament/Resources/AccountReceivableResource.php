<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\AccountReceivableResource\Pages;
use App\Models\AccountReceivable;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AccountReceivableResource extends Resource
{
    protected static ?string $model = AccountReceivable::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?string $modelLabel = 'Conta a Receber';

    protected static ?string $pluralModelLabel = 'Contas a Receber';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Origem da Receita')->schema([
                Grid::make(2)->schema([
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\Select::make('customer_id')->label('Cliente/Sacado')->relationship('customer', 'name')->searchable()->preload()->required(),
                    Components\Select::make('contract_id')->label('Contrato Vinculado (Opcional)')->relationship('contract', 'contract_number')->searchable()->preload(),
                    Components\Select::make('invoice_id')->label('Fatura (Opcional)')->relationship('invoice', 'invoice_number')->searchable()->preload(),
                ]),
                Grid::make(2)->schema([
                    Components\TextInput::make('category')->label('Categoria (Plano de Contas)')->required()->maxLength(255),
                    Components\TextInput::make('description')->label('Resumo da Receita')->required()->maxLength(255),
                ]),
            ]),

            Section::make('Valores e Datas')->schema([
                Grid::make(3)->schema([
                    Components\TextInput::make('amount')->label('Valor Previsto (R$)')->numeric()->prefix('R$')->required(),
                    Components\DatePicker::make('due_date')->label('DataPrevista/Vencimento')->required()->native(false),
                    Components\Select::make('status')->label('Status')->options([
                        'pendente' => 'Pendente',
                        'recebido' => 'Recebido',
                        'inadimplente' => 'Inadimplente',
                        'cancelado' => 'Cancelado',
                    ])->default('pendente')->required(),
                ]),
            ]),

            Section::make('Recebimento')->schema([
                Grid::make(3)->schema([
                    Components\DatePicker::make('received_at')->label('Data do Recebimento Efetivo')->native(false),
                    Components\TextInput::make('payment_method')->label('Meio de Recebimento')->maxLength(255),
                    Components\TextInput::make('recurrence')->label('Recorrencia')->maxLength(255),
                ]),
                Components\Textarea::make('notes')->label('Observacoes da Receita')->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')->label('Descricao')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('due_date')->label('Vencimento')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Valor (R$)')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendente' => 'warning',
                        'recebido' => 'success',
                        'inadimplente' => 'danger',
                        'cancelado' => 'gray',
                        default => 'primary',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pendente' => 'Pendente',
                    'recebido' => 'Recebido',
                    'inadimplente' => 'Inadimplente',
                    'cancelado' => 'Cancelado',
                ]),
                Tables\Filters\SelectFilter::make('customer_id')->label('Cliente')->relationship('customer', 'name'),
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
            ->defaultSort('due_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountReceivables::route('/'),
            'create' => Pages\CreateAccountReceivable::route('/create'),
            'edit' => Pages\EditAccountReceivable::route('/{record}/edit'),
        ];
    }
}
