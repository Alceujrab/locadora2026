<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;

use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
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
use App\MoonShine\Pages\CalendarPage;
use App\MoonShine\Resources\VehicleInspection\VehicleInspectionResource;
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
use App\MoonShine\Pages\BookingCalendarPage;
use App\MoonShine\Pages\FleetProfitabilityPage;
use App\MoonShine\Pages\DefaultReportPage;
use App\MoonShine\Resources\VehiclePhoto\VehiclePhotoResource;
use App\MoonShine\Resources\CustomerDocument\CustomerDocumentResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\Faq\FaqResource;
use App\MoonShine\Resources\Testimonial\TestimonialResource;
use App\MoonShine\Resources\PostCategory\PostCategoryResource;
use App\MoonShine\Resources\Post\PostResource;
use App\MoonShine\Resources\SeoMetadata\SeoMetadataResource;
use App\MoonShine\Resources\SettingResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  CoreContract<MoonShineConfigurator>  $core
     */
    public function boot(CoreContract $core): void
    {
        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                BranchResource::class,
                VehicleCategoryResource::class,
                VehicleResource::class,
                CustomerResource::class,
                SupplierResource::class,
                RentalExtraResource::class,
                ContractTemplateResource::class,
                ReservationResource::class,
                ContractResource::class,
                ServiceOrderResource::class,
                VehicleInspectionResource::class,
                InspectionItemResource::class,
                AccountPayableResource::class,
                AccountReceivableResource::class,
                PaymentResource::class,
                InvoiceResource::class,
                CautionResource::class,
                SupportTicketResource::class,
                ServiceOrderItemResource::class,
                MaintenanceAlertResource::class,
                FineTrafficResource::class,
                \App\MoonShine\Resources\Nfse\NfseResource::class,
                \App\MoonShine\Resources\AuditLog\AuditLogResource::class,
                VehiclePhotoResource::class,
                CustomerDocumentResource::class,
                PageResource::class,
                FaqResource::class,
                TestimonialResource::class,
                PostCategoryResource::class,
                PostResource::class,
                SeoMetadataResource::class,
                SettingResource::class,
            ])
            ->pages([
                ...$core->getConfig()->getPages(),
                CalendarPage::class,
                CashFlowPage::class,
                BookingCalendarPage::class,
                FleetProfitabilityPage::class,
                DefaultReportPage::class,
            ]);
    }
}
