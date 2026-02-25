<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\FineTrafficResource\Pages;
use App\Models\FineTraffic;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FineTrafficResource extends Resource
{
    protected static ?string $model = FineTraffic::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $modelLabel = 'Multa de Transito';

    protected static ?string $pluralModelLabel = 'Multas de Transito';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Identificacao')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('vehicle_id')
                        ->label('Veiculo')
                        ->relationship('vehicle', 'plate')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Components\Select::make('customer_id')
                        ->label('Locatario/Cliente')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->preload(),
                    Components\Select::make('contract_id')
                        ->label('Contrato Vinculado')
                        ->relationship('contract', 'contract_number')
                        ->searchable()
                        ->preload(),
                ]),
                Grid::make(2)->schema([
                    Components\TextInput::make('auto_infraction_number')
                        ->label('Auto de Infracao (AIT)')
                        ->maxLength(255),
                    Components\TextInput::make('fine_code')
                        ->label('Codigo da Infracao')
                        ->maxLength(255),
                ]),
            ]),

            Section::make('Detalhes da Multa')->schema([
                Grid::make(3)->schema([
                    Components\DatePicker::make('fine_date')->label('Data da Infracao')->required()->native(false),
                    Components\DatePicker::make('notification_date')->label('Data da Notificacao')->native(false),
                    Components\DatePicker::make('due_date')->label('Data de Vencimento')->native(false),
                ]),
                Grid::make(3)->schema([
                    Components\TextInput::make('amount')->label('Valor (R$)')->numeric()->prefix('R$')->required(),
                    Components\Select::make('responsibility')->label('Responsabilidade')->options([
                        'locadora' => 'Locadora',
                        'cliente' => 'Cliente',
                    ])->default('cliente')->required(),
                    Components\Select::make('status')->label('Status')->options([
                        'pendente' => 'Pendente',
                        'indicado' => 'Indicado',
                        'pago' => 'Pago',
                        'recorrido' => 'Recorrido',
                        'cancelado' => 'Cancelado',
                    ])->default('pendente')->required(),
                ]),
                Components\Textarea::make('description')->label('Descricao da Infracao')->columnSpanFull(),
                Components\Textarea::make('notes')->label('Observacoes Internas')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('auto_infraction_number')->label('AIT')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('vehicle.plate')->label('Veiculo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('fine_date')->label('Data Infracao')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('due_date')->label('Vencimento')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Valor')
                    ->formatStateUsing(fn ($state) => $state ? 'R$ '.number_format((float) $state, 2, ',', '.') : '-')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendente' => 'warning',
                        'pago' => 'success',
                        'indicado' => 'info',
                        'recorrido' => 'gray',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pendente' => 'Pendente',
                    'indicado' => 'Indicado',
                    'pago' => 'Pago',
                    'recorrido' => 'Recorrido',
                    'cancelado' => 'Cancelado',
                ]),
                Tables\Filters\SelectFilter::make('responsibility')->options([
                    'locadora' => 'Locadora',
                    'cliente' => 'Cliente',
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
            ->defaultSort('fine_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFineTraffics::route('/'),
            'create' => Pages\CreateFineTraffic::route('/create'),
            'edit' => Pages\EditFineTraffic::route('/{record}/edit'),
        ];
    }
}
