<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Caution\Pages;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Caution\CautionResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Number;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use Throwable;
/**
 * @extends FormPage<CautionResource>
 */
class CautionFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                Grid::make([
                    Column::make([
                        ID::make(),
                        BelongsTo::make('Contrato Origem', 'contract', 'id', \App\MoonShine\Resources\ContractResource::class)
                            ->searchable()
                            ->required(),
                        BelongsTo::make('Cliente', 'customer', 'name', \App\MoonShine\Resources\CustomerResource::class)
                            ->searchable()
                            ->required(),
                    ])->columnSpan(6),
                    Column::make([
                        Select::make('Tipo', 'type')
                            ->options([
                                'cartao' => 'Carté£o de Cré©dito (Pré©-Autorização)',
                                'deposito' => 'Depé³sito Bancé¡rio/PIX',
                            ])
                            ->required(),
                        Number::make('Valor Retido', 'amount')
                            ->step(0.01)
                            ->required(),
                        Select::make('Status', 'status')
                            ->options([
                                'retida' => 'Retida',
                                'liberada' => 'Liberada',
                                'cobrada_parcial' => 'Cobrada Parcial',
                                'cobrada_total' => 'Cobrada Total',
                            ])
                            ->required(),
                    ])->columnSpan(6),
                    Column::make([
                        Text::make('ID Pré©-Auth Mercado Pago', 'mp_preauth_id')
                            ->hint('Cé³digo gerado pelo Mercado Pago'),
                        Number::make('Valor Cobrado (se houver)', 'charged_amount')
                            ->step(0.01),
                        Text::make('Motivo da Cobrané§a', 'charge_reason'),
                        Textarea::make('Observações', 'notes'),
                    ])->columnSpan(12)
                ])
            ]),
        ];
    }
    protected function buttons(): ListOf
    {
        return parent::buttons();
    }
    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }
    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }
    /**
     * @param  FormBuilder  $component
     *
     * @return FormBuilder
     */
    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
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
