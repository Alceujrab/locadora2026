<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Laravel\MoonShineUI;
use MoonShine\Laravel\Http\Requests\MoonShineRequest;
use MoonShine\Contracts\UI\ActionButtonContract;
/**
 * @extends ModelResource<Reservation>
 */
class ReservationResource extends ModelResource
{
    protected string $model = Reservation::class;
    protected ?PageType $redirectAfterSave = PageType::INDEX;
    protected string $title = 'Reservas';
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
        return ['id'];
    }
    public function query(): \Illuminate\Contracts\Database\Eloquent\Builder
    {
        $query = parent::query();
        return $query;
    }
    public function getActiveActions(): array
    {
        return parent::getActiveActions();
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class),
            BelongsTo::make('Veé­culo', 'vehicle', resource: VehicleResource::class),
            Date::make('Retirada', 'pickup_date')->sortable(),
            Date::make('Devolução', 'return_date')->sortable(),
            Number::make('Total (R$)', 'total')
                ->sortable(),
            Enum::make('Status', 'status')
                ->attach(ReservationStatus::class),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            Box::make('Reserva', [
                ID::make(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class)
                    ->required()
                    ->searchable(),
                BelongsTo::make('Veé­culo', 'vehicle', resource: VehicleResource::class)
                    ->required()
                    ->searchable(),
                BelongsTo::make('Categoria', 'category', resource: VehicleCategoryResource::class),
            ]),
            Box::make('Datas e Local', [
                Date::make('Data Retirada', 'pickup_date')->required(),
                Date::make('Data Devolução', 'return_date')->required(),
                BelongsTo::make('Filial Retirada', 'pickupBranch', resource: BranchResource::class),
                BelongsTo::make('Filial Devolução', 'returnBranch', resource: BranchResource::class),
            ]),
            Box::make('Valores', [
                Number::make('Dié¡ria (R$)', 'daily_rate')
                    ->step(0.01)->min(0),
                Number::make('Total Dias', 'total_days')
                    ->min(1),
                Number::make('Subtotal (R$)', 'subtotal')
                    ->step(0.01)->min(0),
                Number::make('Extras (R$)', 'extras_total')
                    ->step(0.01)->min(0),
                Number::make('Desconto (R$)', 'discount')
                    ->step(0.01)->min(0),
                Number::make('Total (R$)', 'total')
                    ->step(0.01)->min(0),
            ]),
            Box::make('Status', [
                Enum::make('Status', 'status')
                    ->attach(ReservationStatus::class),
                Textarea::make('Observações', 'notes'),
            ]),
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
                ->attach(ReservationStatus::class),
            Date::make('Retirada a partir', 'pickup_date'),
            BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class)
                ->nullable(),
        ];
    }
    protected function rules(mixed $item): array
    {
        return [
            'customer_id' => ['required'],
            'vehicle_id' => [
                'required',
                function ($attribute, $value, $fail) use ($item) {
                    $pickupDate = request()->input('pickup_date');
                    $returnDate = request()->input('return_date');
                    if ($pickupDate && $returnDate && $value) {
                        $vehicle = \App\Models\Vehicle::find($value);
                        if ($vehicle) {
                            try {
                                $start = new \DateTime($pickupDate);
                                $end = new \DateTime($returnDate);
                                if (!$vehicle->isAvailableForPeriod($start, $end, $item?->id)) {
                                    $fail('O veé­culo selecionado né£o esté¡ disponé­vel neste peré­odo.');
                                }
                            } catch (\Exception $e) {
                                $fail('Datas de reserva invé¡lidas.');
                            }
                        }
                    }
                },
            ],
            'pickup_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after:pickup_date'],
        ];
    }
    protected function beforeSave(mixed $item): mixed
    {
        $service = new \App\Services\ReservationService();
        $pricing = $service->calculatePricing(
            request()->input('pickup_date'),
            request()->input('return_date'),
            (int)request()->input('vehicle_id'),
            request()->filled('category_id') ? (int)request()->input('category_id') : null,
            [], // Extras seré£o integrados posteriormente
            (float)request()->input('discount', 0)
        );
        $item->total_days = $pricing['total_days'];
        $item->daily_rate = $pricing['daily_rate'];
        $item->subtotal = $pricing['subtotal'];
        $item->extras_total = $pricing['extras_total'];
        $item->discount = $pricing['discount'];
        $item->total = $pricing['total'];
        // Associar filial automaticamente se né£o informada
        if (!$item->branch_id && $item->vehicle_id) {
            $vehicle = \App\Models\Vehicle::find($item->vehicle_id);
            $item->branch_id = $vehicle?->branch_id;
        }
        return $item;
    }
    /**
     * @return list<ActionButtonContract>
     */
    public function indexButtons(): iterable
    {
        return [
            ActionButton::make('Aprovar')
                ->icon('check')
                ->success()
                ->method('approve')
                ->canSee(fn($item) => $item->status === ReservationStatus::PENDING),
            ActionButton::make('Cancelar')
                ->icon('x-mark')
                ->danger()
                ->method('cancel')
                ->canSee(fn($item) => in_array($item->status, [ReservationStatus::PENDING, ReservationStatus::CONFIRMED])),
            ActionButton::make('Gerar Contrato')
                ->icon('document-text')
                ->primary()
                ->method('convertToContract')
                ->canSee(fn($item) => in_array($item->status, [ReservationStatus::PENDING, ReservationStatus::CONFIRMED])),
        ];
    }
    /**
     * @return list<ActionButtonContract>
     */
    public function detailButtons(): iterable
    {
        return $this->indexButtons();
    }
    /**
     * @return list<ActionButtonContract>
     */
    public function formButtons(): iterable
    {
        return [
            // Extra buttons na edição se necessé¡rio
        ];
    }
    public function approve(MoonShineRequest $request): mixed
    {
        $item = $request->getResource()->getItem();
        $item->update(['status' => ReservationStatus::CONFIRMED]);
        MoonShineUI::toast('Reserva aprovada com sucesso!', 'success');
        return back();
    }
    public function cancel(MoonShineRequest $request): mixed
    {
        $item = $request->getResource()->getItem();
        $item->update(['status' => ReservationStatus::CANCELLED, 'canceled_at' => now()]);
        MoonShineUI::toast('Reserva cancelada.', 'warning');
        return back();
    }
    public function convertToContract(MoonShineRequest $request): mixed
    {
        $item = $request->getResource()->getItem();
        $vehicle = $item->vehicle;
        if (!$vehicle) {
            MoonShineUI::toast('Veé­culo né£o encontrado na reserva.', 'error');
            return back();
        }
        if ($item->contract()->exists()) {
            MoonShineUI::toast('Jé¡ existe um contrato gerado para esta reserva.', 'error');
            return back();
        }
        $contract = \App\Models\Contract::create([
            'branch_id' => $item->branch_id,
            'reservation_id' => $item->id,
            'customer_id' => $item->customer_id,
            'vehicle_id' => $item->vehicle_id,
            'contract_number' => \App\Models\Contract::generateContractNumber(),
            'pickup_date' => $item->pickup_date,
            'return_date' => $item->return_date,
            'pickup_mileage' => $vehicle->mileage ?? 0,
            'daily_rate' => $item->daily_rate,
            'total_days' => $item->total_days,
            'extras_total' => $item->extras_total,
            'discount' => $item->discount,
            'total' => $item->total,
            'status' => \App\Enums\ContractStatus::DRAFT,
            'created_by' => auth()->id() ?? 1,
        ]);
        $item->update(['status' => ReservationStatus::IN_PROGRESS]);
        MoonShineUI::toast('Contrato gerado com sucesso! (Rascunho)', 'success');
        return back();
    }
}
