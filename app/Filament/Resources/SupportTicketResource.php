<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static string|\UnitEnum|null $navigationGroup = 'Servicos';

    protected static ?string $modelLabel = 'Ticket de Suporte';

    protected static ?string $pluralModelLabel = 'Tickets de Suporte';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Identificacao e Cliente')->schema([
                Grid::make(2)->schema([
                    Components\Select::make('customer_id')->label('Cliente/Solicitante')->relationship('customer', 'name')->searchable()->preload()->required(),
                    Components\Select::make('assigned_to')->label('Atendente Responsavel')->relationship('assignedTo', 'name')->searchable()->preload(),
                ]),
                Components\TextInput::make('subject')->label('Assunto (Resumo)')->required()->maxLength(255),
                Components\Textarea::make('description')->label('Descricao do Problema/Solicitacao')->required()->columnSpanFull(),
            ]),

            Section::make('Classificacao')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('priority')->label('Prioridade')->options([
                        'baixa' => 'Baixa',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'critica' => 'Critica',
                    ])->default('media')->required(),
                    Components\Select::make('status')->label('Status')->options([
                        'aberto' => 'Aberto (Novo)',
                        'em_andamento' => 'Em Andamento',
                        'aguardando_cliente' => 'Aguardando Cliente',
                        'resolvido' => 'Resolvido/Fechado',
                    ])->default('aberto')->required(),
                    Components\TextInput::make('category')->label('Categoria (Duvida, Manutencao, Pagamento, etc)')->maxLength(255),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('subject')->label('Assunto')->searchable()->sortable()->limit(30),
                Tables\Columns\TextColumn::make('customer.name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('priority')->label('Prioridade')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baixa' => 'gray',
                        'media' => 'info',
                        'alta' => 'warning',
                        'critica' => 'danger',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aberto' => 'danger',
                        'em_andamento' => 'warning',
                        'aguardando_cliente' => 'info',
                        'resolvido' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignedTo.name')->label('Atendente')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Aberto em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'aberto' => 'Aberto',
                    'em_andamento' => 'Em Andamento',
                    'aguardando_cliente' => 'Aguardando Cliente',
                    'resolvido' => 'Resolvido',
                ]),
                Tables\Filters\SelectFilter::make('priority')->options([
                    'baixa' => 'Baixa',
                    'media' => 'Media',
                    'alta' => 'Alta',
                    'critica' => 'Critica',
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
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
