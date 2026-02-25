<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\AccountPayableResource\Pages;
use App\Models\AccountPayable;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AccountPayableResource extends Resource
{
    protected static ?string $model = AccountPayable::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?string $modelLabel = 'Conta a Pagar';

    protected static ?string $pluralModelLabel = 'Contas a Pagar';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detalhes da Despesa')->schema([
                Grid::make(2)->schema([
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\Select::make('supplier_id')->label('Fornecedor')->relationship('supplier', 'name')->searchable()->preload()->required(),
                    Components\TextInput::make('category')->label('Categoria (Plano de Contas)')->required()->maxLength(255),
                    Components\Select::make('vehicle_id')->label('Veiculo Vinculado (Opcional)')->relationship('vehicle', 'plate')->searchable()->preload(),
                ]),
                Components\TextInput::make('description')->label('Descricao / Resumo da Despesa')->required()->maxLength(255)->columnSpanFull(),
            ]),

            Section::make('Valores e Prazos')->schema([
                Grid::make(3)->schema([
                    Components\TextInput::make('amount')->label('Valor (R$)')->numeric()->prefix('R$')->required(),
                    Components\DatePicker::make('due_date')->label('Data de Vencimento')->required()->native(false),
                    Components\Select::make('status')->label('Status')->options([
                        'pendente' => 'Pendente',
                        'pago' => 'Pago',
                        'cancelado' => 'Cancelado',
                    ])->default('pendente')->required(),
                ]),
            ]),

            Section::make('Pagamento')->schema([
                Grid::make(3)->schema([
                    Components\DatePicker::make('paid_at')->label('Data do Pagamento')->native(false),
                    Components\TextInput::make('payment_method')->label('Meio de Pagamento')->maxLength(255),
                    Components\TextInput::make('recurrence')->label('Recorrencia (Ex: Mensal)')->maxLength(255),
                ]),
                Components\Textarea::make('notes')->label('Observacoes Internas')->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')->label('Descricao')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Fornecedor')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('due_date')->label('Vencimento')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Valor (R$)')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendente' => 'warning',
                        'pago' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pendente' => 'Pendente',
                    'pago' => 'Pago',
                    'cancelado' => 'Cancelado',
                ]),
                Tables\Filters\SelectFilter::make('supplier_id')->label('Fornecedor')->relationship('supplier', 'name'),
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
            'index' => Pages\ListAccountPayables::route('/'),
            'create' => Pages\CreateAccountPayable::route('/create'),
            'edit' => Pages\EditAccountPayable::route('/{record}/edit'),
        ];
    }
}
