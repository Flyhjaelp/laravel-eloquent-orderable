<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Traits;

use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelUpdating;
use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait OrderableWithinGroup
{
    use SharedOrderableMethods;

    public static function bootOrderableWithinGroup(): void
    {
        static::addDefaultOrderingEvents();

        static::updating(function (OrderableInterface $orderableModel) {
            static::fireEventIfNotUpdatingOrderAlready(OrderableModelUpdating::class, $orderableModel);
        });

        static::addGlobalOrderingScope();
    }

    abstract public function scopeWithinOrderGroup(Builder $query, OrderableInterface $orderableModel): void;

    abstract public function columnsAffectingOrderGroup(): \Illuminate\Support\Collection;

    public function getLastOrder(): int
    {
        $orderColumn = $this->getOrderableColumn();

        return optional(static::withinOrderGroup($this)->get()->last())->$orderColumn ?? 0;
    }

    public function scopeNotSelf(Builder $query, OrderableInterface $orderableModel): void
    {
        $query->where('id', '!=', $orderableModel->id);
    }

    /**
     * Methods to return collections within certains order.
     */
    public function getAllHigherOrEqualOrdered(): Collection
    {
        $orderColumn = $this->getOrderableColumn();

        return static::withinOrderGroup($this)->where($orderColumn, '>=', $this->$orderColumn)->notSelf($this)->get();
    }

    public function getAllOrderedBetweenWithoutSelf(int $minOrder, int $maxOrder): Collection
    {
        $orderColumn = $this->getOrderableColumn();

        return static::withinOrderGroup($this)->whereBetween($orderColumn, [$minOrder, $maxOrder])->notSelf($this)->get();
    }
}
