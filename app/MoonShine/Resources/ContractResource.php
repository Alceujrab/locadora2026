<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\Contract;
use App\Enums\ContractStatus;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use App\MoonShine\Pages\Contract\ContractIndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Pages\Contract\ContractDetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use App\MoonShine\Resources\CustomerResource;
use App\MoonShine\Resources\VehicleResource;
use App\MoonShine\Resources\ReservationResource;
use App\MoonShine\Resources\BranchResource;
use App\MoonShine\Resources\ContractTemplateResource;
use App\MoonShine\Resources\VehicleInspection\VehicleInspectionResource;
use App\MoonShine\Resources\VehicleInspection\Pages\VehicleInspectionFormPage;
use MoonShine\Laravel\MoonShineUI;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\UI\Components\ActionButton;
use App\Services\ContractService;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Storage;
use MoonShine\Support\Enums\HttpMethod;
/**
 * @extends ModelResource<Contract>
 */
class ContractResource extends ModelResource
{
    protected string $model = Contract::class;
    protected string $title = 'Contratos';
    protected string $column = 'contract_number';
    protected bool $columnSelection = true;
    protected function pages(): array
    {
        return [
            ContractIndexPage::class,
            FormPage::class,
            ContractDetailPage::class,
        ];
    }
    public function search(): array
    {
        return ['contract_number', 'id'];
    }
    public function query(): \Illuminate\Contracts\Database\Eloquent\Builder
    {
        $query = parent::query();
        return $query;
    }
    public function getActiveActions(): array
    {
        return [
            \MoonShine\Support\Enums\Action::CREATE,
            \MoonShine\Support\Enums\Action::VIEW,
            \MoonShine\Support\Enums\Action::UPDATE,
            \MoonShine\Support\Enums\Action::DELETE,
            \MoonShine\Support\Enums\Action::MASS_DELETE,
        ];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Nº Contrato', 'contract_number')->sortable(),
            BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class),
            BelongsTo::make('Veículo', 'vehicle', resource: VehicleResource::class),
            Date::make('Retirada', 'pickup_date')->sortable(),
            Date::make('Devolução', 'return_date'),
            Number::make('Total (R$)', 'total')
                ->sortable(),
            Enum::make('Status', 'status')
                ->attach(ContractStatus::class),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            Box::make('Contrato', [
                ID::make(),
                Text::make('Nº Contrato', 'contract_number'),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                BelongsTo::make('Reserva', 'reservation', resource: ReservationResource::class)
                    ->nullable()
                    ->searchable(),
                BelongsTo::make('Template', 'template', resource: ContractTemplateResource::class)
                    ->nullable(),
            ]),
            Box::make('Partes', [
                BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class)
                    ->required()
                    ->searchable(),
                BelongsTo::make('Veículo', 'vehicle', resource: VehicleResource::class)
                    ->required()
                    ->searchable(),
            ]),
            Box::make('Datas', [
                Date::make('Data Retirada', 'pickup_date')->required(),
                Date::make('Data Devolução Prevista', 'return_date')->required(),
                Date::make('Data Devolução Real', 'actual_return_date'),
            ]),
            Box::make('Quilometragem', [
                Number::make('Km Retirada', 'pickup_mileage')->min(0),
                Number::make('Km Devolução', 'return_mileage')->min(0),
            ]),
            Box::make('Valores', [
                Number::make('Diária (R$)', 'daily_rate')
                    ->step(0.01)->min(0),
                Number::make('Total Dias', 'total_days')
                    ->min(1),
                Number::make('Extras (R$)', 'extras_total')
                    ->step(0.01)->min(0),
                Number::make('Caução (R$)', 'caution_amount')
                    ->step(0.01)->min(0),
                Number::make('Desconto (R$)', 'discount')
                    ->step(0.01)->min(0),
                Number::make('Total (R$)', 'total')
                    ->step(0.01)->min(0),
            ]),
            Box::make('Status e Assinatura', [
                Enum::make('Status', 'status')
                    ->attach(ContractStatus::class),
                Date::make('Assinado em', 'signed_at'),
                Text::make('IP Assinatura', 'signature_ip'),
                Text::make('Método', 'signature_method'),
            ]),
            Box::make('Observações', [
                Textarea::make('Observações', 'notes'),
            ]),
        ];
    }
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Nº Contrato', 'contract_number'),
            BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
            BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class),
            BelongsTo::make('Veículo', 'vehicle', resource: VehicleResource::class),
            BelongsTo::make('Reserva', 'reservation', resource: ReservationResource::class)->nullable(),
            BelongsTo::make('Template', 'template', resource: ContractTemplateResource::class)->nullable(),
            Date::make('Data Retirada', 'pickup_date'),
            Date::make('Data Devolução Prevista', 'return_date'),
            Date::make('Data Devolução Real', 'actual_return_date'),
            Number::make('Km Retirada', 'pickup_mileage'),
            Number::make('Km Devolução', 'return_mileage'),
            Number::make('Diária (R$)', 'daily_rate'),
            Number::make('Total Dias', 'total_days'),
            Number::make('Extras (R$)', 'extras_total'),
            Number::make('Caução (R$)', 'caution_amount'),
            Number::make('Desconto (R$)', 'discount'),
            Number::make('Total (R$)', 'total'),
            Enum::make('Status', 'status')->attach(ContractStatus::class),
            Date::make('Assinado em', 'signed_at'),
            Text::make('IP Assinatura', 'signature_ip'),
            Text::make('Método', 'signature_method'),
            Textarea::make('Observações', 'notes'),
        ];
    }
    protected function filters(): iterable
    {
        return [
            Enum::make('Status', 'status')
                ->attach(ContractStatus::class),
            Date::make('Retirada a partir', 'pickup_date'),
            BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class)
                ->nullable(),
        ];
    }
    protected function rules($item): array
    {
        return [
            'customer_id' => ['required'],
            'vehicle_id' => ['required'],
            'pickup_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after:pickup_date'],
        ];
    }
    /**
     * @return list<ActionButtonContract>
     */
    public function indexButtons(): iterable
    {
        return [
            ActionButton::make('Gerar PDF', fn(Contract $item) => route('admin.contract.generatePdf', $item->id))
                ->icon('document-text')
                ->primary()
                ->async(HttpMethod::POST)
                ->canSee(fn($item) => $item->template_id !== null),
            ActionButton::make('Baixar PDF', fn(Contract $item) => Storage::disk('public')->url($item->pdf_path))
                ->icon('arrow-down-tray')
                ->blank()
                ->canSee(fn($item) => $item->pdf_path !== null),
            ActionButton::make('Check-out (Entrega)', fn(Contract $item) => route('admin.contract.checkout', $item->id))
                ->icon('truck')
                ->success()
                ->async(HttpMethod::POST)
                ->canSee(fn($item) => in_array($item->status, [ContractStatus::DRAFT, ContractStatus::AWAITING_SIGNATURE])),
            ActionButton::make('Check-in (Devolução)', fn(Contract $item) => route('admin.contract.checkin', $item->id))
                ->icon('arrow-uturn-left')
                ->warning()
                ->async(HttpMethod::POST)
                ->canSee(fn($item) => $item->status === ContractStatus::ACTIVE),
            ActionButton::make('Gerar Fatura', fn(Contract $item) => route('admin.contract.generateInvoices', $item->id))
                ->icon('banknotes')
                ->warning()
                ->async(HttpMethod::POST)
                ->canSee(fn($item) => in_array($item->status, [ContractStatus::ACTIVE, ContractStatus::FINISHED]) && !$item->invoices()->exists()),
        ];
    }
    /**
     * @return list<ActionButtonContract>
     */
    public function detailButtons(): iterable
    {
        return $this->indexButtons();
    }
}
