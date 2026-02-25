<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\CautionResource\Pages;
use App\Models\Caution;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CautionResource extends Resource
{
    protected static ?string $model = Caution::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $modelLabel = 'Caucao';

    protected static ?string $pluralModelLabel = 'Caucoes';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Dados da Caucao')->schema([
                Grid::make(2)->schema([
                    Components\Select::make('contract_id')
                        ->label('Contrato Vinculado')
                        ->relationship('contract', 'contract_number')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Components\Select::make('customer_id')
                        ->label('Locatario')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),
                Grid::make(3)->schema([
                    Components\Select::make('type')->label('Tipo (Meio)')->options([
                        'cartao_credito' => 'Cartao de Credito',
                        'dinheiro' => 'Dinheiro',
                        'pix' => 'Pix',
                        'cheque' => 'Cheque',
                        'promissoria' => 'Nota Promissoria',
                    ])->required()->default('cartao_credito'),
                    Components\TextInput::make('amount')->label('Valor Retido (R$)')->numeric()->prefix('R$')->required(),
                    Components\Select::make('status')->label('Status')->options([
                        'retida' => 'Retido',
                        'liberada' => 'Liberado',
                        'executada' => 'Executado/Cobrado',
                    ])->default('retida')->required(),
                ]),
            ]),

            Section::make('Integracao & Controle')->schema([
                Grid::make(3)->schema([
                    Components\TextInput::make('mp_payment_id')->label('ID Pagamento (Gateway)'),
                    Components\TextInput::make('mp_preauth_id')->label('ID Pre-Auth (Gateway)'),
                    Components\DateTimePicker::make('released_at')->label('Data de Liberacao')->native(false),
                ]),
                Grid::make(2)->schema([
                    Components\TextInput::make('charged_amount')->label('Valor Executado (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('charge_reason')->label('Motivo da Execucao (Danos/Multas)'),
                ]),
                Components\Textarea::make('notes')->label('Observacoes da Caucao')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract.contract_number')->label('Contrato')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Modalidade')->badge(),
                Tables\Columns\TextColumn::make('amount')->label('Valor (R$)')
                    ->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'retida' => 'warning',
                        'liberada' => 'success',
                        'executada' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Data Registro')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'retida' => 'Retido',
                    'liberada' => 'Liberado',
                    'executada' => 'Executado/Cobrado',
                ]),
            ])
            ->actions([
                Actions\EditAction::make(),
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
            'index' => Pages\ListCautions::route('/'),
            'create' => Pages\CreateCaution::route('/create'),
            'edit' => Pages\EditCaution::route('/{record}/edit'),
        ];
    }
}
