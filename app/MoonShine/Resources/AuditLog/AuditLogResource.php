<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AuditLog;
use App\Models\AuditLog;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\SortDirection;
#[Icon('shield-check')]
class AuditLogResource extends ModelResource
{
    protected string $model = AuditLog::class;
    protected string $title = 'Logs de Auditoria';
    protected string $column = 'id';
    protected SortDirection $sortDirection = SortDirection::DESC;
    public function getActiveActions(): array
    {
        return ['view', 'export'];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Modelo', 'model_type', fn($item) => class_basename($item->model_type))
                ->sortable(),
            Text::make('ID Registro', 'model_id'),
            Select::make('AÃ§Ã£o', 'action')
                ->options([
                    'created' => 'Criado',
                    'updated' => 'Alterado',
                    'deleted' => 'ExcluÃ­do',
                ])
                ->badge(fn(string $value) => match($value) {
                    'created' => 'success',
                    'updated' => 'warning',
                    'deleted' => 'error',
                    default => 'secondary',
                })
                ->sortable(),
            Text::make('UsuÃ¡rio', 'user_id', fn($item) => $item->user?->name ?? 'Sistema')
                ->sortable(),
            Text::make('IP', 'ip'),
            Date::make('Data', 'created_at')
                ->format('d/m/Y H:i')
                ->sortable(),
        ];
    }
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Modelo', 'model_type'),
            Text::make('ID Registro', 'model_id'),
            Select::make('AÃ§Ã£o', 'action')
                ->options([
                    'created' => 'Criado',
                    'updated' => 'Alterado',
                    'deleted' => 'ExcluÃ­do',
                ]),
            Text::make('UsuÃ¡rio', 'user_id', fn($item) => $item->user?->name ?? 'Sistema'),
            Text::make('IP', 'ip'),
            Text::make('User Agent', 'user_agent'),
            Textarea::make('Valores Anteriores', 'old_values', fn($item) => json_encode($item->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
            Textarea::make('Novos Valores', 'new_values', fn($item) => json_encode($item->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
            Date::make('Data', 'created_at')
                ->format('d/m/Y H:i:s'),
        ];
    }
    protected function filters(): iterable
    {
        return [
            Select::make('AÃ§Ã£o', 'action')
                ->options([
                    'created' => 'Criado',
                    'updated' => 'Alterado',
                    'deleted' => 'ExcluÃ­do',
                ])
                ->nullable(),
            Text::make('Modelo', 'model_type')
                ->nullable(),
        ];
    }
}
