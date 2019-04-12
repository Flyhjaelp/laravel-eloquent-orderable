<?php


namespace Flyhjaelp\LaravelEloquentOrderable\Tests;


use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;
use Flyhjaelp\LaravelEloquentOrderable\Traits\Orderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderableNonDefaultColumnTest extends DefaultTestCase {

   use RefreshDatabase;

   /** @test */
   public function a_model_with_the_orderable_trait_and_overwritten_order_column_is_by_default_ordered_by_order(){

      $orderableModelA = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelB = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelC = factory(OrderableTestModelWithOtherOrderColumn::class)->create(['non_default_order_column' => 2]);

      $modelsFromDB = OrderableTestModelWithOtherOrderColumn::all();

      $this->assertEquals($orderableModelA->id,$modelsFromDB->shift()->id);
      $this->assertEquals($orderableModelC->id,$modelsFromDB->shift()->id);
      $this->assertEquals($orderableModelB->id,$modelsFromDB->shift()->id);
   }

   /** @test */
   public function when_a_model_with_the_orderable_trait_and_overwritten_order_column_is_created_if_no_specific_order_is_given_its_added_as_the_last_model(){

      $orderableModelA = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelB = factory(OrderableTestModelWithOtherOrderColumn::class)->create();

      $this->assertEquals(1,$orderableModelA->non_default_order_column);
      $this->assertEquals(2,$orderableModelB->non_default_order_column);

   }

   /** @test */
   public function when_a_model_with_the_orderable_trait_and_overwritten_order_column_is_created_with_a_specified_order_other_models_with_the_same_order_or_higher_increases_their_order_by_1(){

      $orderableModelA = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelB = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelC = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelD = factory(OrderableTestModelWithOtherOrderColumn::class)->create(['non_default_order_column' => 2]);

      $this->assertEquals(1,$orderableModelA->fresh()->non_default_order_column);
      $this->assertEquals(3,$orderableModelB->fresh()->non_default_order_column);
      $this->assertEquals(4,$orderableModelC->fresh()->non_default_order_column);
      $this->assertEquals(2,$orderableModelD->fresh()->non_default_order_column);

   }

   /** @test */
   public function when_a_model_with_the_orderable_trait_and_overwritten_order_column_is_deleted_other_models_the_same_order_or_higher_get_their_order_decreased_with_one(){

      $orderableModelA = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelB = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelC = factory(OrderableTestModelWithOtherOrderColumn::class)->create();

      $orderableModelB->delete();

      $this->assertEquals(1,$orderableModelA->fresh()->non_default_order_column);
      $this->assertEquals(2,$orderableModelC->fresh()->non_default_order_column);
   }

   /** @test */
   public function when_a_model_with_the_orderable_trait_and_overwritten_order_column_has_its_order_column_updated_to_a_lower_order_the_other_models_update_their_orders_accordingly(){

      $orderableModelA = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelB = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelC = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelD = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelE = factory(OrderableTestModelWithOtherOrderColumn::class)->create();

      $orderableModelD->non_default_order_column = 2;
      $orderableModelD->save();

      $this->assertEquals(1,$orderableModelA->fresh()->non_default_order_column);
      $this->assertEquals(3,$orderableModelB->fresh()->non_default_order_column);
      $this->assertEquals(4,$orderableModelC->fresh()->non_default_order_column);
      $this->assertEquals(2,$orderableModelD->fresh()->non_default_order_column);
      $this->assertEquals(5,$orderableModelE->fresh()->non_default_order_column);

   }

   /** @test */
   public function when_a_model_with_the_orderable_trait_and_overwritten_order_column_has_its_order_column_updated_to_a_hgiher_order_the_other_models_update_their_orders_accordingly(){

      $orderableModelA = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelB = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelC = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelD = factory(OrderableTestModelWithOtherOrderColumn::class)->create();
      $orderableModelE = factory(OrderableTestModelWithOtherOrderColumn::class)->create();

      $orderableModelB->non_default_order_column = 4;
      $orderableModelB->save();

      $this->assertEquals(1,$orderableModelA->fresh()->non_default_order_column);
      $this->assertEquals(4,$orderableModelB->fresh()->non_default_order_column);
      $this->assertEquals(2,$orderableModelC->fresh()->non_default_order_column);
      $this->assertEquals(3,$orderableModelD->fresh()->non_default_order_column);
      $this->assertEquals(5,$orderableModelE->fresh()->non_default_order_column);

   }
}

class OrderableTestModelWithOtherOrderColumn extends Model implements OrderableInterface {

   use Orderable;

   public function getOrderableColumn(): string {
      return 'non_default_order_column';
   }

}