<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\ServiceOrder;
use App\Enums\ServiceOrderStatus;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
/**
 * @extends ModelResource<ServiceOrder>
 */
class ServiceOrderResource extends ModelResource
{
    protected string $model = ServiceOrder::class;
    protected string $title = 'Ordens de ServiÃ§o';
    protected string $column = 'id';
    protected bool $columnSelection = true;
    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }
    public function search(): array
    {
        return ['id', 'description', 'nf_number'];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('VeÃ­culo', 'vehicle', resource: VehicleResource::class),
            BelongsTo::make('Fornecedor', 'supplier', resource: SupplierResource::class),
            Select::make('Tipo', 'type')
                ->options([
                    'preventiva' => 'Preventiva',
                    'corretiva' => 'Corretiva',
                ]),
            Enum::make('Status', 'status')
                ->attach(ServiceOrderStatus::class),
            Number::make('Total (R$)', 'total')
                ->sortable(),
            Date::make('Abertura', 'opened_at')->sortable(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            Box::make('Ordem de ServiÃ§o', [
                ID::make(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                BelongsTo::make('VeÃ­culo', 'vehicle', resource: VehicleResource::class)
                    ->required()
                    ->searchable(),
                BelongsTo::make('Fornecedor', 'supplier', resource: SupplierResource::class)
                    ->searchable(),
                Select::make('Tipo', 'type')
                    ->options([
                        'preventiva' => 'Preventiva',
                        'corretiva' => 'Corretiva',
                    ])
                    ->required(),
                Textarea::make('DescriÃ§Ã£o', 'description'),
            ]),
            Box::make('Valores', [
                Number::make('PeÃ§as (R$)', 'items_total')
                    ->step(0.01)->min(0)->readonly(),
                Number::make('MÃ£o de Obra (R$)', 'labor_total')
                    ->step(0.01)->min(0)->readonly(),
                Number::make('Total (R$)', 'total')
                    ->step(0.01)->min(0)->readonly(),
            ]),
            Box::make('Status e Datas', [
                Enum::make('Status', 'status')
                    ->attach(ServiceOrderStatus::class),
                Date::make('Abertura', 'opened_at'),
                Date::make('ConclusÃ£o', 'completed_at'),
            ]),
            Box::make('Nota Fiscal', [
                Text::make('NÂº NF', 'nf_number'),
                Text::make('Arquivo NF', 'nf_path'),
                Textarea::make('ObservaÃ§Ãµes', 'notes'),
            ]),
            \MoonShine\Laravel\Fields\Relationships\HasMany::make('Itens e MÃ£o de Obra', 'items', resource: \App\MoonShine\Resources\ServiceOrderItem\ServiceOrderItemResource::class)
                ->creatable()
                ->modifyTable(fn($table) => $table->cast(new \App\Models\ServiceOrderItem())) // Cast para as tabelas renderizarem melhor sem bugs no admin panel
        ];
    }
    protected function detailFields(): iterable
    {
        return $this->formFields();
    }
    protected function filters(): iterable
    {
        return [
            Enum::make('Status', 'status')
                ->attach(ServiceOrderStatus::class),
            Select::make('Tipo', 'type')
                ->options([
                    'preventiva' => 'Preventiva',
                    'corretiva' => 'Corretiva',
                ])
                ->nullable(),
        ];
    }
    protected function rules($item): array
    {
        return [
            'vehicle_id' => ['required'],
            'type' => ['required', 'string'],
        ];
    }
    public function getActiveActions(): array
    {
        return ['create', 'view', 'update', 'delete', 'massDelete', 'export'];
    }
}
