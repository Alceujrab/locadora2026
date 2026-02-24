<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use App\MoonShine\Resources\BranchResource;
use App\MoonShine\Resources\VehicleCategoryResource;
use App\MoonShine\Resources\ContractResource;
use App\MoonShine\Resources\ServiceOrderResource;
use App\MoonShine\Resources\VehiclePhoto\VehiclePhotoResource;
use App\MoonShine\Resources\FineTraffic\FineTrafficResource;
use App\Enums\VehicleStatus;
use App\Enums\ContractStatus;
use App\Enums\ServiceOrderStatus;

class VehicleDetailPage extends DetailPage
{
    protected function components(): iterable
    {
        $vehicle = $this->getResource()->getItem();

        $faturamento = 'R$ 0,00';
        $totalLocacoes = '0';
        $custoManutencao = 'R$ 0,00';
        $totalMultas = '0';

        if ($vehicle) {
            $faturamento = 'R$ ' . number_format(
                (float) $vehicle->contracts()
                    ->whereIn('status', [ContractStatus::ACTIVE, ContractStatus::FINISHED])
                    ->sum('total'),
                2, ',', '.'
            );
            $totalLocacoes = (string) $vehicle->contracts()->count();
            $custoManutencao = 'R$ ' . number_format(
                (float) $vehicle->serviceOrders()
                    ->where('status', ServiceOrderStatus::COMPLETED)
                    ->sum('total'),
                2, ',', '.'
            );
            $totalMultas = (string) $vehicle->fines()->count();
        }

        return [
            Tabs::make([
                Tab::make('Visão Geral', [
                    Box::make('Dados do Veículo', [
                        ID::make(),
                        Text::make('Placa', 'plate'),
                        BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                        BelongsTo::make('Categoria', 'category', resource: VehicleCategoryResource::class),
                        Text::make('Marca', 'brand'),
                        Text::make('Modelo', 'model'),
                        Text::make('Ano Modelo', 'year_model'),
                        Text::make('Cor', 'color'),
                        Enum::make('Status', 'status')->attach(VehicleStatus::class),
                        Number::make('Quilometragem (Km)', 'mileage'),
                    ]),
                    Box::make('Valores e Documentação', [
                        Number::make('Diária Override (R$)', 'daily_rate_override'),
                        Number::make('Semanal Override (R$)', 'weekly_rate_override'),
                        Number::make('Mensal Override (R$)', 'monthly_rate_override'),
                        Number::make('Valor Seguro (R$)', 'insurance_value'),
                        Number::make('Valor FIPE (R$)', 'fipe_value'),
                        Number::make('Valor Compra (R$)', 'purchase_value'),
                        Date::make('Data Compra', 'purchase_date'),
                        Text::make('Nº CRLV', 'crlv_number'),
                        Date::make('Validade CRLV', 'crlv_expiry'),
                        Date::make('Validade IPVA', 'ipva_expiry'),
                        Textarea::make('Observações', 'notes'),
                    ]),
                ]),
                Tab::make('Galeria de Fotos', [
                    HasMany::make('Fotos', 'photos', resource: VehiclePhotoResource::class)
                        ->creatable(),
                ]),
                Tab::make('Locações e Faturamento', [
                    Grid::make([
                        Column::make([
                            ValueMetric::make('Faturamento Total')
                                ->value($faturamento)
                                ->icon('banknotes'),
                        ])->columnSpan(6),
                        Column::make([
                            ValueMetric::make('Total de Locações')
                                ->value($totalLocacoes)
                                ->icon('document-duplicate'),
                        ])->columnSpan(6),
                    ]),
                    HasMany::make('Contratos', 'contracts', resource: ContractResource::class),
                ]),
                Tab::make('Histórico Mecânico', [
                    ValueMetric::make('Custo Total de Manutenção')
                        ->value($custoManutencao)
                        ->icon('wrench-screwdriver'),
                    HasMany::make('Ordens de Serviço', 'serviceOrders', resource: ServiceOrderResource::class),
                ]),
                Tab::make('Multas de Trânsito', [
                    ValueMetric::make('Total de Multas')
                        ->value($totalMultas)
                        ->icon('exclamation-triangle'),
                    HasMany::make('Multas', 'fines', resource: FineTrafficResource::class),
                ]),
            ]),
        ];
    }
}
