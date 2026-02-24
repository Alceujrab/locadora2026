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
    public function fields(): array
    {
        return [
            ID::make(),
            Text::make('Categoria', 'category')->required()->hint('Ex: Exterior, Interior, MecÃ¢nica'),
            Text::make('Nome do Item', 'item_name')->required()->hint('Ex: Para-choque Dianteiro, RÃ¡dio'),
            Text::make('CondiÃ§Ã£o', 'condition')->required()->hint('Ex: Bom, Regular, Ruim, Danificado'),
            Textarea::make('DescriÃ§Ã£o do Dano', 'damage_description'),
            Number::make('Valor Dano (R$)', 'damage_value')->step(0.01)->min(0),
            Image::make('Fotos', 'photos')->multiple()->removable()->dir('inspections'),
        ];
    }
}
