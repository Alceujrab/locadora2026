<?php

namespace App\Filament\Resources;

use App\Enums\CustomerType;
use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Cadastros';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'cpf_cnpj', 'email', 'phone', 'rg'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Tabs::make('Cliente')->tabs([
                \Filament\Schemas\Components\Tabs\Tab::make('Dados Pessoais')->icon('heroicon-o-user')->schema([
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload(),
                    Components\TextInput::make('name')->label('Nome')->required()->maxLength(255),
                    Components\Select::make('type')->label('Tipo')->options(CustomerType::class)->required()->live(),
                    Components\TextInput::make('cpf_cnpj')->label('CPF/CNPJ')->required()->maxLength(18),
                    Components\TextInput::make('rg')->label('RG/IE')->maxLength(20),
                    Components\DatePicker::make('birth_date')->label('Data de Nascimento'),
                    Components\TextInput::make('company_name')->label('Razao Social')->visible(fn (Get $get) => $get('type') === 'pj'),
                    Components\TextInput::make('state_registration')->label('Inscricao Estadual')->visible(fn (Get $get) => $get('type') === 'pj'),
                    Components\TextInput::make('responsible_name')->label('Responsavel')->visible(fn (Get $get) => $get('type') === 'pj'),
                    Components\TextInput::make('responsible_cpf')->label('CPF Responsavel')->visible(fn (Get $get) => $get('type') === 'pj'),
                ])->columns(3),

                \Filament\Schemas\Components\Tabs\Tab::make('Contato')->icon('heroicon-o-phone')->schema([
                    Components\TextInput::make('email')->label('E-mail')->email(),
                    Components\TextInput::make('phone')->label('Telefone')->tel(),
                    Components\TextInput::make('whatsapp')->label('WhatsApp')->tel(),
                ])->columns(3),

                \Filament\Schemas\Components\Tabs\Tab::make('CNH')->icon('heroicon-o-identification')->schema([
                    Components\TextInput::make('cnh_number')->label('No CNH'),
                    Components\TextInput::make('cnh_category')->label('Categoria'),
                    Components\DatePicker::make('cnh_expiry')->label('Validade'),
                ])->columns(3),

                \Filament\Schemas\Components\Tabs\Tab::make('Endereco')->icon('heroicon-o-map-pin')->schema([
                    Components\TextInput::make('address_zip')->label('CEP')->maxLength(9),
                    Components\TextInput::make('address_street')->label('Logradouro'),
                    Components\TextInput::make('address_number')->label('Numero')->maxLength(10),
                    Components\TextInput::make('address_complement')->label('Complemento'),
                    Components\TextInput::make('address_neighborhood')->label('Bairro'),
                    Components\TextInput::make('address_city')->label('Cidade'),
                    Components\TextInput::make('address_state')->label('UF')->maxLength(2),
                ])->columns(3),

                \Filament\Schemas\Components\Tabs\Tab::make('Emergencia')->icon('heroicon-o-exclamation-triangle')->schema([
                    Components\TextInput::make('emergency_contact_name')->label('Nome'),
                    Components\TextInput::make('emergency_contact_phone')->label('Telefone')->tel(),
                    Components\TextInput::make('emergency_contact_relation')->label('Relacao'),
                ])->columns(3),

                \Filament\Schemas\Components\Tabs\Tab::make('Documentos')->icon('heroicon-o-document-arrow-up')->schema([
                    Components\FileUpload::make('doc_cnh')->label('CNH (Frente e Verso)')->directory('customers/cnh')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])->openable()->downloadable(),
                    Components\FileUpload::make('doc_cpf_cnpj_card')->label('Cartao CPF/CNPJ')->directory('customers/cpf_cnpj')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])->openable()->downloadable(),
                    Components\FileUpload::make('doc_address_proof')->label('Comprovante de Endereco')->directory('customers/address_proof')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])->openable()->downloadable(),
                    Components\FileUpload::make('doc_social_contract')->label('Contrato Social (PJ)')->directory('customers/social_contract')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])->openable()->downloadable()
                        ->visible(fn (Get $get) => $get('type') === 'pj'),
                ])->columns(2),

                \Filament\Schemas\Components\Tabs\Tab::make('Status')->icon('heroicon-o-shield-check')->schema([
                    Components\Toggle::make('is_blocked')->label('Bloqueado'),
                    Components\Textarea::make('blocked_reason')->label('Motivo Bloqueio')->visible(fn (Get $get) => $get('is_blocked')),
                    Components\Textarea::make('notes')->label('Observacoes')->columnSpanFull(),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge()
                    ->formatStateUsing(fn ($state) => $state === 'pf' || (is_object($state) && $state->value === 'pf') ? 'PF' : 'PJ')
                    ->color(fn ($state) => $state === 'pf' || (is_object($state) && $state->value === 'pf') ? 'info' : 'warning'),
                Tables\Columns\TextColumn::make('cpf_cnpj')->label('CPF/CNPJ')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefone')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('E-mail')->searchable(),
                Tables\Columns\IconColumn::make('is_blocked')->label('Bloqueado')->boolean()
                    ->trueIcon('heroicon-o-lock-closed')->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')->falseColor('success')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Tipo')->options(CustomerType::class),
                Tables\Filters\TernaryFilter::make('is_blocked')->label('Bloqueado'),
                Tables\Filters\SelectFilter::make('branch_id')->label('Filial')->relationship('branch', 'name'),
            ])
            ->actions([
                Actions\Action::make('pdf')->label('PDF')->icon('heroicon-o-document-arrow-down')->color('info')
                    ->url(fn (Customer $record) => route('admin.customer.pdf', $record->getKey()))->openUrlInNewTab(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
