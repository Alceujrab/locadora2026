<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\InspectionItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\InspectionItem;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Image;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
/**
 * @extends ModelResource<InspectionItem, InspectionItemIndexPage, InspectionItemFormPage, InspectionItemDetailPage>
 */
class InspectionItemResource extends ModelResource
{
    protected string $model = InspectionItem::class;
    protected string $title = 'InspectionItems';
    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Categoria', 'category')->sortable(),
            Text::make('Nome do Item', 'item_name'),
            Text::make('Condição', 'condition'),
            Number::make('Valor Dano Base (R$)', 'damage_value')->sortable(),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Box::make([
                ID::make(),
                Text::make('Categoria', 'category')->required()->hint('Ex: Exterior, Interior, Mecânica'),
                Text::make('Nome do Item', 'item_name')->required()->hint('Ex: Para-choque Dianteiro, Rádio'),
                Text::make('Condição Default', 'condition')->required()->hint('Ex: Bom, Regular, Ruim, Danificado'),
                Textarea::make('Descrição do Dano Padrão', 'damage_description'),
                Number::make('Valor Dano Base (R$)', 'damage_value')->step(0.01)->min(0),
                Image::make('Fotos de Exemplo', 'photos')->multiple()->removable()->dir('inspections'),
            ])
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->formFields();
    }
}
