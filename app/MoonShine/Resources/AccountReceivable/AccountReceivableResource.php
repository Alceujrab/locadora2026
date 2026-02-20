<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AccountReceivable;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountReceivable;
use App\MoonShine\Resources\AccountReceivable\Pages\AccountReceivableIndexPage;
use App\MoonShine\Resources\AccountReceivable\Pages\AccountReceivableFormPage;
use App\MoonShine\Resources\AccountReceivable\Pages\AccountReceivableDetailPage;

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
            AccountReceivableIndexPage::class,
            AccountReceivableFormPage::class,
            AccountReceivableDetailPage::class,
        ];
    }
}
