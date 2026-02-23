<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\MenuManager\MenuItem;
use MoonShine\MenuManager\MenuGroup;

use App\MoonShine\Resources\BranchResource;
use App\MoonShine\Resources\VehicleCategoryResource;
use App\MoonShine\Resources\VehicleResource;
use App\MoonShine\Resources\CustomerResource;
use App\MoonShine\Resources\SupplierResource;
use App\MoonShine\Resources\RentalExtraResource;
use App\MoonShine\Resources\ContractTemplateResource;
use App\MoonShine\Resources\ReservationResource;
use App\MoonShine\Resources\ContractResource;
use App\MoonShine\Resources\ServiceOrderResource;
use App\MoonShine\Resources\VehicleInspection\VehicleInspectionResource;
use App\MoonShine\Pages\CalendarPage;
use App\MoonShine\Pages\BookingCalendarPage;
use App\MoonShine\Resources\InspectionItem\InspectionItemResource;
use App\MoonShine\Resources\AccountPayable\AccountPayableResource;
use App\MoonShine\Resources\AccountReceivable\AccountReceivableResource;
use App\MoonShine\Resources\Payment\PaymentResource;
use App\MoonShine\Resources\Invoice\InvoiceResource;
use App\MoonShine\Pages\CashFlowPage;
use App\MoonShine\Resources\Caution\CautionResource;
use App\MoonShine\Resources\SupportTicket\SupportTicketResource;
use App\MoonShine\Resources\ServiceOrderItem\ServiceOrderItemResource;
use App\MoonShine\Resources\MaintenanceAlert\MaintenanceAlertResource;
use App\MoonShine\Resources\FineTraffic\FineTrafficResource;
use App\MoonShine\Resources\Nfse\NfseResource;
use App\MoonShine\Resources\AuditLog\AuditLogResource;

final class MoonShineLayout extends AppLayout
{
    protected ?string $palette = PurplePalette::class;

    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            MenuGroup::make('Cadastros', [
                MenuItem::make(BranchResource::class, 'Filiais')
                    ->icon('building-office'),
                MenuItem::make(VehicleCategoryResource::class, 'Categorias')
                    ->icon('tag'),
                MenuItem::make(VehicleResource::class, 'Veículos')
                    ->icon('truck'),
                MenuItem::make(CustomerResource::class, 'Clientes')
                    ->icon('users'),
                MenuItem::make(SupplierResource::class, 'Fornecedores')
                    ->icon('building-storefront'),
                MenuItem::make(RentalExtraResource::class, 'Extras de Locação')
                    ->icon('puzzle-piece'),
            ])->icon('folder'),

            MenuGroup::make('Operacional', [
                MenuItem::make(BookingCalendarPage::class, 'Calendário de Frota (Timeline)')
                    ->icon('map'),
                MenuItem::make(CalendarPage::class, 'Calendário Visual')
                    ->icon('calendar'),
                MenuItem::make(ReservationResource::class, 'Reservas')
                    ->icon('calendar-days'),
                MenuItem::make(ContractResource::class, 'Contratos')
                    ->icon('document-text'),
                MenuItem::make(ContractTemplateResource::class, 'Templates')
                    ->icon('document-duplicate'),
                MenuItem::make(VehicleInspectionResource::class, 'Vistorias')
                    ->icon('clipboard-document-check'),
                MenuItem::make(ServiceOrderResource::class, 'Ordens de Serviço')
                    ->icon('wrench-screwdriver'),
                MenuItem::make(MaintenanceAlertResource::class, 'Alertas de Frota')
                    ->icon('bell-alert'),
                MenuItem::make(FineTrafficResource::class, 'Multas de Trânsito')
                    ->icon('exclamation-triangle'),
                MenuItem::make(SupportTicketResource::class, 'Help Desk (Chamados)')
                    ->icon('chat-bubble-left-ellipsis'),
            ])->icon('clipboard-document-list'),
            
            MenuGroup::make('Financeiro', [
                MenuItem::make(AccountReceivableResource::class, 'A Receber')
                    ->icon('arrow-trending-up'),
                MenuItem::make(AccountPayableResource::class, 'A Pagar')
                    ->icon('arrow-trending-down'),
                MenuItem::make(InvoiceResource::class, 'Faturas')
                    ->icon('document-text'),
                MenuItem::make(PaymentResource::class, 'Pagamentos/Recebimentos')
                    ->icon('currency-dollar'),
                MenuItem::make(CashFlowPage::class, 'Fluxo de Caixa')
                    ->icon('chart-pie'),
                MenuItem::make(CautionResource::class, 'Cauções')
                    ->icon('lock-closed'),
                MenuItem::make(NfseResource::class, 'NFS-e')
                    ->icon('document-check'),
            ])->icon('banknotes'),

            MenuItem::make(AuditLogResource::class, 'Auditoria')
                ->icon('shield-check'),
        ];
    }

    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);
    }
}
