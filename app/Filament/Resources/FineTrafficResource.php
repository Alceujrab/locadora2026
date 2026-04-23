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

    protected static ?string $modelLabel = 'Multa de Trânsito';

    protected static ?string $pluralModelLabel = 'Multas de Trânsito';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Identificacao')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('vehicle_id')
                        ->label('Veículo')
                        ->relationship('vehicle', 'plate')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Components\Select::make('customer_id')
                        ->label('Locatário/Cliente')
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
                        ->label('Auto de Infração (AIT)')
                        ->maxLength(255),
                    Components\TextInput::make('fine_code')
                        ->label('Código da Infração')
                        ->maxLength(255),
                ]),
            ]),

            Section::make('Detalhes da Multa')->schema([
                Grid::make(3)->schema([
                    Components\DatePicker::make('fine_date')->label('Data da Infração')->required()->native(false),
                    Components\DatePicker::make('notification_date')->label('Data da Notificação')->native(false),
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
                Components\Textarea::make('description')->label('Descrição da Infração')->columnSpanFull(),
                Components\Textarea::make('notes')->label('Observações Internas')->columnSpanFull(),
            ]),

            Section::make('Condutor Informado')
                ->description('Dados do condutor indicado para transferência de pontos/responsabilidade da multa.')
                ->collapsible()
                ->schema([
                    Grid::make(3)->schema([
                        Components\TextInput::make('driver_name')
                            ->label('Nome Completo')
                            ->maxLength(150),
                        Components\TextInput::make('driver_cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            ->maxLength(20),
                        Components\TextInput::make('driver_rg')
                            ->label('RG')
                            ->maxLength(30),
                    ]),
                    Grid::make(3)->schema([
                        Components\TextInput::make('driver_phone')
                            ->label('Telefone')
                            ->tel()
                            ->mask('(99) 99999-9999')
                            ->maxLength(30),
                        Components\TextInput::make('driver_email')
                            ->label('E-mail')
                            ->email()
                            ->maxLength(150),
                        Components\TextInput::make('driver_cnh_number')
                            ->label('Nº da CNH')
                            ->maxLength(30),
                    ]),
                    Grid::make(3)->schema([
                        Components\DatePicker::make('driver_cnh_expires_at')
                            ->label('Validade da CNH')
                            ->native(false),
                        Components\TextInput::make('driver_zipcode')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->maxLength(15),
                        Components\TextInput::make('driver_address')
                            ->label('Logradouro')
                            ->maxLength(255),
                    ]),
                    Grid::make(4)->schema([
                        Components\TextInput::make('driver_address_number')
                            ->label('Número')
                            ->maxLength(20),
                        Components\TextInput::make('driver_address_complement')
                            ->label('Complemento')
                            ->maxLength(100),
                        Components\TextInput::make('driver_neighborhood')
                            ->label('Bairro')
                            ->maxLength(120),
                        Components\TextInput::make('driver_city')
                            ->label('Cidade')
                            ->maxLength(120),
                    ]),
                    Grid::make(4)->schema([
                        Components\TextInput::make('driver_state')
                            ->label('UF')
                            ->maxLength(2),
                    ]),
                    Grid::make(2)->schema([
                        Components\FileUpload::make('driver_cnh_path')
                            ->label('Cópia da CNH')
                            ->disk('public')
                            ->directory('fines/driver-cnh')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(8192)
                            ->downloadable()
                            ->openable()
                            ->previewable(true),
                        Components\FileUpload::make('driver_address_proof_path')
                            ->label('Comprovante de Endereço')
                            ->disk('public')
                            ->directory('fines/driver-address-proof')
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(8192)
                            ->downloadable()
                            ->openable()
                            ->previewable(true),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('auto_infraction_number')->label('AIT')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('vehicle.plate')->label('Veículo')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('driver_name')->label('Condutor Informado')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),
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
