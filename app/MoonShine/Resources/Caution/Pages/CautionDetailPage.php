<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Caution\Pages;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\Caution\CautionResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\ActionButton;
use Illuminate\Http\Request;
use MoonShine\Laravel\MoonShineUI;
use MoonShine\UI\Components\FormBuilder;
use Throwable;
/**
 * @extends DetailPage<CautionResource>
 */
class CautionDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                Grid::make([
                    Column::make([
                        ID::make(),
                        BelongsTo::make('Contrato', 'contract', 'id', \App\MoonShine\Resources\ContractResource::class),
                        BelongsTo::make('Cliente', 'customer', 'name', \App\MoonShine\Resources\CustomerResource::class),
                        Text::make('Tipo', 'type')
                            ->badge(fn($type) => $type === 'cartao' ? 'info' : 'warning'),
                        Number::make('Valor Retido', 'amount'),
                        Text::make('Status', 'status')
                            ->badge(fn($status) => match($status) {
                                'retida' => 'warning',
                                'liberada' => 'success',
                                'cobrada_parcial' => 'info',
                                'cobrada_total' => 'error',
                                default => 'gray'
                            }),
                    ])->columnSpan(6),
                    Column::make([
                        Text::make('ID Pré©-Auth Mercado Pago', 'mp_preauth_id'),
                        Date::make('Data de Liberação', 'released_at')->format('d/m/Y H:i'),
                        Number::make('Valor Cobrado', 'charged_amount'),
                        Text::make('Motivo da Cobrané§a', 'charge_reason'),
                        Text::make('Observações', 'notes'),
                    ])->columnSpan(6),
                ])
            ])
        ];
    }
    protected function buttons(): ListOf
    {
        return parent::buttons()->add(
            ActionButton::make('Liberar Total', fn($item) => $this->getResource()->route('release', $item->getKey()))
                ->icon('check-circle')
                ->success()
                ->canSee(fn($item) => $item->status === 'retida'),
        )->add(
            ActionButton::make('Cobrar Valor', fn($item) => $this->getResource()->route('charge', $item->getKey()))
                ->icon('currency-dollar')
                ->error()
                ->inModal(
                    title: 'Cobrar Participação da Caução',
                    content: fn($item) => FormBuilder::make($this->getResource()->route('charge', $item->getKey()))
                        ->fields([
                            Number::make('Valor a Ser Cobrado', 'charged_amount')->required()->step(0.01),
                            Text::make('Motivo da Cobrané§a', 'charge_reason')->required(),
                        ])
                        ->submit('Cobrar')
                )
                ->canSee(fn($item) => $item->status === 'retida')
        );
    }
    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }
    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }
    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }
    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
