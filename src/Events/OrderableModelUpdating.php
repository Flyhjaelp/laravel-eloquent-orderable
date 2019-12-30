<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Events;

use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderableModelUpdating
{
    use Dispatchable, SerializesModels;

    /**
     * @var OrderableInterface
     */
    public $orderableModel;

    public function __construct(OrderableInterface $orderableModel)
    {
        $this->orderableModel = $orderableModel;
    }
}
