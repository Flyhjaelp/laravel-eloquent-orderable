<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;

class OrderableModelDeleted
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
