<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use App\MoonShine\Resources\Payment\Pages\PaymentIndexPage;
use App\MoonShine\Resources\Payment\Pages\PaymentFormPage;
use App\MoonShine\Resources\Payment\Pages\PaymentDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Payment, PaymentIndexPage, PaymentFormPage, PaymentDetailPage>
 */
class PaymentResource extends ModelResource
{
    protected string $model = Payment::class;

    protected string $title = 'Payments';
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            PaymentIndexPage::class,
            PaymentFormPage::class,
            PaymentDetailPage::class,
        ];
    }
}
