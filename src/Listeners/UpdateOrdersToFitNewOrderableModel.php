<?php


namespace Flyhjaelp\LaravelEloquentOrderable\Listeners;


use Flyhjaelp\LaravelEloquentOrderable\Events\OrderableModelCreating;

class UpdateOrdersToFitNewOrderableModel {

   public function handle(OrderableModelCreating $event) {

      if($event->orderableModel->getCurrentOrderValue() === null){
         $event->orderableModel->setOrderValue($event->orderableModel->getLastOrder() + 1);
      }else{
         $event->orderableModel->getAllHigherOrEqualOrdered()->each->increaseOrder();
      }

   }
}