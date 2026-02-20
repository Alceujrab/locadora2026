<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Invoice::observe(\App\Observers\InvoiceObserver::class);
        \App\Models\Contract::observe(\App\Observers\ContractObserver::class);
        \App\Models\ServiceOrder::observe(\App\Observers\ServiceOrderObserver::class);
        \App\Models\ServiceOrderItem::observe(\App\Observers\ServiceOrderItemObserver::class);

        // Audit trail on critical models
        $auditObserver = \App\Observers\AuditObserver::class;
        \App\Models\Contract::observe($auditObserver);
        \App\Models\Invoice::observe($auditObserver);
        \App\Models\Vehicle::observe($auditObserver);
        \App\Models\FineTraffic::observe($auditObserver);
        \App\Models\Nfse::observe($auditObserver);
    }
}
