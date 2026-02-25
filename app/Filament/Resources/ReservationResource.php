<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Enums\ReservationStatus;
use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $modelLabel = 'Reserva';

    protected static ?string $pluralModelLabel = 'Reservas';

    protected static ?int $navigationSort = 2;

    public static function getGloballySearchableAttributes(): array
    {
        return ['customer.name', 'vehicle.plate', 'id'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Reserva')->tabs([
                Tabs\Tab::make('Dados Principais')->icon('heroicon-o-information-circle')->schema([
                    Components\Select::make('branch_id')->label('Filial Originaria')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\Select::make('customer_id')->label('Cliente')->relationship('customer', 'name')->searchable()->preload()->required(),
                    Components\Select::make('category_id')->label('Categoria Desejada')->relationship('category', 'name')->searchable()->preload(),
                    Components\Select::make('vehicle_id')->label('Veiculo Especifico')->relationship('vehicle', 'plate')->searchable()->preload(),
                    Components\Select::make('status')->label('Status')->options(ReservationStatus::class)->default(ReservationStatus::PENDING)->required(),
                ])->columns(2),

                Tabs\Tab::make('Datas e Locais')->icon('heroicon-o-clock')->schema([
                    Components\DateTimePicker::make('pickup_date')->label('Data/Hora de Retirada')->required(),
                    Components\DateTimePicker::make('return_date')->label('Data/Hora de Devolucao')->required(),
                    Components\Select::make('pickup_branch_id')->label('Local de Retirada')->relationship('pickupBranch', 'name')->searchable()->preload(),
                    Components\Select::make('return_branch_id')->label('Local de Devolucao')->relationship('returnBranch', 'name')->searchable()->preload(),
                ])->columns(2),

                Tabs\Tab::make('Valores')->icon('heroicon-o-currency-dollar')->schema([
                    Components\TextInput::make('daily_rate')->label('Diaria (R$)')->numeric()->prefix('R$')->required(),
                    Components\TextInput::make('total_days')->label('Total de Dias')->numeric()->required(),
                    Components\TextInput::make('subtotal')->label('Subtotal (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('extras_total')->label('Extras (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('discount')->label('Desconto (R$)')->numeric()->prefix('R$')->default(0),
                    Components\TextInput::make('total')->label('Total Geral (R$)')->numeric()->prefix('R$')->required(),
                ])->columns(3),

                Tabs\Tab::make('Cancelamento & Obs')->icon('heroicon-o-x-circle')->schema([
                    Components\DateTimePicker::make('canceled_at')->label('Data do Cancelamento')->disabled(),
                    Components\Textarea::make('cancel_reason')->label('Motivo do Cancelamento')->columnSpanFull(),
                    Components\Textarea::make('notes')->label('Observacoes Internas')->columnSpanFull(),
                ])->columns(2),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('vehicle.plate')->label('Veiculo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pickup_date')->label('Retirada')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('return_date')->label('Devolucao')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(ReservationStatus::class),
                Tables\Filters\SelectFilter::make('branch_id')->label('Filial Originaria')->relationship('branch', 'name'),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
