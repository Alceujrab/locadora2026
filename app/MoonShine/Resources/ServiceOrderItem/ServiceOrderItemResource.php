<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ServiceOrderItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceOrderItem;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
/**
 * @extends ModelResource<ServiceOrderItem, ServiceOrderItemIndexPage, ServiceOrderItemFormPage, ServiceOrderItemDetailPage>
 */
class ServiceOrderItemResource extends ModelResource
{
    protected string $model = ServiceOrderItem::class;
    protected string $title = 'Itens de ServiÃ§o';
    // Oculta do menu esquerdo (sÃ³ acessÃ­vel por dentro da OS Pai)
    protected bool $isDisplayInMenu = false;
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }
}
