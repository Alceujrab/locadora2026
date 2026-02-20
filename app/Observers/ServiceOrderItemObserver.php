<?php

namespace App\Observers;

use App\Models\ServiceOrderItem;

class ServiceOrderItemObserver
{
    /**
     * Handle the ServiceOrderItem "created" event.
     */
    public function created(ServiceOrderItem $serviceOrderItem): void
    {
        if ($serviceOrderItem->serviceOrder) {
            $serviceOrderItem->serviceOrder->recalculateTotal();
        }
    }

    /**
     * Handle the ServiceOrderItem "updated" event.
     */
    public function updated(ServiceOrderItem $serviceOrderItem): void
    {
        if ($serviceOrderItem->serviceOrder) {
            $serviceOrderItem->serviceOrder->recalculateTotal();
        }
    }

    /**
     * Handle the ServiceOrderItem "deleted" event.
     */
    public function deleted(ServiceOrderItem $serviceOrderItem): void
    {
        if ($serviceOrderItem->serviceOrder) {
            $serviceOrderItem->serviceOrder->recalculateTotal();
        }
    }

    /**
     * Handle the ServiceOrderItem "restored" event.
     */
    public function restored(ServiceOrderItem $serviceOrderItem): void
    {
        //
    }

    /**
     * Handle the ServiceOrderItem "force deleted" event.
     */
    public function forceDeleted(ServiceOrderItem $serviceOrderItem): void
    {
        //
    }
}
