<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\VehicleInspection;
use Illuminate\Database\Eloquent\Model;
use App\Models\VehicleInspection;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use App\MoonShine\Resources\VehicleResource;
use App\MoonShine\Resources\ContractResource;
use App\MoonShine\Resources\UserResource;
use App\MoonShine\Resources\InspectionItem\InspectionItemResource;
use MoonShine\UI\Components\Layout\Box;
use App\Enums\InspectionType;
/**
 * @extends ModelResource<VehicleInspection, VehicleInspectionIndexPage, VehicleInspectionFormPage, VehicleInspectionDetailPage>
 */
class VehicleInspectionResource extends ModelResource
{
    protected string $model = VehicleInspection::class;
    protected string $title = 'Vistorias de Veé­culos';
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }
    public function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Veé­culo', 'vehicle', resource: VehicleResource::class),
            BelongsTo::make('Contrato', 'contract', resource: ContractResource::class),
            Enum::make('Tipo', 'type')->attach(InspectionType::class),
            Date::make('Data', 'inspection_date')->format('d/m/Y H:i'),
            Text::make('Status', 'status'),
        ];
    }
    public function formFields(): iterable
    {
        return [
            Box::make('Dados da Vistoria', [
                ID::make(),
                BelongsTo::make('Veé­culo', 'vehicle', resource: VehicleResource::class)->required()->searchable(),
                BelongsTo::make('Contrato', 'contract', resource: ContractResource::class)->nullable()->searchable(),
                BelongsTo::make('Inspetor', 'inspector', resource: UserResource::class)->required()->searchable(),
                Enum::make('Tipo', 'type')->attach(InspectionType::class)->required(),
                Date::make('Data da Vistoria', 'inspection_date')->withTime()->required(),
            ]),
            Box::make('Estado do Veé­culo', [
                Number::make('Quilometragem', 'mileage')->min(0)->required(),
                Number::make('Né­vel de Combusté­vel (%)', 'fuel_level')->min(0)->max(100)->required(),
                Text::make('Condição Geral', 'overall_condition')->required(),
                Textarea::make('Observações Gerais', 'notes'),
                Enum::make('Status', 'status')->attach(['rascunho' => 'Rascunho', 'concluida' => 'Conclué­da'])->required(),
            ]),
            HasMany::make('Itens Avaliados', 'items', resource: InspectionItemResource::class)
                ->creatable()
        ];
    }
    public function detailFields(): iterable
    {
        return [
            ...$this->indexFields(),
            Number::make('Quilometragem', 'mileage'),
            Number::make('Combusté­vel %', 'fuel_level'),
            Text::make('Condição Geral', 'overall_condition'),
            Textarea::make('Observações', 'notes'),
            HasMany::make('Itens Avaliados', 'items', resource: InspectionItemResource::class)
        ];
    }
    public function filters(): array
    {
        return [
            BelongsTo::make('Veé­culo', 'vehicle', resource: VehicleResource::class)->nullable(),
            BelongsTo::make('Contrato', 'contract', resource: ContractResource::class)->nullable(),
            Enum::make('Tipo', 'type')->attach(InspectionType::class),
        ];
    }
    protected function afterSave(DataWrapperContract $item, FieldsContract $fields): DataWrapperContract
    {
        /** @var \App\Models\VehicleInspection $model */
        $model = $item->toModel();
        if ($model->type === InspectionType::RETURN && $model->status === 'concluida') {
            $contract = $model->contract;
            if ($contract) {
                // Atualizar KM do contrato
                $contract->return_mileage = $model->mileage;
                // Buscar vistoria de saé­da para comparar combusté­vel
                $checkoutInspection = \App\Models\VehicleInspection::where('contract_id', $contract->id)
                                        ->where('type', InspectionType::CHECKOUT)
                                        ->first();
                $fuelDiff = 0;
                if ($checkoutInspection && $model->fuel_level < $checkoutInspection->fuel_level) {
                    $fuelDiff = $checkoutInspection->fuel_level - $model->fuel_level;
                }
                $extraCharges = 0;
                $description = [];
                // Exemplo simples de cobrané§a de Combusté­vel (R$ 5,00 por %)
                if ($fuelDiff > 0) {
                    $fuelCharge = $fuelDiff * 5.00;
                    $extraCharges += $fuelCharge;
                    $description[] = "Reabastecimento ({$fuelDiff}%) - R$ " . number_format($fuelCharge, 2, ',', '.');
                }
                // Salvar as avarias
                // Note: assuming 'total_damage_value' is a method/attribute in model. 
                // Using null coalesce in case it does not exist directly.
                $damageTotal = $model->total_damage_value ?? 0;
                if ($damageTotal > 0) {
                    $extraCharges += $damageTotal;
                    $description[] = "Avarias (Vistoria) - R$ " . number_format((float)$damageTotal, 2, ',', '.');
                }
                if ($extraCharges > 0) {
                    $contract->additional_charges = ($contract->additional_charges ?? 0) + $extraCharges;
                    $oldDesc = $contract->additional_charges_description ? $contract->additional_charges_description . ' | ' : '';
                    $contract->additional_charges_description = $oldDesc . implode(' | ', $description);
                    $contract->total += $extraCharges;
                }
                $contract->save();
            }
        }
        return $item;
    }
}
