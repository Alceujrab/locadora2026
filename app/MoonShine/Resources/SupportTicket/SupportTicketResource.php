<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SupportTicket;

use Illuminate\Database\Eloquent\Model;
use App\Models\SupportTicket;
use App\MoonShine\Resources\SupportTicket\Pages\SupportTicketIndexPage;
use App\MoonShine\Resources\SupportTicket\Pages\SupportTicketFormPage;
use App\MoonShine\Resources\SupportTicket\Pages\SupportTicketDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Select;
use App\MoonShine\Resources\CustomerResource;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Components\Layout\Box;

/**
 * @extends ModelResource<SupportTicket, SupportTicketIndexPage, SupportTicketFormPage, SupportTicketDetailPage>
 */
class SupportTicketResource extends ModelResource
{
    protected string $model = SupportTicket::class;

    protected string $title = 'Chamados de Suporte';
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            SupportTicketIndexPage::class,
            SupportTicketFormPage::class,
            SupportTicketDetailPage::class,
        ];
    }

    public function query(): \Illuminate\Contracts\Database\Eloquent\Builder
    {
        $query = parent::query();
        return $query;
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class),
            Text::make('Assunto', 'subject'),
            Select::make('Prioridade', 'priority')
                ->options(['baixa' => 'Baixa', 'media' => 'Média', 'alta' => 'Alta', 'urgente' => 'Urgente']),
            Select::make('Status', 'status')
                ->options(['aberto' => 'Aberto', 'em_andamento' => 'Em Andamento', 'resolvido' => 'Resolvido', 'fechado' => 'Fechado']),
            Text::make('Criado em', 'created_at')
                ->format(fn($val) => $val ? $val->format('d/m/Y H:i') : '')
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Box::make('Abertura de Chamado', [
                ID::make(),
                BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class)
                    ->required(),
                    
                Select::make('Categoria', 'category')
                    ->options([
                        'duvida' => 'Dúvida', 
                        'financeiro' => 'Financeiro/Fatura', 
                        'manutencao' => 'Manutenção/Veículo', 
                        'emergencia' => 'Emergência/Acidente',
                        'outros' => 'Outros'
                    ])->required(),

                Text::make('Assunto', 'subject')->required(),
                Select::make('Prioridade', 'priority')
                    ->options(['baixa' => 'Baixa', 'media' => 'Média', 'alta' => 'Alta', 'urgente' => 'Urgente'])
                    ->required(),
                
                Textarea::make('Descrição', 'description')->required(),
                
                Select::make('Status', 'status')
                    ->options(['aberto' => 'Aberto', 'em_andamento' => 'Em Andamento', 'resolvido' => 'Resolvido', 'fechado' => 'Fechado'])
                    ->default('aberto')
            ])
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->formFields();
    }

    protected function beforeSave(mixed $item): mixed
    {
        return $item;
    }

    protected function rules(mixed $item): array
    {
        $rules = [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string'],
            'priority' => ['required', 'string'],
        ];

        $rules['customer_id'] = ['required', 'integer'];
        $rules['status'] = ['required', 'string'];

        return $rules;
    }
}
