<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Nfse;

use App\Models\Nfse;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\SortDirection;

#[Icon('document-check')]
class NfseResource extends ModelResource
{
    protected string $model = Nfse::class;

    protected string $title = 'Notas Fiscais (NFS-e)';

    protected string $column = 'numero';

    protected SortDirection $sortDirection = SortDirection::DESC;

    public function getActiveActions(): array
    {
        return ['create', 'view', 'update', 'delete', 'massDelete', 'export'];
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Número', 'numero')
                ->sortable(),

            BelongsTo::make('Fatura', 'invoice', fn($item) => $item->invoice_number ?? 'N/A')
                ->sortable(),

            Text::make('Tomador', 'tomador_nome')
                ->sortable(),

            Text::make('CPF/CNPJ', 'tomador_cnpj_cpf'),

            Number::make('Valor Serviço (R$)', 'valor_servico')
                ->sortable(),

            Number::make('ISS (R$)', 'valor_iss'),

            Date::make('Emissão', 'data_emissao')
                ->format('d/m/Y')
                ->sortable(),

            Select::make('Status', 'status')
                ->options([
                    'rascunho' => 'Rascunho',
                    'emitida' => 'Emitida',
                    'cancelada' => 'Cancelada',
                ])
                ->badge(fn(string $value) => match($value) {
                    'rascunho' => 'warning',
                    'emitida' => 'success',
                    'cancelada' => 'error',
                    default => 'secondary',
                }),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            ID::make(),

            BelongsTo::make('Fatura Vinculada', 'invoice', fn($item) => $item->invoice_number ?? '#' . $item->id)
                ->nullable()
                ->searchable(),

            Text::make('Número NFS-e', 'numero')
                ->hint('Deixe vazio para auto-gerar'),

            Text::make('Série', 'serie')
                ->default('A1'),

            Date::make('Data de Emissão', 'data_emissao')
                ->required(),

            Text::make('Código do Serviço (CNAE)', 'codigo_servico')
                ->hint('Ex: 7020'),

            Textarea::make('Discriminação do Serviço', 'discriminacao')
                ->required(),

            Number::make('Valor do Serviço (R$)', 'valor_servico')
                ->min(0)
                ->step(0.01)
                ->required(),

            Number::make('Alíquota ISS (%)', 'aliquota_iss')
                ->min(0)
                ->step(0.01)
                ->default(5.00),

            Number::make('Valor ISS (R$)', 'valor_iss')
                ->min(0)
                ->step(0.01)
                ->hint('Calculado automaticamente se vazio'),

            Text::make('CPF/CNPJ do Tomador', 'tomador_cnpj_cpf')
                ->required(),

            Text::make('Nome do Tomador', 'tomador_nome')
                ->required(),

            Text::make('Endereço do Tomador', 'tomador_endereco'),

            Text::make('E-mail do Tomador', 'tomador_email'),

            Select::make('Status', 'status')
                ->options([
                    'rascunho' => 'Rascunho',
                    'emitida' => 'Emitida',
                    'cancelada' => 'Cancelada',
                ])
                ->default('rascunho')
                ->required(),

            Textarea::make('Observações', 'observacoes'),
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->formFields();
    }

    protected function filters(): iterable
    {
        return [
            Text::make('Número', 'numero'),

            Select::make('Status', 'status')
                ->options([
                    'rascunho' => 'Rascunho',
                    'emitida' => 'Emitida',
                    'cancelada' => 'Cancelada',
                ])
                ->nullable(),

            Date::make('Emissão De', 'data_emissao')
                ->nullable(),
        ];
    }
}
