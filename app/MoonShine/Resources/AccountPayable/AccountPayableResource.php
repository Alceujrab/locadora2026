<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AccountPayable;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountPayable;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
/**
 * @extends ModelResource<AccountPayable, AccountPayableIndexPage, AccountPayableFormPage, AccountPayableDetailPage>
 */
class AccountPayableResource extends ModelResource
{
    protected string $model = AccountPayable::class;
    protected string $title = 'AccountPayables';
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
