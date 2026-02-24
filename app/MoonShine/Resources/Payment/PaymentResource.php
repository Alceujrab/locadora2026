<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Payment;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\Contracts\Core\PageContract;
/**
 * @extends ModelResource<Payment, PaymentIndexPage, PaymentFormPage, PaymentDetailPage>
 */
class PaymentResource extends ModelResource
{
    protected string $model = Payment::class;
    protected ?PageType $redirectAfterSave = PageType::INDEX;
    protected string $title = 'Payments';
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
