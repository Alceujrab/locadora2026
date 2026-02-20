<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\FineTraffic;

use App\Models\FineTraffic;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\SortDirection;

#[Icon('exclamation-triangle')]
class FineTrafficResource extends ModelResource
{
    protected string $model = FineTraffic::class;

    protected string $title = 'Multas de Trânsito';

    protected string $column = 'auto_infraction_number';

    protected SortDirection $sortDirection = SortDirection::DESC;

    public function getActiveActions(): array
    {
        return ['create', 'view', 'update', 'delete', 'massDelete', 'export'];
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Veículo', 'vehicle', fn($item) => $item->plate ?? $item->id)
                ->sortable(),

            BelongsTo::make('Cliente', 'customer', fn($item) => $item->name ?? $item->id)
                ->sortable(),

            Text::make('Nº Auto Infração', 'auto_infraction_number'),

            Text::make('Código', 'fine_code'),

            Number::make('Valor (R$)', 'amount')
                ->sortable(),

            Date::make('Data Infração', 'fine_date')
                ->format('d/m/Y')
                ->sortable(),

            Date::make('Vencimento', 'due_date')
                ->format('d/m/Y')
                ->sortable(),

            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'pago' => 'Pago',
                    'transferido' => 'Transferido',
                    'recurso' => 'Em Recurso',
                    'cancelado' => 'Cancelado',
                ])
                ->badge(fn(string $value) => match($value) {
                    'pendente' => 'warning',
                    'pago' => 'success',
                    'transferido' => 'info',
                    'recurso' => 'secondary',
                    'cancelado' => 'error',
                    default => 'secondary',
                }),

            Select::make('Responsabilidade', 'responsibility')
                ->options([
                    'empresa' => 'Empresa',
                    'cliente' => 'Cliente',
                ]),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            ID::make(),

            BelongsTo::make('Veículo', 'vehicle', fn($item) => $item->plate . ' - ' . ($item->brand ?? '') . ' ' . ($item->model ?? ''))
                ->required()
                ->searchable(),

            BelongsTo::make('Contrato', 'contract', fn($item) => $item->contract_number ?? 'Contrato #' . $item->id)
                ->nullable()
                ->searchable(),

            BelongsTo::make('Cliente', 'customer', fn($item) => $item->name ?? $item->id)
                ->nullable()
                ->searchable(),

            Text::make('Nº Auto Infração', 'auto_infraction_number')
                ->required(),

            Text::make('Código da Multa', 'fine_code'),

            Text::make('Descrição', 'description')
                ->required(),

            Number::make('Valor (R$)', 'amount')
                ->min(0)
                ->step(0.01)
                ->required(),

            Date::make('Data da Infração', 'fine_date')
                ->required(),

            Date::make('Vencimento', 'due_date')
                ->required(),

            Date::make('Data Notificação', 'notification_date'),

            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'pago' => 'Pago',
                    'transferido' => 'Transferido ao Cliente',
                    'recurso' => 'Em Recurso',
                    'cancelado' => 'Cancelado',
                ])
                ->default('pendente')
                ->required(),

            Select::make('Responsabilidade', 'responsibility')
                ->options([
                    'empresa' => 'Empresa',
                    'cliente' => 'Cliente',
                ])
                ->default('empresa')
                ->required(),

            Textarea::make('Observações', 'notes'),
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->formFields();
    }

    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Veículo', 'vehicle', fn($item) => $item->plate ?? $item->id)
                ->nullable(),

            Select::make('Status', 'status')
                ->options([
                    'pendente' => 'Pendente',
                    'pago' => 'Pago',
                    'transferido' => 'Transferido',
                    'recurso' => 'Em Recurso',
                    'cancelado' => 'Cancelado',
                ])
                ->nullable(),

            Select::make('Responsabilidade', 'responsibility')
                ->options([
                    'empresa' => 'Empresa',
                    'cliente' => 'Cliente',
                ])
                ->nullable(),

            Date::make('Vencimento De', 'due_date')
                ->nullable(),
        ];
    }
}
