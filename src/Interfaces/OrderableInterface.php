<?php


namespace Flyhjaelp\LaravelEloquentOrderable\Interfaces;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

Interface OrderableInterface {

   public static function fireEventIfNotUpdatingOrderAlready(string $eventClassName, OrderableInterface $orderableModel): void;

   public function getOrderableColumn(): string;
   public function getCurrentOrderValue(): ?int;
   public function getOriginalOrderValue(): ?int;
   public function setOrderValue(int $value): void;

   public function scopeOrdered(Builder $query): void;

   public function setIsUpdatingOrderToTrue(): void;
   public function setIsUpdatingOrderToFalse(): void;
   public function isUpdatingOrder(): bool;

   public function getLastOrder(): int;
   public function getAllHigherOrEqualOrdered(): Collection;
   public function getAllOrderedBetweenWithoutSelf(int $minOrder, int $maxOrder): Collection;

   public function increaseOrder(): void;
   public function decreaseOrder(): void;

   public function hasOrderGroup(): bool;
   public function hasChangedOrderGroup(): bool;

}