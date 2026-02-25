<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class CompanySettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Dados da Empresa';

    protected static ?string $title = 'Dados da Empresa';

    protected static ?string $slug = 'company-settings';

    protected static ?int $navigationSort = 0;

    public function getView(): string
    {
        return 'filament.pages.company-settings';
    }

    public ?array $data = [];

    private array $settingsKeys = [
        'company_name' => 'Elite Locadora de Veiculos',
        'company_cnpj' => '00.000.000/0001-00',
        'company_phone' => '(66) 3521-0000',
        'company_email' => 'contato@elitelocadora.com.br',
        'company_address' => '',
        'company_city' => 'Sinop',
        'company_state' => 'MT',
        'company_zip' => '',
        'pix_key' => '00.000.000/0001-00',
        'pix_type' => 'CNPJ',
        'pix_holder' => 'Elite Locadora de Veiculos LTDA',
        'bank_name' => 'Banco do Brasil',
        'bank_agency' => '0001',
        'bank_account' => '12345-6',
        'invoice_terms' => 'Apos o pagamento, envie o comprovante pelo WhatsApp ou para contato@elitelocadora.com.br',
        'invoice_footer' => 'Este documento nao possui validade fiscal. Para nota fiscal, solicite a NFS-e.',
    ];

    public function mount(): void
    {
        $data = [];
        foreach ($this->settingsKeys as $key => $default) {
            $data[$key] = Setting::get($key, $default);
        }
        $this->data = $data;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Dados da Empresa')->schema([
                Grid::make(2)->schema([
                    Components\TextInput::make('company_name')->label('Razao Social / Nome')->required()->maxLength(255),
                    Components\TextInput::make('company_cnpj')->label('CNPJ')->required()->maxLength(20)->mask('99.999.999/9999-99'),
                ]),
                Grid::make(3)->schema([
                    Components\TextInput::make('company_phone')->label('Telefone')->maxLength(20),
                    Components\TextInput::make('company_email')->label('Email')->email()->maxLength(255),
                    Components\TextInput::make('company_zip')->label('CEP')->maxLength(10),
                ]),
                Grid::make(3)->schema([
                    Components\TextInput::make('company_address')->label('Endereco')->maxLength(255)->columnSpan(1),
                    Components\TextInput::make('company_city')->label('Cidade')->maxLength(100),
                    Components\TextInput::make('company_state')->label('UF')->maxLength(2),
                ]),
            ])->icon('heroicon-o-building-office-2'),

            Section::make('Dados Bancarios e PIX')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('pix_type')->label('Tipo Chave PIX')->options([
                        'CNPJ' => 'CNPJ',
                        'CPF' => 'CPF',
                        'E-mail' => 'E-mail',
                        'Telefone' => 'Telefone',
                        'Aleatoria' => 'Chave Aleatoria',
                    ])->required(),
                    Components\TextInput::make('pix_key')->label('Chave PIX')->required()->maxLength(255),
                    Components\TextInput::make('pix_holder')->label('Titular PIX')->required()->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    Components\TextInput::make('bank_name')->label('Banco')->maxLength(100),
                    Components\TextInput::make('bank_agency')->label('Agencia')->maxLength(20),
                    Components\TextInput::make('bank_account')->label('Conta Corrente')->maxLength(20),
                ]),
            ])->icon('heroicon-o-banknotes'),

            Section::make('Textos de Documentos')->schema([
                Components\Textarea::make('invoice_terms')->label('Instrucoes de Pagamento (aparece no PDF da Fatura)')->rows(2)->maxLength(500),
                Components\Textarea::make('invoice_footer')->label('Rodape da Fatura / OS (aparece no final do PDF)')->rows(2)->maxLength(500),
            ])->icon('heroicon-o-document-text'),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '', 'empresa');
        }

        Notification::make()->title('Dados salvos!')->body('As configuracoes da empresa foram atualizadas com sucesso.')->success()->send();
    }
}
