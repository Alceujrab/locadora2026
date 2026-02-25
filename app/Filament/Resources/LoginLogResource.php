<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoginLogResource\Pages;
use App\Models\LoginLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LoginLogResource extends Resource
{
    protected static ?string $model = LoginLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuracao';

    protected static ?string $modelLabel = 'Log de Acesso';

    protected static ?string $pluralModelLabel = 'Logs de Acesso';

    protected static ?int $navigationSort = 11;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Usuario')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('user.email')->label('E-mail')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('action')->label('Acao')->badge()
                    ->colors(['success' => 'login', 'warning' => 'logout', 'danger' => 'failed'])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'login' => '✅ Login',
                        'logout' => '🚪 Logout',
                        'failed' => '❌ Falha',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('ip_address')->label('IP')->searchable()->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user_agent')->label('Dispositivo')->sortable()
                    ->limit(40)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->label('Data/Hora')->dateTime('d/m/Y H:i:s')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('action')->label('Acao')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'failed' => 'Falha',
                    ]),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoginLogs::route('/'),
        ];
    }
}
