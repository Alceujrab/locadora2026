<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Select;
/**
 * @extends ModelResource<Setting>
 */
class SettingResource extends ModelResource
{
    protected string $model = Setting::class;
    protected ?PageType $redirectAfterSave = PageType::INDEX;
    protected string $title = 'Parâmetros do Sistema';
    protected string $column = 'key';
    protected function pages(): array
    {
        return [
            \MoonShine\Laravel\Pages\Crud\IndexPage::class,
            \MoonShine\Laravel\Pages\Crud\FormPage::class,
            \MoonShine\Laravel\Pages\Crud\DetailPage::class,
        ];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Select::make('Grupo', 'group')->options([
                'socialite' => 'Social Login (Socialite)',
                'whatsapp' => 'WhatsApp (Evolution API)',
                'payment' => 'Gateway de Pagamento',
                'theme' => 'Visual e Cores do Painel',
                'general' => 'Geral'
            ])->badge('purple'),
            Text::make('Chave (Nome)', 'key'),
            Text::make('Valor', 'value')->changePreview(fn($value) => strlen((string) $value) > 30 ? substr((string) $value, 0, 30) . '...' : $value),
            Text::make('Descrição', 'description'),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make()->sortable(),
                Select::make('Grupo', 'group')->options([
                    'socialite' => 'Social Login (Socialite)',
                    'whatsapp' => 'WhatsApp (Evolution API)',
                    'payment' => 'Gateway de Pagamento',
                    'theme' => 'Visual e Cores do Painel',
                    'general' => 'Geral'
                ])->required()->badge('purple'),
                Text::make('Chave (Nome)', 'key')
                    ->hint('Ex: GOOGLE_CLIENT_ID')
                    ->required(),
                Textarea::make('Valor da Chave', 'value')
                    ->required(),
                Select::make('Tipo de Dado', 'type')->options([
                    'string' => 'Texto',
                    'integer' => 'Número Inteiro',
                    'boolean' => 'Booleano (1/0)',
                    'json' => 'Objeto JSON'
                ])->default('string')->required(),
                Text::make('Descrição', 'description'),
            ])
        ];
    }
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Select::make('Grupo', 'group')->options([
                'socialite' => 'Social Login (Socialite)',
                'whatsapp' => 'WhatsApp (Evolution API)',
                'payment' => 'Gateway de Pagamento',
                'theme' => 'Visual e Cores do Painel',
                'general' => 'Geral'
            ]),
            Text::make('Chave (Nome)', 'key'),
            Textarea::make('Valor da Chave', 'value'),
            Select::make('Tipo de Dado', 'type')->options([
                'string' => 'Texto',
                'integer' => 'Número Inteiro',
                'boolean' => 'Booleano (1/0)',
                'json' => 'Objeto JSON'
            ]),
            Text::make('Descrição', 'description'),
        ];
    }
    public function rules(Model $item): array
    {
        return [
            'key' => ['required', 'string', 'max:255'],
            'value' => ['required'],
            'group' => ['required', 'string'],
        ];
    }
}
