<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\CustomerDocument;

use Illuminate\Database\Eloquent\Model;
use App\Models\CustomerDocument;
use App\MoonShine\Resources\CustomerDocument\Pages\CustomerDocumentIndexPage;
use App\MoonShine\Resources\CustomerDocument\Pages\CustomerDocumentFormPage;
use App\MoonShine\Resources\CustomerDocument\Pages\CustomerDocumentDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<CustomerDocument, CustomerDocumentIndexPage, CustomerDocumentFormPage, CustomerDocumentDetailPage>
 */
class CustomerDocumentResource extends ModelResource
{
    protected string $model = CustomerDocument::class;

    protected string $title = 'Documentos do Cliente';

    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Text::make('Tipo', 'type'),
            \MoonShine\UI\Fields\File::make('Arquivo', 'file_path')->dir('customers/documents'),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Cliente', 'customer', resource: \App\MoonShine\Resources\CustomerResource::class)->hideOnForm(),
            \MoonShine\UI\Fields\Select::make('Tipo', 'type')->options([
                'cnh' => 'CNH',
                'rg' => 'RG',
                'cpf' => 'CPF',
                'comprovante_residencia' => 'Comprovante de Residência',
                'contrato_social' => 'Contrato Social',
                'outros' => 'Outros',
            ])->required(),
            \MoonShine\UI\Fields\File::make('Arquivo ou Foto', 'file_path')->dir('customers/documents')->removable()->required(),
            \MoonShine\UI\Fields\Text::make('Nome Original', 'original_name'),
            \MoonShine\UI\Fields\Textarea::make('Observações', 'notes'),
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->formFields();
    }
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            CustomerDocumentIndexPage::class,
            CustomerDocumentFormPage::class,
            CustomerDocumentDetailPage::class,
        ];
    }
}
