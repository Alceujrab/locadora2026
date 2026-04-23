<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\WuzapiService;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WhatsAppSettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'WhatsApp / WuzAPI';

    protected static ?string $title = 'Configurações do WhatsApp';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.whatsapp-settings';

    /** @var array<string, mixed> */
    public array $data = [];

    /** @var array<string, mixed> */
    public array $testData = [];

    public function mount(): void
    {
        $this->form->fill([
            'wuzapi_api_url' => Setting::get('wuzapi_api_url', Setting::get('evolution_api_url', ''), null),
            'wuzapi_token' => Setting::get('wuzapi_token', Setting::get('evolution_api_key', ''), null),
            'wuzapi_instance_label' => Setting::get('wuzapi_instance_label', Setting::get('evolution_instance_name', ''), null),

            'whatsapp_enabled' => (bool) Setting::get('whatsapp_enabled', false, null),
            'whatsapp_notify_contracts' => (bool) Setting::get('whatsapp_notify_contracts', true, null),
            'whatsapp_notify_invoices' => (bool) Setting::get('whatsapp_notify_invoices', true, null),
            'whatsapp_notify_service_orders' => (bool) Setting::get('whatsapp_notify_service_orders', true, null),
            'whatsapp_notify_reservations' => (bool) Setting::get('whatsapp_notify_reservations', true, null),

            'whatsapp_default_message_header' => Setting::get('whatsapp_default_message_header', 'Elite Locadora', null),
            'whatsapp_default_message_footer' => Setting::get('whatsapp_default_message_footer', 'Elite Locadora - Aluguel de Veiculos', null),
        ]);

        $this->testForm->fill([
            'test_phone' => '',
            'test_message' => 'Mensagem de teste do sistema Elite Locadora.',
        ]);
    }

    /** @return array<int, string> */
    protected function getForms(): array
    {
        return ['form', 'testForm'];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Conexão com WuzAPI')
                    ->description('Configure a URL da API e o token do usuário gerado no painel do WuzAPI.')
                    ->icon('heroicon-o-signal')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('wuzapi_api_url')
                                ->label('URL da API')
                                ->placeholder('https://wuzapi.seudominio.com.br')
                                ->helperText('Ex.: https://wuzapi.seudominio.com.br ou http://localhost:8080')
                                ->url()
                                ->columnSpan(2),

                            TextInput::make('wuzapi_instance_label')
                                ->label('Rótulo da Instância')
                                ->placeholder('principal')
                                ->helperText('Identificação visual (opcional).'),
                        ]),

                        TextInput::make('wuzapi_token')
                            ->label('Token do Usuário')
                            ->placeholder('token gerado no painel do WuzAPI')
                            ->helperText('Enviado no header "token" de todas as requisições.')
                            ->password()
                            ->revealable()
                            ->autocomplete('off'),
                    ])
                    ->footerActions([
                        Action::make('testConnection')
                            ->label('Testar Conexão')
                            ->icon('heroicon-o-signal')
                            ->color('info')
                            ->action('testConnection'),
                    ]),

                Section::make('Ativação do WhatsApp')
                    ->description('Ative ou desative o envio de notificações por módulo.')
                    ->icon('heroicon-o-bell-alert')
                    ->schema([
                        Toggle::make('whatsapp_enabled')
                            ->label('WhatsApp Ativo')
                            ->helperText('Ativar/desativar todo o envio via WhatsApp no sistema.')
                            ->inline(false),

                        Grid::make(2)->schema([
                            Checkbox::make('whatsapp_notify_contracts')
                                ->label('Contratos')
                                ->helperText('Enviar contratos e assinaturas por WhatsApp.'),
                            Checkbox::make('whatsapp_notify_invoices')
                                ->label('Faturas')
                                ->helperText('Notificar sobre faturas pendentes e vencidas.'),
                            Checkbox::make('whatsapp_notify_service_orders')
                                ->label('Ordens de Serviço')
                                ->helperText('Enviar OS para assinatura digital via WhatsApp.'),
                            Checkbox::make('whatsapp_notify_reservations')
                                ->label('Reservas')
                                ->helperText('Confirmar reservas e lembretes por WhatsApp.'),
                        ]),
                    ]),

                Section::make('Mensagens Padrão')
                    ->description('Cabeçalho e rodapé anexados às mensagens enviadas.')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('whatsapp_default_message_header')
                                ->label('Cabeçalho da Mensagem')
                                ->placeholder('Elite Locadora')
                                ->helperText('Texto adicionado no início de cada mensagem.'),
                            TextInput::make('whatsapp_default_message_footer')
                                ->label('Rodapé da Mensagem')
                                ->placeholder('Elite Locadora - Aluguel de Veículos')
                                ->helperText('Texto adicionado no final de cada mensagem.'),
                        ]),
                    ]),
            ]);
    }

    public function testForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('testData')
            ->components([
                Section::make('Enviar mensagem de teste')
                    ->description('Dispara uma mensagem de texto pelo WuzAPI usando o cabeçalho/rodapé configurados acima. Salve as configurações antes de testar.')
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('test_phone')
                                ->label('Número de destino')
                                ->placeholder('66999998888')
                                ->helperText('DDD + número. O prefixo 55 é adicionado automaticamente.')
                                ->required()
                                ->columnSpan(1),

                            Textarea::make('test_message')
                                ->label('Texto da mensagem')
                                ->rows(2)
                                ->columnSpan(2),
                        ]),
                    ])
                    ->footerActions([
                        Action::make('sendTestMessage')
                            ->label('Enviar teste')
                            ->icon('heroicon-o-paper-airplane')
                            ->color('success')
                            ->action('sendTestMessage'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('wuzapi_api_url', $data['wuzapi_api_url'] ?? '', 'whatsapp');
        Setting::set('wuzapi_token', $data['wuzapi_token'] ?? '', 'whatsapp');
        Setting::set('wuzapi_instance_label', $data['wuzapi_instance_label'] ?? '', 'whatsapp');

        Setting::set('evolution_api_url', $data['wuzapi_api_url'] ?? '', 'whatsapp');
        Setting::set('evolution_api_key', $data['wuzapi_token'] ?? '', 'whatsapp');
        Setting::set('evolution_instance_name', $data['wuzapi_instance_label'] ?? '', 'whatsapp');

        Setting::set('whatsapp_enabled', !empty($data['whatsapp_enabled']) ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_contracts', !empty($data['whatsapp_notify_contracts']) ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_invoices', !empty($data['whatsapp_notify_invoices']) ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_service_orders', !empty($data['whatsapp_notify_service_orders']) ? '1' : '0', 'whatsapp');
        Setting::set('whatsapp_notify_reservations', !empty($data['whatsapp_notify_reservations']) ? '1' : '0', 'whatsapp');

        Setting::set('whatsapp_default_message_header', $data['whatsapp_default_message_header'] ?? '', 'whatsapp');
        Setting::set('whatsapp_default_message_footer', $data['whatsapp_default_message_footer'] ?? '', 'whatsapp');

        Notification::make()
            ->title('Configurações salvas!')
            ->body('As configurações do WhatsApp (WuzAPI) foram atualizadas com sucesso.')
            ->success()
            ->send();
    }

    public function testConnection(): void
    {
        $data = $this->form->getState();
        $url = $data['wuzapi_api_url'] ?? '';
        $token = $data['wuzapi_token'] ?? '';

        if (empty($url) || empty($token)) {
            Notification::make()
                ->title('Dados incompletos')
                ->body('Preencha a URL e o token do WuzAPI antes de testar.')
                ->danger()
                ->send();
            return;
        }

        Setting::set('wuzapi_api_url', $url, 'whatsapp');
        Setting::set('wuzapi_token', $token, 'whatsapp');

        try {
            $status = app(WuzapiService::class)->getConnectionStatus();

            if ($status !== null) {
                $payload = $status['data'] ?? $status;
                $connected = $payload['connected'] ?? $payload['Connected'] ?? false;
                $loggedIn = $payload['loggedIn'] ?? $payload['LoggedIn'] ?? false;
                $jid = $payload['jid'] ?? $payload['JID'] ?? '';
                $name = $payload['name'] ?? $payload['Name'] ?? '';

                $state = $connected && $loggedIn
                    ? 'Conectado e Logado'
                    : ($connected ? 'Conectado (sem login)' : 'Desconectado');

                $detail = $state . ($name ? " - {$name}" : '') . ($jid ? " ({$jid})" : '');

                Notification::make()
                    ->title('Conexão com WuzAPI OK!')
                    ->body("Status: {$detail}")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Erro na conexão')
                    ->body('Não foi possível consultar o status da sessão no WuzAPI. Verifique URL e token.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Falha na conexão')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function sendTestMessage(): void
    {
        $config = $this->form->getState();
        $test = $this->testForm->getState();

        $phone = trim((string) ($test['test_phone'] ?? ''));
        if ($phone === '') {
            Notification::make()
                ->title('Número obrigatório')
                ->body('Informe o número de destino no formato DDD + número (ex: 66992184925).')
                ->danger()
                ->send();
            return;
        }

        if (empty($config['wuzapi_api_url']) || empty($config['wuzapi_token'])) {
            Notification::make()
                ->title('Configuração incompleta')
                ->body('Preencha e salve a URL e o token do WuzAPI antes de enviar testes.')
                ->danger()
                ->send();
            return;
        }

        $header = trim((string) ($config['whatsapp_default_message_header'] ?? ''));
        $footer = trim((string) ($config['whatsapp_default_message_footer'] ?? ''));
        $body = trim((string) ($test['test_message'] ?? '')) ?: 'Mensagem de teste.';

        $full = ($header ? "*{$header}*\n\n" : '')
            . $body
            . ($footer ? "\n\n_{$footer}_" : '');

        try {
            $result = app(WuzapiService::class)->sendText($phone, $full);

            if ($result !== null) {
                Notification::make()
                    ->title('Mensagem enviada!')
                    ->body("Enviado para {$phone}. Verifique o WhatsApp do destinatário.")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Falha no envio')
                    ->body('A API não confirmou o envio. Verifique storage/logs/laravel.log.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro ao enviar')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
