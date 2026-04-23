<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

    /** @var array<string, mixed> */
    public array $data = [];

    private const FIELDS = [
        'company_name', 'company_cnpj', 'company_phone', 'company_email',
        'company_address', 'company_city', 'company_state', 'company_zip',
        'pix_key', 'pix_type', 'pix_holder',
        'bank_name', 'bank_agency', 'bank_account',
        'invoice_terms', 'invoice_footer',
    ];

    public function mount(): void
    {
        $this->form->fill([
            'company_name' => Setting::get('company_name', 'Elite Locadora de Veiculos', null),
            'company_cnpj' => Setting::get('company_cnpj', '00.000.000/0001-00', null),
            'company_phone' => Setting::get('company_phone', '(66) 3521-0000', null),
            'company_email' => Setting::get('company_email', 'contato@elitelocadora.com.br', null),
            'company_address' => Setting::get('company_address', '', null),
            'company_city' => Setting::get('company_city', 'Sinop', null),
            'company_state' => Setting::get('company_state', 'MT', null),
            'company_zip' => Setting::get('company_zip', '', null),

            'pix_type' => Setting::get('pix_type', 'CNPJ', null),
            'pix_key' => Setting::get('pix_key', '00.000.000/0001-00', null),
            'pix_holder' => Setting::get('pix_holder', 'Elite Locadora de Veiculos LTDA', null),
            'bank_name' => Setting::get('bank_name', 'Banco do Brasil', null),
            'bank_agency' => Setting::get('bank_agency', '0001', null),
            'bank_account' => Setting::get('bank_account', '12345-6', null),

            'invoice_terms' => Setting::get('invoice_terms', 'Apos o pagamento, envie o comprovante pelo WhatsApp ou email.', null),
            'invoice_footer' => Setting::get('invoice_footer', 'Este documento nao possui validade fiscal.', null),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Dados da Empresa')
                    ->description('Informações que aparecem nos PDFs de faturas e ordens de serviço.')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('company_name')
                                ->label('Razão Social / Nome')
                                ->placeholder('Elite Locadora de Veículos')
                                ->required(),
                            TextInput::make('company_cnpj')
                                ->label('CNPJ')
                                ->placeholder('00.000.000/0001-00'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('company_phone')
                                ->label('Telefone')
                                ->placeholder('(66) 3521-0000')
                                ->tel(),
                            TextInput::make('company_email')
                                ->label('E-mail')
                                ->placeholder('contato@empresa.com.br')
                                ->email(),
                            TextInput::make('company_zip')
                                ->label('CEP')
                                ->placeholder('78550-000'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('company_address')
                                ->label('Endereço')
                                ->placeholder('Rua, número, bairro')
                                ->columnSpan(2),
                            TextInput::make('company_city')
                                ->label('Cidade')
                                ->placeholder('Sinop'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('company_state')
                                ->label('UF')
                                ->placeholder('MT')
                                ->maxLength(2),
                        ]),
                    ]),

                Section::make('Dados Bancários e PIX')
                    ->description('Informações de pagamento que aparecem nas faturas e páginas de confirmação.')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('pix_type')
                                ->label('Tipo Chave PIX')
                                ->options([
                                    'CNPJ' => 'CNPJ',
                                    'CPF' => 'CPF',
                                    'E-mail' => 'E-mail',
                                    'Telefone' => 'Telefone',
                                    'Aleatoria' => 'Chave Aleatória',
                                ])
                                ->native(false),
                            TextInput::make('pix_key')
                                ->label('Chave PIX')
                                ->placeholder('00.000.000/0001-00'),
                            TextInput::make('pix_holder')
                                ->label('Titular PIX')
                                ->placeholder('Nome do titular'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('bank_name')
                                ->label('Banco')
                                ->placeholder('Banco do Brasil'),
                            TextInput::make('bank_agency')
                                ->label('Agência')
                                ->placeholder('0001'),
                            TextInput::make('bank_account')
                                ->label('Conta Corrente')
                                ->placeholder('12345-6'),
                        ]),
                    ]),

                Section::make('Textos de Documentos')
                    ->description('Textos personalizados que aparecem nos PDFs gerados pelo sistema.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)->schema([
                            Textarea::make('invoice_terms')
                                ->label('Instruções de Pagamento')
                                ->rows(4)
                                ->placeholder('Após o pagamento, envie o comprovante...')
                                ->helperText('Aparece no PDF da fatura, abaixo dos dados de pagamento.'),
                            Textarea::make('invoice_footer')
                                ->label('Rodapé dos Documentos')
                                ->rows(4)
                                ->placeholder('Este documento não possui validade fiscal...')
                                ->helperText('Aparece no final dos PDFs de faturas e OS.'),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (self::FIELDS as $key) {
            Setting::set($key, $data[$key] ?? '', 'empresa');
        }

        Notification::make()
            ->title('Dados salvos!')
            ->body('As configurações da empresa foram atualizadas com sucesso.')
            ->success()
            ->send();
    }
}
