<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MoonShineUserRole;
use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;
/**
 * @extends ModelResource<MoonshineUserRole, MoonShineUserRoleIndexPage, MoonShineUserRoleFormPage, null>
 */
#[Icon('bookmark')]
#[Group('moonshine::ui.resource.system', 'users', translatable: true)]
#[Order(1)]
class MoonShineUserRoleResource extends ModelResource
{
    protected string $model = MoonshineUserRole::class;
    protected ?PageType $redirectAfterSave = PageType::INDEX;
    protected string $column = 'name';
    protected bool $createInModal = true;
    protected bool $detailInModal = true;
    protected bool $editInModal = true;
    protected bool $cursorPaginate = true;
    public function getTitle(): string
    {
        return __('moonshine::ui.resource.role');
    }
    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }
    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
        ];
    }
    protected function search(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
