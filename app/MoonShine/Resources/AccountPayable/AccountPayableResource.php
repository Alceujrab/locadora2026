<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AccountPayable;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountPayable;
use App\MoonShine\Resources\AccountPayable\Pages\AccountPayableIndexPage;
use App\MoonShine\Resources\AccountPayable\Pages\AccountPayableFormPage;
use App\MoonShine\Resources\AccountPayable\Pages\AccountPayableDetailPage;

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
            AccountPayableIndexPage::class,
            AccountPayableFormPage::class,
            AccountPayableDetailPage::class,
        ];
    }
}
