<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Caution;

use Illuminate\Database\Eloquent\Model;
use App\Models\Caution;
use App\MoonShine\Resources\Caution\Pages\CautionIndexPage;
use App\MoonShine\Resources\Caution\Pages\CautionFormPage;
use App\MoonShine\Resources\Caution\Pages\CautionDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use Illuminate\Http\Request;
use MoonShine\Laravel\MoonShineUI;
use App\Models\AccountReceivable;

/**
 * @extends ModelResource<Caution, CautionIndexPage, CautionFormPage, CautionDetailPage>
 */
class CautionResource extends ModelResource
{
    protected string $model = Caution::class;

    protected string $title = 'Cauções';

    protected string $column = 'contract_id';

    public function search(): array
    {
        return ['contract_id', 'customer.name'];
    }
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            CautionIndexPage::class,
            CautionFormPage::class,
            CautionDetailPage::class,
        ];
    }

    protected function routes(): void
    {
        $this->addRoute('release', 'release/{resourceItem}', function (Caution $resourceItem) {
            $resourceItem->update([
                'status' => 'liberada',
                'released_at' => now(),
            ]);

            MoonShineUI::toast('Caução liberada com sucesso!', 'success');
            return back();
        });

        $this->addRoute('charge', 'charge/{resourceItem}', function (Caution $resourceItem, Request $request) {
            $amount = (float) $request->input('charged_amount', 0);
            $reason = $request->input('charge_reason');

            if ($amount <= 0) {
                MoonShineUI::toast('O valor cobrado deve ser maior que zero.', 'error');
                return back();
            }

            if ($amount > $resourceItem->amount) {
                MoonShineUI::toast('O valor não pode ser superior à caução retida.', 'error');
                return back();
            }

            $resourceItem->update([
                'status' => $amount == $resourceItem->amount ? 'cobrada_total' : 'cobrada_parcial',
                'charged_amount' => $amount,
                'charge_reason' => $reason,
            ]);

            // Create an automatic Account Receivable for the charged caution
            AccountReceivable::create([
                'customer_id' => $resourceItem->customer_id,
                'description' => "Cobrança de Caução - Contrato {$resourceItem->contract_id}: {$reason}",
                'amount' => $amount,
                'due_date' => now(),
                'received_at' => now(), // already "received" since it was a deposit or preauth capture
                'status' => 'pago',
                'notes' => 'Gerado automaticamente via registro de cobrança da Caução #' . $resourceItem->id,
            ]);

            MoonShineUI::toast('Cobrança registrada e lançada no Contas a Receber.', 'success');
            return back();
        });
    }
}
