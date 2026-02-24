<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\Vehicle;
use App\Enums\VehicleStatus;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;

/**
 * @extends ModelResource<Vehicle>
 */
class VehicleResource extends ModelResource
{
    protected string $model = Vehicle::class;
    protected string $title = 'Veículos';
    protected string $column = 'plate';
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
        return ['plate', 'brand', 'model', 'chassis', 'renavam', 'color'];
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Placa', 'plate')->sortable(),
            Text::make('Marca', 'brand')->sortable(),
            Text::make('Modelo', 'model')->sortable(),
            Text::make('Ano', 'year_model'),
            Text::make('Cor', 'color'),
            Enum::make('Status', 'status')
                ->attach(VehicleStatus::class),
            Number::make('Km', 'mileage')
                ->sortable(),
            BelongsTo::make('Categoria', 'category', resource: VehicleCategoryResource::class)
                ->badge('purple'),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Box::make('Dados do Veículo', [
                ID::make(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                BelongsTo::make('Categoria', 'category', resource: VehicleCategoryResource::class)
                    ->required(),
                Text::make('Placa', 'plate')->required(),
                Text::make('Renavam', 'renavam'),
                Text::make('Chassi', 'chassis'),
            ]),
            Box::make('Identificação', [
                Text::make('Marca', 'brand')->required(),
                Text::make('Modelo', 'model')->required(),
                Number::make('Ano Fabricação', 'year_fab')
                    ->min(1900)->max(2030),
                Number::make('Ano Modelo', 'year_model')
                    ->min(1900)->max(2030),
                Text::make('Cor', 'color'),
            ]),
            Box::make('Especificações', [
                Select::make('Combustível', 'fuel')
                    ->options([
                        'flex' => 'Flex',
                        'gasolina' => 'Gasolina',
                        'etanol' => 'Etanol',
                        'diesel' => 'Diesel',
                        'eletrico' => 'Elétrico',
                        'hibrido' => 'Híbrido',
                    ]),
                Select::make('Câmbio', 'transmission')
                    ->options([
                        'manual' => 'Manual',
                        'automatico' => 'Automático',
                        'cvt' => 'CVT',
                        'automatizado' => 'Automatizado',
                    ]),
                Number::make('Portas', 'doors')->min(1)->max(6),
                Number::make('Lugares', 'seats')->min(1)->max(50),
                Number::make('Porta-Malas (L)', 'trunk_capacity'),
                Number::make('Quilometragem', 'mileage')->min(0),
            ]),
            Box::make('Status e Preços', [
                Enum::make('Status', 'status')
                    ->attach(VehicleStatus::class),
                Number::make('Diária Override (R$)', 'daily_rate_override')
                    ->step(0.01)->min(0),
                Number::make('Semanal Override (R$)', 'weekly_rate_override')
                    ->step(0.01)->min(0),
                Number::make('Mensal Override (R$)', 'monthly_rate_override')
                    ->step(0.01)->min(0),
            ]),
            Box::make('Valores', [
                Number::make('Valor Seguro (R$)', 'insurance_value')
                    ->step(0.01)->min(0),
                Number::make('Valor FIPE (R$)', 'fipe_value')
                    ->step(0.01)->min(0),
                Number::make('Valor Compra (R$)', 'purchase_value')
                    ->step(0.01)->min(0),
                Date::make('Data Compra', 'purchase_date'),
            ]),
            Box::make('Documentação', [
                Text::make('Nº CRLV', 'crlv_number'),
                Date::make('Validade CRLV', 'crlv_expiry'),
                Date::make('Validade IPVA', 'ipva_expiry'),
            ]),
            Box::make('Observações', [
                Textarea::make('Observações', 'notes'),
            ]),
            HasMany::make('Fotos', 'photos', resource: \App\MoonShine\Resources\VehiclePhoto\VehiclePhotoResource::class)
                ->creatable(),
        ];
    }

    protected function detailFields(): iterable
    {
        return [
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

            // Relacionamentos renderizados como Abas com links para os Resources completos
            HasMany::make('Galeria de Fotos', 'photos', resource: \App\MoonShine\Resources\VehiclePhoto\VehiclePhotoResource::class)
                ->creatable()
                ->tabMode(),

            HasMany::make('Locações e Faturamento', 'contracts', resource: ContractResource::class)
                ->relatedLink()
                ->tabMode(),

            HasMany::make('Histórico Mecânico', 'serviceOrders', resource: ServiceOrderResource::class)
                ->relatedLink()
                ->tabMode(),

            HasMany::make('Multas de Trânsito', 'fines', resource: \App\MoonShine\Resources\FineTraffic\FineTrafficResource::class)
                ->relatedLink()
                ->tabMode(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Enum::make('Status', 'status')
                ->attach(VehicleStatus::class),
            BelongsTo::make('Categoria', 'category', resource: VehicleCategoryResource::class)
                ->nullable(),
            Text::make('Marca', 'brand'),
            Text::make('Cor', 'color'),
        ];
    }

    protected function rules($item): array
    {
        return [
            'plate' => ['required', 'string', 'max:10'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'exists:vehicle_categories,id'],
        ];
    }
}
