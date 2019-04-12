<?php


namespace Flyhjaelp\LaravelEloquentOrderable\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait Orderable {

   use SharedOrderableMethods;

   /**
    * Static methods
    */
   public static function bootOrderable(): void {
      static::addDefaultOrderingEvents();
      static::addGlobalOrderingScope();
   }

   /**
    * Method Scopes
    */
   public function scopeOrdered(Builder $query): void{
      $orderColumn = $this->getOrderableColumn();
      $query->orderBy($orderColumn);
   }

   /**
    * Methods to get order information
    */
   public function getLastOrder(): int {
      $orderColumn = $this->getOrderableColumn();
      return optional(static::all()->last())->$orderColumn ?? 0;
   }

   /**
    * Methods to return collections within certains order
    */
   public function getAllHigherOrEqualOrdered(): Collection {
      $orderColumn = $this->getOrderableColumn();
      return static::where($orderColumn,'>=',$this->$orderColumn)->where('id','!=',$this->id)->get();
   }

   public function getAllOrderedBetweenWithoutSelf(int $minOrder, int $maxOrder): Collection {
      $orderColumn = $this->getOrderableColumn();
      return static::whereBetween($orderColumn,[$minOrder,$maxOrder])->where('id','!=',$this->id)->get();
   }

}