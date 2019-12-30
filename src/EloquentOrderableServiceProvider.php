<?php

namespace Flyhjaelp\LaravelEloquentOrderable;

use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelCreating;
use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelDeleted;
use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelUpdated;
use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelUpdating;
use Flyhjaelp\LaravelEloquentOrderable\Listeners\UpdateOrdersToFitDeletedModel;
use Flyhjaelp\LaravelEloquentOrderable\Listeners\UpdateOrdersToFitNewOrderableModel;
use Flyhjaelp\LaravelEloquentOrderable\Listeners\UpdateOrderToFitUpdatedOrderableModel;
use Flyhjaelp\LaravelEloquentOrderable\Listeners\UpdateOrderToFitUpdatingOrderableModel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EloquentOrderableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(OrderableModelCreating::class, UpdateOrdersToFitNewOrderableModel::class);
        Event::listen(OrderableModelDeleted::class, UpdateOrdersToFitDeletedModel::class);
        Event::listen(OrderableModelUpdating::class, UpdateOrderToFitUpdatingOrderableModel::class);
        Event::listen(OrderableModelUpdated::class, UpdateOrderToFitUpdatedOrderableModel::class);
    }

    public function register(): void
    {
    }
}
