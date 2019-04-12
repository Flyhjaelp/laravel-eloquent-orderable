<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Listeners;

use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelDeleted;

class UpdateOrdersToFitDeletedModel
{
    public function handle(OrderableModelDeleted $event)
    {
        $event->orderableModel->getAllHigherOrEqualOrdered()->each->decreaseOrder();
    }
}
