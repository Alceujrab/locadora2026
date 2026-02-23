<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\VehicleInspection;

use Illuminate\Database\Eloquent\Model;
use App\Models\VehicleInspection;
use App\MoonShine\Resources\VehicleInspection\Pages\VehicleInspectionIndexPage;
use App\MoonShine\Resources\VehicleInspection\Pages\VehicleInspectionFormPage;
use App\MoonShine\Resources\VehicleInspection\Pages\VehicleInspectionDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
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

    protected string $title = 'Vistorias de Veículos';
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            VehicleInspectionIndexPage::class,
            VehicleInspectionFormPage::class,
            VehicleInspectionDetailPage::class,
        ];
    }

    public function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Veículo', 'vehicle', resource: VehicleResource::class),
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
                BelongsTo::make('Veículo', 'vehicle', resource: VehicleResource::class)->required()->searchable(),
                BelongsTo::make('Contrato', 'contract', resource: ContractResource::class)->nullable()->searchable(),
                BelongsTo::make('Inspetor', 'inspector', resource: UserResource::class)->required()->searchable(),
                Enum::make('Tipo', 'type')->attach(InspectionType::class)->required(),
                Date::make('Data da Vistoria', 'inspection_date')->withTime()->required(),
            ]),
            Box::make('Estado do Veículo', [
                Number::make('Quilometragem', 'mileage')->min(0)->required(),
                Number::make('Nível de Combustível (%)', 'fuel_level')->min(0)->max(100)->required(),
                Text::make('Condição Geral', 'overall_condition')->required(),
                Textarea::make('Observações Gerais', 'notes'),
                Enum::make('Status', 'status')->attach(['rascunho' => 'Rascunho', 'concluida' => 'Concluída'])->required(),
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
            Number::make('Combustível %', 'fuel_level'),
            Text::make('Condição Geral', 'overall_condition'),
            Textarea::make('Observações', 'notes'),
            HasMany::make('Itens Avaliados', 'items', resource: InspectionItemResource::class)
        ];
    }

    public function filters(): array
    {
        return [
            BelongsTo::make('Veículo', 'vehicle', resource: VehicleResource::class)->nullable(),
            BelongsTo::make('Contrato', 'contract', resource: ContractResource::class)->nullable(),
            Enum::make('Tipo', 'type')->attach(InspectionType::class),
        ];
    }

    protected function afterSave(Model $item): Model
    {
        if ($item->type === InspectionType::RETURN && $item->status === 'concluida') {
            $contract = $item->contract;
            
            if ($contract) {
                // Atualizar KM do contrato
                $contract->return_mileage = $item->mileage;
                
                // Buscar vistoria de saída para comparar combustível
                $checkoutInspection = \App\Models\VehicleInspection::where('contract_id', $contract->id)
                                        ->where('type', InspectionType::CHECKOUT)
                                        ->first();
                
                $fuelDiff = 0;
                if ($checkoutInspection && $item->fuel_level < $checkoutInspection->fuel_level) {
                    $fuelDiff = $checkoutInspection->fuel_level - $item->fuel_level;
                }

                $extraCharges = 0;
                $description = [];

                // Exemplo simples de cobrança de Combustível (R$ 5,00 por %)
                if ($fuelDiff > 0) {
                    $fuelCharge = $fuelDiff * 5.00;
                    $extraCharges += $fuelCharge;
                    $description[] = "Reabastecimento ({$fuelDiff}%) - R$ " . number_format($fuelCharge, 2, ',', '.');
                }

                // Salvar as avarias
                $damageTotal = $item->total_damage_value;
                if ($damageTotal > 0) {
                    $extraCharges += $damageTotal;
                    $description[] = "Avarias (Vistoria) - R$ " . number_format($damageTotal, 2, ',', '.');
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
