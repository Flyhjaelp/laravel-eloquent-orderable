<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Traits;

use Exception;
use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelCreating;
use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelDeleted;
use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelUpdated;
use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelUpdating;
use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;

trait PivotOrderable
{
    use OrderableWithinGroup;

    /**
     * @var Collection;
     */
    protected static $originalOrderValues;

    public static function bootOrderableWithinGroup(): void
    {
        static::setOriginalOrderValues();

        static::creating(function (OrderableInterface $orderableModel) {
            static::fireEventIfNotUpdatingOrderAlready(OrderableModelCreating::class, $orderableModel);
        });

        static::updating(function (OrderableInterface $orderableModel) {
            $orderableModel->fillWithDataFromDb();
            static::fireEventIfNotUpdatingOrderAlready(OrderableModelUpdating::class, $orderableModel);
        });

        static::updated(function (OrderableInterface $orderableModel) {
            $orderableModel->fillWithDataFromDb();
            static::fireEventIfNotUpdatingOrderAlready(OrderableModelUpdated::class, $orderableModel);
        });

        static::deleting(function (OrderableInterface $orderableModel) {
            $orderableModel->fillWithDataFromDb();
        });

        static::deleted(function (OrderableInterface $orderableModel) {
            static::fireEventIfNotUpdatingOrderAlready(OrderableModelDeleted::class, $orderableModel);
        });

        static::addGlobalOrderingScope();
    }

    protected static function setOriginalOrderValues()
    {
        if (static::$originalOrderValues === null) {
            static::$originalOrderValues = collect();
        }
    }

    public function getOriginalOrderValue(): ?int
    {
        if (static::$originalOrderValues->has($this->getOriginalOrderValueKey())) {
            return static::$originalOrderValues[$this->getOriginalOrderValueKey()];
        }

        return null;
    }

    protected function getOriginalOrderValueKey(): string
    {
        $keyName = $this->getKeyName();
        if ($this->canBeFoundViaPrimaryKey()) {
            return 'I'.$this->$keyName;
        }

        throw new Exception('Can\'t get original order value key as both the "Foreign key" and "Primary key" aren\'t set');
    }

    protected function canBeFoundViaForeignKeys(): bool
    {
        $foreignKey = $this->getForeignKey();
        $relatedKey = $this->getRelatedKey();

        return $foreignKey !== null && $relatedKey !== null;
    }

    protected function canBeFoundViaPrimaryKey(): bool
    {
        $keyName = $this->getKeyName();

        return $this->$keyName !== null;
    }

    protected function fillWithDataFromDb(): void
    {
        $newData = $this->toArray();

        if ($this->canBeFoundViaForeignKeys()) {
            $oldData = $this->findModelViaForeignKeys()->toArray();
        } elseif ($this->canBeFoundViaPrimaryKey()) {
            $oldData = $this->findModelViaPrimaryKey()->toArray();
        } else {
            throw new Exception('Can\'t fill model as both the "Foreign key" and "Primary key" aren\'t set');
        }

        $this->fill($oldData);
        if (! static::$originalOrderValues->has($this->getOriginalOrderValueKey())) {
            static::$originalOrderValues[$this->getOriginalOrderValueKey()] = $oldData[$this->getOrderableColumn()];
        }
        $this->fill($newData);
    }

    protected function findModelViaForeignKeys(): Pivot
    {
        $relatedKey = $this->getRelatedKey();
        $foreignKey = $this->getForeignKey();

        return $this
         ->where($relatedKey, $this->$relatedKey)
         ->where($foreignKey, $this->$foreignKey)
         ->firstOrFail();
    }

    protected function findModelViaPrimaryKey(): Pivot
    {
        $primaryKey = $this->getKeyName();

        return $this
         ->where($primaryKey, $this->$primaryKey)
         ->firstOrFail();
    }
}
