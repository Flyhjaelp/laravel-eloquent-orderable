<?php


namespace Flyhjaelp\LaravelEloquentOrderable\Traits;


use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelCreating;
use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelDeleted;
use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelUpdated;
use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;
use Illuminate\Database\Eloquent\Builder;

trait SharedOrderableMethods {

   protected static $isUpdatingOrder = false;

   public static function addDefaultOrderingEvents() {
      static::creating(function (OrderableInterface $orderableModel){
         static::fireEventIfNotUpdatingOrderAlready(OrderableModelCreating::class,$orderableModel);
      });

      static::deleted(function (OrderableInterface $orderableModel){
         static::fireEventIfNotUpdatingOrderAlready(OrderableModelDeleted::class,$orderableModel);
      });

      static::updated(function (OrderableInterface $orderableModel){
         static::fireEventIfNotUpdatingOrderAlready(OrderableModelUpdated::class,$orderableModel);
      });
   }

   public static function addGlobalOrderingScope() {
      static::addGlobalScope('order', function (Builder $builder) {
         $builder->ordered();
      });
   }

   public static function fireEventIfNotUpdatingOrderAlready(string $eventClassName, OrderableInterface $orderableModel): void {
      if(!$orderableModel->isUpdatingOrder()){
         $orderableModel->setIsUpdatingOrderToTrue();
         event(new $eventClassName($orderableModel));
         $orderableModel->setIsUpdatingOrderToFalse();
      }
   }

   /**
    * methods related to isUpdatingOrder
    */
   public function setIsUpdatingOrderToTrue(): void {
      static::$isUpdatingOrder = true;
   }

   public function setIsUpdatingOrderToFalse(): void {
      static::$isUpdatingOrder = false;
   }

   public function isUpdatingOrder(): bool{
      return static::$isUpdatingOrder;
   }

   /**
    * Methods Default value Getters
    */
   public function getOrderableColumn(): string {
      return 'order';
   }

   public function getCurrentOrderValue(): ?int {
      $orderColumn = $this->getOrderableColumn();
      return $this->$orderColumn;
   }

   public function getOriginalOrderValue(): ?int {
      $orderColumn = $this->getOrderableColumn();
      return $this->getOriginal()[$orderColumn];;
   }

   public function setOrderValue(int $value): void {
      $orderColumn = $this->getOrderableColumn();
      $this->$orderColumn = $value;
   }

   public function hasOrderGroup(): bool {
      return method_exists($this,'scopeWithinOrderGroup');
   }

   public function hasChangedOrderGroup(): bool {
      if(!$this->hasOrderGroup()){
         return false;
      }

      foreach($this->columnsAffectingOrderGroup() as $column){
            if($this->$column != $this->getOriginal()[$column]){
               return true;
            }
      }

      return false;

   }

   /**
    * Methods to update orders
    */
   public function increaseOrder(): void {
      $orderColumn = $this->getOrderableColumn();
      $this->$orderColumn += 1;
      $this->save();
   }

   public function decreaseOrder(): void {
      $orderColumn = $this->getOrderableColumn();
      $this->$orderColumn -= 1;
      $this->save();
   }

}