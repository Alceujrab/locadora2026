<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AccountReceivable;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountReceivable;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
/**
 * @extends ModelResource<AccountReceivable, AccountReceivableIndexPage, AccountReceivableFormPage, AccountReceivableDetailPage>
 */
class AccountReceivableResource extends ModelResource
{
    protected string $model = AccountReceivable::class;
    protected string $title = 'AccountReceivables';
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
