<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

use MoonShine\Laravel\Resources\ModelResource;
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

    protected string $title = 'Chaves e APIs (Configurações)';
    
    protected string $column = 'key';

    public function fields(): array
    {
        return [
            Box::make([
                ID::make()->sortable(),
                Select::make('Grupo', 'group')->options([
                    'socialite' => 'Social Login (Socialite)',
                    'whatsapp' => 'WhatsApp (Evolution API)',
                    'payment' => 'Gateway de Pagamento',
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
                
                Text::make('Descrição', 'description')->hideOnIndex(),
            ])
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
