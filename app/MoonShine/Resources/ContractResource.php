<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\Contract;
use App\Enums\ContractStatus;
use MoonShine\Laravel\Resources\ModelResource;
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
use MoonShine\Laravel\Fields\Relationships\HasMany;
use App\MoonShine\Resources\CustomerResource;
use App\MoonShine\Resources\VehicleResource;
use App\MoonShine\Resources\ReservationResource;
use App\MoonShine\Resources\BranchResource;
use App\MoonShine\Resources\ContractTemplateResource;
use App\MoonShine\Resources\VehicleInspection\VehicleInspectionResource;
use App\MoonShine\Resources\VehicleInspection\Pages\VehicleInspectionFormPage;
use MoonShine\Laravel\MoonShineUI;
use MoonShine\Laravel\Http\Requests\MoonShineRequest;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\UI\Components\ActionButton;
use App\Services\ContractService;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Storage;
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
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
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
        return ['create', 'view', 'update', 'delete', 'massDelete', 'export'];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('NÂº Contrato', 'contract_number')->sortable(),
            BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class),
            BelongsTo::make('VeÃ­culo', 'vehicle', resource: VehicleResource::class),
            Date::make('Retirada', 'pickup_date')->sortable(),
            Date::make('DevoluÃ§Ã£o', 'return_date'),
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
                Text::make('NÂº Contrato', 'contract_number'),
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
                BelongsTo::make('VeÃ­culo', 'vehicle', resource: VehicleResource::class)
                    ->required()
                    ->searchable(),
            ]),
            Box::make('Datas', [
                Date::make('Data Retirada', 'pickup_date')->required(),
                Date::make('Data DevoluÃ§Ã£o Prevista', 'return_date')->required(),
                Date::make('Data DevoluÃ§Ã£o Real', 'actual_return_date'),
            ]),
            Box::make('Quilometragem', [
                Number::make('Km Retirada', 'pickup_mileage')->min(0),
                Number::make('Km DevoluÃ§Ã£o', 'return_mileage')->min(0),
            ]),
            Box::make('Valores', [
                Number::make('DiÃ¡ria (R$)', 'daily_rate')
                    ->step(0.01)->min(0),
                Number::make('Total Dias', 'total_days')
                    ->min(1),
                Number::make('Extras (R$)', 'extras_total')
                    ->step(0.01)->min(0),
                Number::make('CauÃ§Ã£o (R$)', 'caution_amount')
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
                Text::make('MÃ©todo', 'signature_method'),
            ]),
            Box::make('ObservaÃ§Ãµes', [
                Textarea::make('ObservaÃ§Ãµes', 'notes'),
            ]),
        ];
    }
    protected function detailFields(): iterable
    {
        return [
            ...$this->formFields(),
            HasMany::make('Logs de AlteraÃ§Ã£o', 'logs', resource: new class extends ModelResource {
                protected string $model = \App\Models\ContractLog::class;
                protected string $title = 'Logs';
                public function fields(): array {
                    return [
                        \MoonShine\UI\Fields\ID::make(),
                        \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('UsuÃ¡rio', 'user', resource: new class extends ModelResource {
                            protected string $model = \App\Models\User::class;
                            public function fields(): array { return [\MoonShine\UI\Fields\Text::make('Nome', 'name')]; }
                        }),
                        \MoonShine\UI\Fields\Text::make('AÃ§Ã£o', 'action'),
                        \MoonShine\UI\Fields\Text::make('DescriÃ§Ã£o', 'description'),
                        \MoonShine\UI\Fields\Date::make('Data', 'created_at')->format('d/m/Y H:i:s'),
                    ];
                }
            })
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
            ActionButton::make('Gerar PDF', '#')
                ->icon('document-text')
                ->primary()
                ->method('generatePdf')
                ->canSee(fn($item) => $item->template_id !== null),
            ActionButton::make('Baixar PDF', fn($item) => Storage::disk('public')->url($item->pdf_path))
                ->icon('arrow-down-tray')
                ->blank()
                ->canSee(fn($item) => $item->pdf_path !== null),
            ActionButton::make('Assinar', '#')
                ->icon('pencil-square')
                ->success()
                ->method('sign')
                ->canSee(fn($item) => $item->status === ContractStatus::AWAITING_SIGNATURE && $item->pdf_path !== null),
            ActionButton::make('Check-out (Entrega)', '#')
                ->icon('truck')
                ->success()
                ->method('checkout')
                ->canSee(fn($item) => in_array($item->status, [ContractStatus::DRAFT, ContractStatus::AWAITING_SIGNATURE])),
            ActionButton::make('Check-in (DevoluÃ§Ã£o)', '#')
                ->icon('arrow-uturn-left')
                ->warning()
                ->method('checkin')
                ->canSee(fn($item) => $item->status === ContractStatus::ACTIVE),
            ActionButton::make('Gerar Fatura', '#')
                ->icon('banknotes')
                ->warning()
                ->method('generateInvoices')
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
    public function generatePdf(MoonShineRequest $request, ContractService $service): mixed
    {
        $item = $request->getResource()->getItem();
        if (!$item->template_id) {
            MoonShineUI::toast('Nenhum template selecionado neste contrato.', 'error');
            return back();
        }
        $result = $service->generatePdf($item);
        if ($result) {
            MoonShineUI::toast('PDF do contrato gerado com sucesso!', 'success');
        } else {
            MoonShineUI::toast('Erro ao gerar PDF: O template pode estar vazio.', 'error');
        }
        return back();
    }
    public function sign(MoonShineRequest $request, ContractService $service): mixed
    {
        $item = $request->getResource()->getItem();
        $success = $service->signContract($item, $request->ip());
        if ($success) {
            MoonShineUI::toast('Contrato assinado digitalmente com sucesso!', 'success');
        } else {
            MoonShineUI::toast('NÃ£o foi possÃ­vel assinar o contrato. Verifique o status.', 'error');
        }
        return back();
    }
    public function generateInvoices(MoonShineRequest $request, InvoiceService $service): mixed
    {
        $item = $request->getResource()->getItem();
        $invoices = $service->generateForContract($item, 1, 5); // 1 parcela, vencimento em 5 dias.
        if (count($invoices) > 0) {
            MoonShineUI::toast('Fatura gerada com sucesso!', 'success');
        } else {
            MoonShineUI::toast('Erro ao gerar fatura.', 'error');
        }
        return back();
    }
    public function checkout(MoonShineRequest $request): mixed
    {
        $contract = $request->getResource()->getItem();
        $inspection = \App\Models\VehicleInspection::firstOrCreate([
            'contract_id' => $contract->id,
            'type' => \App\Enums\InspectionType::CHECKOUT,
        ], [
            'vehicle_id' => $contract->vehicle_id,
            'inspector_user_id' => auth()->id() ?? 1,
            'status' => 'rascunho',
            'inspection_date' => now(),
            'mileage' => $contract->vehicle->mileage ?? 0,
            'fuel_level' => 100,
            'overall_condition' => 'Bom',
        ]);
        $contract->update(['status' => ContractStatus::ACTIVE]);
        if ($contract->reservation) {
            $contract->reservation->update(['status' => \App\Enums\ReservationStatus::IN_PROGRESS]);
        }
        $resource = new VehicleInspectionResource();
        $uri = to_page(FormPage::class, $resource, ['resourceItem' => $inspection->id]);
        return redirect($uri);
    }
    public function checkin(MoonShineRequest $request): mixed
    {
        $contract = $request->getResource()->getItem();
        $inspection = \App\Models\VehicleInspection::firstOrCreate([
            'contract_id' => $contract->id,
            'type' => \App\Enums\InspectionType::RETURN,
        ], [
            'vehicle_id' => $contract->vehicle_id,
            'inspector_user_id' => auth()->id() ?? 1,
            'status' => 'rascunho',
            'inspection_date' => now(),
            'mileage' => clone $contract->vehicle->mileage ?? clone $contract->pickup_mileage,
            'fuel_level' => 100,
            'overall_condition' => 'Bom',
        ]);
        $contract->update([
            'status' => ContractStatus::FINISHED,
            'actual_return_date' => now(),
        ]);
        if ($contract->reservation) {
            $contract->reservation->update(['status' => \App\Enums\ReservationStatus::COMPLETED]);
        }
        // Liberar VeÃ­culo
        $contract->vehicle->update(['status' => \App\Enums\VehicleStatus::AVAILABLE]);
        $resource = new VehicleInspectionResource();
        $uri = to_page(FormPage::class, $resource, ['resourceItem' => $inspection->id]);
        return redirect($uri);
    }
}
