<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppSettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'WhatsApp / Evolution';

    protected static ?string $title = 'Configuracoes do WhatsApp';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.whatsapp-settings';

    // Form data
    public ?string $evolution_api_url = '';
    public ?string $evolution_api_key = '';
    public ?string $evolution_instance_name = '';
    public bool $whatsapp_enabled = false;
    public bool $whatsapp_notify_contracts = true;
    public bool $whatsapp_notify_invoices = true;
    public bool $whatsapp_notify_service_orders = true;
    public bool $whatsapp_notify_reservations = true;
    public ?string $whatsapp_default_message_header = '';
    public ?string $whatsapp_default_message_footer = '';

    public function mount(): void
    {
        $this->evolution_api_url = Setting::get('evolution_api_url', '', null);
        $this->evolution_api_key = Setting::get('evolution_api_key', '', null);
        $this->evolution_instance_name = Setting::get('evolution_instance_name', '', null);
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
        Setting::set('evolution_api_url', $this->evolution_api_url, 'whatsapp');
        Setting::set('evolution_api_key', $this->evolution_api_key, 'whatsapp');
        Setting::set('evolution_instance_name', $this->evolution_instance_name, 'whatsapp');
        Setting::set('whatsapp_enabled', $this->whatsapp_enabled ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_contracts', $this->whatsapp_notify_contracts ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_invoices', $this->whatsapp_notify_invoices ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_service_orders', $this->whatsapp_notify_service_orders ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_reservations', $this->whatsapp_notify_reservations ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_default_message_header', $this->whatsapp_default_message_header ?? '', 'whatsapp');
        Setting::set('whatsapp_default_message_footer', $this->whatsapp_default_message_footer ?? '', 'whatsapp');

        Notification::make()
            ->title('Configuracoes salvas!')
            ->body('As configuracoes do WhatsApp foram atualizadas com sucesso.')
            ->success()
            ->send();
    }

    public function testConnection(): void
    {
        if (empty($this->evolution_api_url) || empty($this->evolution_api_key) || empty($this->evolution_instance_name)) {
            Notification::make()
                ->title('Dados incompletos')
                ->body('Preencha URL, API Key e Nome da Instancia antes de testar.')
                ->danger()
                ->send();
            return;
        }

        try {
            $url = rtrim($this->evolution_api_url, '/') . '/instance/connectionState/' . $this->evolution_instance_name;

            $response = Http::withHeaders([
                'apikey' => $this->evolution_api_key,
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $state = $response->json('instance.state') ?? $response->json('state') ?? 'desconhecido';

                Notification::make()
                    ->title('Conexao OK!')
                    ->body("Instancia: {$this->evolution_instance_name} | Status: {$state}")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Erro na conexao')
                    ->body("HTTP {$response->status()}: {$response->body()}")
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
