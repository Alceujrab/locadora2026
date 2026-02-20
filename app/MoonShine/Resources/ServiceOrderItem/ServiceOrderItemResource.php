<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ServiceOrderItem;

use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceOrderItem;
use App\MoonShine\Resources\ServiceOrderItem\Pages\ServiceOrderItemIndexPage;
use App\MoonShine\Resources\ServiceOrderItem\Pages\ServiceOrderItemFormPage;
use App\MoonShine\Resources\ServiceOrderItem\Pages\ServiceOrderItemDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<ServiceOrderItem, ServiceOrderItemIndexPage, ServiceOrderItemFormPage, ServiceOrderItemDetailPage>
 */
class ServiceOrderItemResource extends ModelResource
{
    protected string $model = ServiceOrderItem::class;

    protected string $title = 'Itens de Serviço';
    
    // Oculta do menu esquerdo (só acessível por dentro da OS Pai)
    protected bool $isDisplayInMenu = false;
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ServiceOrderItemIndexPage::class,
            ServiceOrderItemFormPage::class,
            ServiceOrderItemDetailPage::class,
        ];
    }
}
