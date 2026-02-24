<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\Vehicle;
use App\Enums\VehicleStatus;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
/**
 * @extends ModelResource<Vehicle>
 */
class VehicleResource extends ModelResource
{
    protected string $model = Vehicle::class;
    protected string $title = 'VeÃ­culos';
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
            Box::make('Dados do VeÃ­culo', [
                ID::make(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                BelongsTo::make('Categoria', 'category', resource: VehicleCategoryResource::class)
                    ->required(),
                Text::make('Placa', 'plate')->required(),
                Text::make('Renavam', 'renavam'),
                Text::make('Chassi', 'chassis'),
            ]),
            Box::make('IdentificaÃ§Ã£o', [
                Text::make('Marca', 'brand')->required(),
                Text::make('Modelo', 'model')->required(),
                Number::make('Ano FabricaÃ§Ã£o', 'year_fab')
                    ->min(1900)->max(2030),
                Number::make('Ano Modelo', 'year_model')
                    ->min(1900)->max(2030),
                Text::make('Cor', 'color'),
            ]),
            Box::make('EspecificaÃ§Ãµes', [
                Select::make('CombustÃ­vel', 'fuel')
                    ->options([
                        'flex' => 'Flex',
                        'gasolina' => 'Gasolina',
                        'etanol' => 'Etanol',
                        'diesel' => 'Diesel',
                        'eletrico' => 'ElÃ©trico',
                        'hibrido' => 'HÃ­brido',
                    ]),
                Select::make('CÃ¢mbio', 'transmission')
                    ->options([
                        'manual' => 'Manual',
                        'automatico' => 'AutomÃ¡tico',
                        'cvt' => 'CVT',
                        'automatizado' => 'Automatizado',
                    ]),
                Number::make('Portas', 'doors')->min(1)->max(6),
                Number::make('Lugares', 'seats')->min(1)->max(50),
                Number::make('Porta-Malas (L)', 'trunk_capacity'),
                Number::make('Quilometragem', 'mileage')->min(0),
            ]),
            Box::make('Status e PreÃ§os', [
                Enum::make('Status', 'status')
                    ->attach(VehicleStatus::class),
                Number::make('DiÃ¡ria Override (R$)', 'daily_rate_override')
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
            Box::make('DocumentaÃ§Ã£o', [
                Text::make('NÂº CRLV', 'crlv_number'),
                Date::make('Validade CRLV', 'crlv_expiry'),
                Date::make('Validade IPVA', 'ipva_expiry'),
            ]),
            Box::make('ObservaÃ§Ãµes', [
                Textarea::make('ObservaÃ§Ãµes', 'notes'),
            ]),
        ];
    }
    protected function detailFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Tabs::make([
                \MoonShine\UI\Components\Layout\Tab::make('Dados Principais', $this->formFields()),
                \MoonShine\UI\Components\Layout\Tab::make('HistÃ³rico MecÃ¢nico', [
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Custo Total de ManutenÃ§Ã£o')
                        ->value(fn($vehicle) => 'R$ ' . number_format($vehicle->serviceOrders()->where('status', \App\Enums\ServiceOrderStatus::COMPLETED)->sum('total'), 2, ',', '.'))
                        ->icon('currency-dollar'),
                    \MoonShine\Laravel\Fields\Relationships\HasMany::make('Ordens de ServiÃ§o', 'serviceOrders', resource: ServiceOrderResource::class)
                        ->hideOnForm()
                ]),
                \MoonShine\UI\Components\Layout\Tab::make('Galeria de Fotos', [
                    \MoonShine\Laravel\Fields\Relationships\HasMany::make('Fotos', 'photos', resource: \App\MoonShine\Resources\VehiclePhoto\VehiclePhotoResource::class)
                        ->creatable()
                        ->hideOnForm()
                ]),
                \MoonShine\UI\Components\Layout\Tab::make('HistÃ³rico (Auditoria)', [
                    \MoonShine\ChangeLog\Components\ChangeLog::make('HistÃ³rico de EdiÃ§Ãµes')
                ]),
            ])
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
