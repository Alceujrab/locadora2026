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

        // Dinamicamente alimenta as configurações do Login Social via Banco de Dados (ignora erros de compilação CLI)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                config([
                    'socialiteplus.providers.google.client_id' => \App\Models\Setting::get('GOOGLE_CLIENT_ID', config('socialiteplus.providers.google.client_id')),
                    'socialiteplus.providers.google.client_secret' => \App\Models\Setting::get('GOOGLE_CLIENT_SECRET', config('socialiteplus.providers.google.client_secret')),
                    'socialiteplus.providers.facebook.client_id' => \App\Models\Setting::get('FACEBOOK_CLIENT_ID', config('socialiteplus.providers.facebook.client_id')),
                    'socialiteplus.providers.facebook.client_secret' => \App\Models\Setting::get('FACEBOOK_CLIENT_SECRET', config('socialiteplus.providers.facebook.client_secret')),
                    'socialiteplus.providers.github.client_id' => \App\Models\Setting::get('GITHUB_CLIENT_ID', config('socialiteplus.providers.github.client_id')),
                    'socialiteplus.providers.github.client_secret' => \App\Models\Setting::get('GITHUB_CLIENT_SECRET', config('socialiteplus.providers.github.client_secret')),
                    'socialiteplus.providers.linkedin.client_id' => \App\Models\Setting::get('LINKEDIN_CLIENT_ID', config('socialiteplus.providers.linkedin.client_id')),
                    'socialiteplus.providers.linkedin.client_secret' => \App\Models\Setting::get('LINKEDIN_CLIENT_SECRET', config('socialiteplus.providers.linkedin.client_secret')),
                ]);
            }
        } catch (\Exception $e) {
            // Silence DB exception during setup
        }

        // Audit trail on critical models
        $auditObserver = \App\Observers\AuditObserver::class;
        \App\Models\Contract::observe($auditObserver);
        \App\Models\Invoice::observe($auditObserver);
        \App\Models\Vehicle::observe($auditObserver);
        \App\Models\FineTraffic::observe($auditObserver);
        \App\Models\Nfse::observe($auditObserver);
    }
}
