<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\WuzapiService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class WhatsAppSettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'WhatsApp / WuzAPI';

    protected static ?string $title = 'Configurações do WhatsApp';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.whatsapp-settings';

    // Conexão WuzAPI
    public ?string $wuzapi_api_url = '';
    public ?string $wuzapi_token = '';
    public ?string $wuzapi_instance_label = '';

    // Toggles
    public bool $whatsapp_enabled = false;
    public bool $whatsapp_notify_contracts = true;
    public bool $whatsapp_notify_invoices = true;
    public bool $whatsapp_notify_service_orders = true;
    public bool $whatsapp_notify_reservations = true;

    // Mensagens padrão
    public ?string $whatsapp_default_message_header = '';
    public ?string $whatsapp_default_message_footer = '';

    public function mount(): void
    {
        // Lê chaves novas; se vazias, cai nas antigas (evolution_*) para migrar suave
        $this->wuzapi_api_url = Setting::get('wuzapi_api_url', Setting::get('evolution_api_url', ''), null);
        $this->wuzapi_token = Setting::get('wuzapi_token', Setting::get('evolution_api_key', ''), null);
        $this->wuzapi_instance_label = Setting::get('wuzapi_instance_label', Setting::get('evolution_instance_name', ''), null);

        $this->whatsapp_enabled = (bool) Setting::get('whatsapp_enabled', false, null);
        $this->whatsapp_notify_contracts = (bool) Setting::get('whatsapp_notify_contracts', true, null);
        $this->whatsapp_notify_invoices = (bool) Setting::get('whatsapp_notify_invoices', true, null);
        $this->whatsapp_notify_service_orders = (bool) Setting::get('whatsapp_notify_service_orders', true, null);
        $this->whatsapp_notify_reservations = (bool) Setting::get('whatsapp_notify_reservations', true, null);

        $this->whatsapp_default_message_header = Setting::get('whatsapp_default_message_header', 'Elite Locadora', null);
        $this->whatsapp_default_message_footer = Setting::get('whatsapp_default_message_footer', 'Elite Locadora - Aluguel de Veiculos', null);
    }

    public function save(): void
    {
        Setting::set('wuzapi_api_url', $this->wuzapi_api_url ?? '', 'whatsapp');
        Setting::set('wuzapi_token', $this->wuzapi_token ?? '', 'whatsapp');
        Setting::set('wuzapi_instance_label', $this->wuzapi_instance_label ?? '', 'whatsapp');

        // Mantém compatibilidade com chaves legadas (enquanto Setting::get pode cair nelas)
        Setting::set('evolution_api_url', $this->wuzapi_api_url ?? '', 'whatsapp');
        Setting::set('evolution_api_key', $this->wuzapi_token ?? '', 'whatsapp');
        Setting::set('evolution_instance_name', $this->wuzapi_instance_label ?? '', 'whatsapp');

        Setting::set('whatsapp_enabled', $this->whatsapp_enabled ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_contracts', $this->whatsapp_notify_contracts ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_invoices', $this->whatsapp_notify_invoices ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_service_orders', $this->whatsapp_notify_service_orders ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_reservations', $this->whatsapp_notify_reservations ? '1' : '0', 'whatsapp');

        Setting::set('whatsapp_default_message_header', $this->whatsapp_default_message_header ?? '', 'whatsapp');
        Setting::set('whatsapp_default_message_footer', $this->whatsapp_default_message_footer ?? '', 'whatsapp');

        Notification::make()
            ->title('Configuracoes salvas!')
            ->body('As configuracoes do WhatsApp (WuzAPI) foram atualizadas com sucesso.')
            ->success()
            ->send();
    }

    public function testConnection(): void
    {
        if (empty($this->wuzapi_api_url) || empty($this->wuzapi_token)) {
            Notification::make()
                ->title('Dados incompletos')
                ->body('Preencha a URL e o token do WuzAPI antes de testar.')
                ->danger()
                ->send();
            return;
        }

        // Salva antes para o service pegar os valores atualizados
        Setting::set('wuzapi_api_url', $this->wuzapi_api_url ?? '', 'whatsapp');
        Setting::set('wuzapi_token', $this->wuzapi_token ?? '', 'whatsapp');

        try {
            $status = app(WuzapiService::class)->getConnectionStatus();

            if ($status !== null) {
                $connected = $status['data']['Connected'] ?? $status['Connected'] ?? false;
                $loggedIn = $status['data']['LoggedIn'] ?? $status['LoggedIn'] ?? false;

                $state = $connected && $loggedIn
                    ? 'Conectado e Logado'
                    : ($connected ? 'Conectado (sem login)' : 'Desconectado');

                Notification::make()
                    ->title('Conexao com WuzAPI OK!')
                    ->body("Status: {$state}")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Erro na conexao')
                    ->body('Nao foi possivel consultar o status da sessao no WuzAPI. Verifique URL e token.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Falha na conexao')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
