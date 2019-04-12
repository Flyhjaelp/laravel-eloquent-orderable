<?php


namespace Flyhjaelp\LaravelEloquentOrderable\Listeners;


use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelUpdating;
use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;

class UpdateOrderToFitUpdatingOrderableModel {

   /**
    * @var OrderableInterface
    */
   protected $orderableModel;

   public function handle(OrderableModelUpdating $event) {

      $this->orderableModel = $event->orderableModel;

      if($event->orderableModel->hasChangedOrderGroup()){
         $this->getAllFromOldGroupEqualOrHigherOrdered()->each->decreaseOrder();
      }

   }

   protected function getAllFromOldGroupEqualOrHigherOrdered() {
      $orderableReflection = new \ReflectionClass(get_class($this->orderableModel));
      $orderableCopyOfOriginalAttributes = $orderableReflection->newInstance();
      collect($this->orderableModel->getOriginal())->each(function($value,$key) use ($orderableCopyOfOriginalAttributes){
         $orderableCopyOfOriginalAttributes->$key = $value;
      });

      return $orderableCopyOfOriginalAttributes->getAllHigherOrEqualOrdered();
   }

}