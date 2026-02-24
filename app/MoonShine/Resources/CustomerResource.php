<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\Customer;
use App\Enums\CustomerType;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
/**
 * @extends ModelResource<Customer>
 */
class CustomerResource extends ModelResource
{
    protected string $model = Customer::class;
    protected ?PageType $redirectAfterSave = PageType::INDEX;
    protected string $title = 'Clientes';
    protected string $column = 'name';
    protected bool $columnSelection = true;
    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }
    public function search(): array
    {
        return ['name', 'cpf_cnpj', 'email', 'phone', 'rg'];
    }
    protected function afterSave(DataWrapperContract $item, FieldsContract $fields): DataWrapperContract
    {
        try {
            $customer = $item->toModel();
            if ($customer && $customer->email) {
                // Find existing user or create a new one for portal access
                $user = User::firstOrNew(['email' => $customer->email]);
                if (!$user->exists) {
                    $docClean = preg_replace('/\D/', '', $customer->cpf_cnpj ?? '');
                    $passwordBase = strlen($docClean) >= 4 ? substr($docClean, 0, 4) : Str::random(6);
                    $user->name = $customer->name;
                    $user->password = Hash::make('Muda@' . $passwordBase);
                }
                $user->branch_id = $customer->branch_id;
                $user->save();
                // Assign 'cliente' role via Spatie (if available)
                if (method_exists($user, 'hasRole') && !$user->hasRole('cliente')) {
                    $user->assignRole('cliente');
                }
                // Bind user to customer
                if ($customer->user_id !== $user->id) {
                    $customer->updateQuietly(['user_id' => $user->id]);
                }
            }
        } catch (\Throwable $e) {
            // Log error but don't block the save
            \Illuminate\Support\Facades\Log::warning('CustomerResource afterSave: ' . $e->getMessage());
        }
        return $item;
    }
    public function indexButtons(): array
    {
        return [
            \MoonShine\UI\Components\ActionButton::make(
                '📄 PDF',
                fn($item) => route('admin.customer.pdf', $item->getKey())
            )
            ->blank()
            ->showInLine()
            ->icon('printer'),
        ];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Nome', 'name')->sortable(),
            Enum::make('Tipo', 'type')
                ->attach(CustomerType::class),
            Text::make('CPF/CNPJ', 'cpf_cnpj'),
            Phone::make('Telefone', 'phone'),
            Email::make('E-mail', 'email'),
            Switcher::make('Bloqueado', 'is_blocked')
                ->sortable(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            Box::make('Dados Pessoais', [
                ID::make(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                Text::make('Nome', 'name')->required(),
                Enum::make('Tipo', 'type')
                    ->attach(CustomerType::class)
                    ->required(),
                Text::make('CPF/CNPJ', 'cpf_cnpj')->required(),
                Text::make('RG/IE', 'rg'),
                Date::make('Data Nascimento', 'birth_date'),
            ]),
            Box::make('Dados PJ', [
                Text::make('Razão Social', 'company_name'),
                Text::make('Inscrição Estadual', 'state_registration'),
            ]),
            Box::make('Contato', [
                Email::make('E-mail', 'email'),
                Phone::make('Telefone', 'phone'),
                Phone::make('WhatsApp', 'whatsapp'),
            ]),
            Box::make('CNH', [
                Text::make('Nº CNH', 'cnh_number'),
                Text::make('Categoria CNH', 'cnh_category'),
                Date::make('Validade CNH', 'cnh_expiry'),
            ]),
            Box::make('Endereço', [
                Text::make('CEP', 'address_zip'),
                Text::make('Rua', 'address_street'),
                Text::make('Número', 'address_number'),
                Text::make('Complemento', 'address_complement'),
                Text::make('Bairro', 'address_neighborhood'),
                Text::make('Cidade', 'address_city'),
                Text::make('UF', 'address_state'),
            ]),
            Box::make('Contato de Emergência', [
                Text::make('Nome Emergência', 'emergency_contact_name'),
                Phone::make('Telefone Emergência', 'emergency_contact_phone'),
                Text::make('Relação', 'emergency_contact_relation'),
            ]),
            Box::make('Status', [
                Switcher::make('Bloqueado', 'is_blocked'),
                Textarea::make('Motivo Bloqueio', 'blocked_reason'),
                Textarea::make('Observações', 'notes'),
            ]),
            Box::make('Documentos Anexos', [
                File::make('CNH (Frente e Verso)', 'doc_cnh')
                    ->dir('customers/cnh')
                    ->removable()
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'pdf']),
                File::make('Cartão CPF/CNPJ', 'doc_cpf_cnpj_card')
                    ->dir('customers/cpf_cnpj')
                    ->removable()
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'pdf']),
                File::make('Comprovante de Endereço', 'doc_address_proof')
                    ->dir('customers/address')
                    ->removable()
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'pdf']),
                File::make('Contrato Social (PJ)', 'doc_social_contract')
                    ->dir('customers/social_contract')
                    ->removable()
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'pdf']),
            ]),
        ];
    }
    protected function detailFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Tabs::make([
                \MoonShine\UI\Components\Tabs\Tab::make('Ficha do Cliente', $this->formFields()),
                \MoonShine\UI\Components\Tabs\Tab::make('Documentos e Fotos', [
                    \MoonShine\Laravel\Fields\Relationships\HasMany::make('Documentos Anexos', 'documents', resource: \App\MoonShine\Resources\CustomerDocument\CustomerDocumentResource::class)
                        ->creatable()
                ]),
            ])
        ];
    }
    protected function filters(): iterable
    {
        return [
            Enum::make('Tipo', 'type')
                ->attach(CustomerType::class),
            Switcher::make('Bloqueado', 'is_blocked'),
            Text::make('Cidade', 'address_city'),
        ];
    }
    protected function rules($item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'cpf_cnpj' => ['required', 'string', 'max:18'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
