<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Setting;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Layout\Box;

class SystemSettingsPage extends Page
{
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return 'Parâmetros do Sistema';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        // Carrega todas as configurações existentes
        $settings = Setting::all()->keyBy('key');

        $data = [
            // Tema
            'PANEL_COLOR_PRIMARY' => $settings->get('PANEL_COLOR_PRIMARY')?->value ?? '#d97706',
            'PANEL_COLOR_SECONDARY' => $settings->get('PANEL_COLOR_SECONDARY')?->value ?? '#1e293b',
            'PANEL_COLOR_ACCENT' => $settings->get('PANEL_COLOR_ACCENT')?->value ?? '#f59e0b',
            'PANEL_DARK_MODE' => $settings->get('PANEL_DARK_MODE')?->value ?? '1',
            'PANEL_LOGO_TEXT' => $settings->get('PANEL_LOGO_TEXT')?->value ?? 'Elite Locadora',

            // Social Login
            'GOOGLE_CLIENT_ID' => $settings->get('GOOGLE_CLIENT_ID')?->value ?? '',
            'GOOGLE_CLIENT_SECRET' => $settings->get('GOOGLE_CLIENT_SECRET')?->value ?? '',
            'GOOGLE_REDIRECT_URI' => $settings->get('GOOGLE_REDIRECT_URI')?->value ?? '',
            'FACEBOOK_CLIENT_ID' => $settings->get('FACEBOOK_CLIENT_ID')?->value ?? '',
            'FACEBOOK_CLIENT_SECRET' => $settings->get('FACEBOOK_CLIENT_SECRET')?->value ?? '',

            // WhatsApp / Evolution API
            'EVOLUTION_API_URL' => $settings->get('EVOLUTION_API_URL')?->value ?? '',
            'EVOLUTION_API_KEY' => $settings->get('EVOLUTION_API_KEY')?->value ?? '',
            'EVOLUTION_INSTANCE_NAME' => $settings->get('EVOLUTION_INSTANCE_NAME')?->value ?? '',
            'WHATSAPP_NUMBER' => $settings->get('WHATSAPP_NUMBER')?->value ?? '',

            // Gateway Pagamento
            'PAYMENT_GATEWAY' => $settings->get('PAYMENT_GATEWAY')?->value ?? 'stripe',
            'PAYMENT_API_KEY' => $settings->get('PAYMENT_API_KEY')?->value ?? '',
            'PAYMENT_SECRET_KEY' => $settings->get('PAYMENT_SECRET_KEY')?->value ?? '',
            'PAYMENT_WEBHOOK_SECRET' => $settings->get('PAYMENT_WEBHOOK_SECRET')?->value ?? '',

            // Empresa
            'COMPANY_NAME' => $settings->get('COMPANY_NAME')?->value ?? '',
            'COMPANY_DOCUMENT' => $settings->get('COMPANY_DOCUMENT')?->value ?? '',
            'COMPANY_PHONE' => $settings->get('COMPANY_PHONE')?->value ?? '',
            'COMPANY_EMAIL' => $settings->get('COMPANY_EMAIL')?->value ?? '',
            'TIMEZONE' => $settings->get('TIMEZONE')?->value ?? 'America/Sao_Paulo',
        ];

        $html = view('admin.system-settings', [
            'settings' => $data,
            'saveUrl' => route('admin.settings.save'),
        ])->render();

        return [
            Box::make([
                \MoonShine\UI\Components\Layout\Html::make([$html]),
            ]),
        ];
    }
}
