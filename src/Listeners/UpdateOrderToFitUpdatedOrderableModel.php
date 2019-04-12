<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Listeners;

use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelUpdated;
use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;

class UpdateOrderToFitUpdatedOrderableModel
{
    public $orderColumn;
    public $originalOrderValue;
    public $newOrderValue;

    /**
     * @var OrderableInterface
     */
    public $orderableModel;

    public function handle(OrderableModelUpdated $event)
    {
        $this->originalOrderValue = $event->orderableModel->getOriginalOrderValue();
        $this->newOrderValue = $event->orderableModel->getCurrentOrderValue();
        $this->orderableModel = $event->orderableModel;

        if ($this->orderableModel->hasChangedOrderGroup()) {
            $this->updateOrderInNewGroup();
        } else {
            $this->updateOrderWithinGroup();
        }
    }

    protected function updateOrderInNewGroup()
    {
        $this->orderableModel->getAllHigherOrEqualOrdered()->each->increaseOrder();
    }

    protected function updateOrderWithinGroup()
    {
        if ($this->originalOrderValue > $this->newOrderValue) {
            $this->orderableModel->getAllOrderedBetweenWithoutSelf($this->newOrderValue, $this->originalOrderValue)->each->increaseOrder();
        }

        if ($this->originalOrderValue < $this->newOrderValue) {
            $this->orderableModel->getAllOrderedBetweenWithoutSelf($this->originalOrderValue, $this->newOrderValue)->each->decreaseOrder();
        }
    }
}
