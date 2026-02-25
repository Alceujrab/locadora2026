<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\LoginLog;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuracao';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static ?int $navigationSort = 10;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make('Usuario')->tabs([
                Tabs\Tab::make('Dados Pessoais')->icon('heroicon-o-user')->schema([
                    Grid::make(2)->schema([
                        Components\TextInput::make('name')->label('Nome Completo')->required()->maxLength(255),
                        Components\TextInput::make('email')->label('E-mail')->email()->required()->maxLength(255)
                            ->unique(table: 'users', column: 'email', ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->whereNull('deleted_at')),
                    ]),
                    Grid::make(3)->schema([
                        Components\TextInput::make('phone')->label('Telefone')->tel()->maxLength(20),
                        Components\TextInput::make('whatsapp')->label('WhatsApp')->tel()->maxLength(20),
                        Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload(),
                    ]),
                    Grid::make(2)->schema([
                        Components\FileUpload::make('avatar')->label('Avatar')->image()->directory('avatars')
                            ->imageResizeMode('cover')->imageCropAspectRatio('1:1')->maxSize(2048)->nullable(),
                        Components\Toggle::make('is_active')->label('Ativo')->default(true),
                    ]),
                ]),

                Tabs\Tab::make('Seguranca')->icon('heroicon-o-lock-closed')->schema([
                    Components\TextInput::make('password')
                        ->label('Senha')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context) => $context === 'create')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),

                Tabs\Tab::make('Permissoes')->icon('heroicon-o-shield-check')->schema([
                    Components\CheckboxList::make('roles')
                        ->label('Papeis (Roles)')
                        ->relationship('roles', 'name')
                        ->columns(3)
                        ->columnSpanFull()
                        ->descriptions([
                            'admin' => 'Acesso total ao sistema',
                            'gerente' => 'Gerencia operacoes e equipe',
                            'operador' => 'Operacoes do dia a dia',
                            'financeiro' => 'Acesso a modulos financeiros',
                            'cliente' => 'Area do cliente (publico)',
                        ]),
                    Components\CheckboxList::make('permissions')
                        ->label('Permissoes Especificas')
                        ->relationship('permissions', 'name')
                        ->columns(3)
                        ->columnSpanFull(),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')->label('')->circular()->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&background=1a1a2e&color=f7c948&size=40'),
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('email')->label('E-mail')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Papeis')->badge()
                    ->colors(['primary' => 'admin', 'success' => 'gerente', 'warning' => 'operador', 'info' => 'financeiro', 'gray' => 'cliente']),
                Tables\Columns\TextColumn::make('branch.name')->label('Filial')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
                Tables\Columns\TextColumn::make('last_login')
                    ->label('Ultimo Acesso')
                    ->getStateUsing(fn (User $record) => LoginLog::where('user_id', $record->id)->where('action', 'login')->latest()->value('created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(false),
                Tables\Columns\TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')->label('Status')
                    ->options([1 => 'Ativos', 0 => 'Inativos']),
                Tables\Filters\SelectFilter::make('roles')->label('Papel')
                    ->relationship('roles', 'name'),
                Tables\Filters\SelectFilter::make('branch_id')->label('Filial')
                    ->relationship('branch', 'name'),
                Tables\Filters\TrashedFilter::make()->label('Lixeira'),
            ])
            ->actions([
                Actions\EditAction::make(),

                Actions\Action::make('toggle_active')
                    ->label(fn (User $record) => $record->is_active ? 'Desativar' : 'Ativar')
                    ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Usuario ativado!' : 'Usuario desativado!')
                            ->success()->send();
                    }),

                Actions\Action::make('reset_password')
                    ->label('Resetar Senha')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->form([
                        Components\TextInput::make('new_password')
                            ->label('Nova Senha')
                            ->password()
                            ->required()
                            ->minLength(6),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update(['password' => bcrypt($data['new_password'])]);
                        Notification::make()->title('Senha resetada!')->success()->send();
                    }),

                Actions\Action::make('view_logs')
                    ->label('Logs')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->url(fn (User $record) => LoginLogResource::getUrl('index', ['tableFilters[user_id][value]' => $record->id])),

                Actions\DeleteAction::make()->label('Excluir'),
                Actions\ForceDeleteAction::make()->label('Excluir Permanentemente'),
                Actions\RestoreAction::make()->label('Restaurar'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
