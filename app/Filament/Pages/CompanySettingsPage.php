<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanySettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Dados da Empresa';

    protected static ?string $title = 'Dados da Empresa';

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.company-settings';

    // Dados da Empresa
    public ?string $company_name = '';
    public ?string $company_cnpj = '';
    public ?string $company_phone = '';
    public ?string $company_email = '';
    public ?string $company_address = '';
    public ?string $company_city = '';
    public ?string $company_state = '';
    public ?string $company_zip = '';

    // PIX e Banco
    public ?string $pix_key = '';
    public ?string $pix_type = 'CNPJ';
    public ?string $pix_holder = '';
    public ?string $bank_name = '';
    public ?string $bank_agency = '';
    public ?string $bank_account = '';

    // Textos
    public ?string $invoice_terms = '';
    public ?string $invoice_footer = '';

    public function mount(): void
    {
        $this->company_name = Setting::get('company_name', 'Elite Locadora de Veiculos', null);
        $this->company_cnpj = Setting::get('company_cnpj', '00.000.000/0001-00', null);
        $this->company_phone = Setting::get('company_phone', '(66) 3521-0000', null);
        $this->company_email = Setting::get('company_email', 'contato@elitelocadora.com.br', null);
        $this->company_address = Setting::get('company_address', '', null);
        $this->company_city = Setting::get('company_city', 'Sinop', null);
        $this->company_state = Setting::get('company_state', 'MT', null);
        $this->company_zip = Setting::get('company_zip', '', null);
        $this->pix_key = Setting::get('pix_key', '00.000.000/0001-00', null);
        $this->pix_type = Setting::get('pix_type', 'CNPJ', null);
        $this->pix_holder = Setting::get('pix_holder', 'Elite Locadora de Veiculos LTDA', null);
        $this->bank_name = Setting::get('bank_name', 'Banco do Brasil', null);
        $this->bank_agency = Setting::get('bank_agency', '0001', null);
        $this->bank_account = Setting::get('bank_account', '12345-6', null);
        $this->invoice_terms = Setting::get('invoice_terms', 'Apos o pagamento, envie o comprovante pelo WhatsApp ou email.', null);
        $this->invoice_footer = Setting::get('invoice_footer', 'Este documento nao possui validade fiscal.', null);
    }

    public function save(): void
    {
        $fields = [
            'company_name', 'company_cnpj', 'company_phone', 'company_email',
            'company_address', 'company_city', 'company_state', 'company_zip',
            'pix_key', 'pix_type', 'pix_holder',
            'bank_name', 'bank_agency', 'bank_account',
            'invoice_terms', 'invoice_footer',
        ];

        foreach ($fields as $key) {
            Setting::set($key, $this->{$key} ?? '', 'empresa');
        }

        Notification::make()
            ->title('Dados salvos!')
            ->body('As configuracoes da empresa foram atualizadas com sucesso.')
            ->success()
            ->send();
    }
}
