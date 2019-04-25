<?php


namespace Flyhjaelp\LaravelEloquentOrderable\Tests;


use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;
use Flyhjaelp\LaravelEloquentOrderable\Traits\OrderableWithinGroup;
use Flyhjaelp\LaravelEloquentOrderable\Traits\PivotOrderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class OrderablePivotTest extends DefaultTestCase {

   use RefreshDatabase;

   /** @test */
   public function when_a_pivot_with_the_orderable_trait_is_created_if_no_specific_order_is_given_its_added_as_the_last_model_within_its_group()
   {
      $primaryA = factory(PrimaryTestPivotModel::class)->create();
      $primaryB = factory(PrimaryTestPivotModel::class)->create();

      $secondaryA = factory(SecondaryTestPivotModel::class)->create();
      $secondaryB = factory(SecondaryTestPivotModel::class)->create();
      $secondaryC = factory(SecondaryTestPivotModel::class)->create();

      $primaryA->secondaries()->attach($secondaryC);
      $primaryA->secondaries()->attach($secondaryB);

      $primaryB->secondaries()->attach($secondaryA);
      $primaryB->secondaries()->attach($secondaryB);

      $this->assertEquals($secondaryC->id, $primaryA->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryA->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryB->id, $primaryA->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryA->secondaries[1]->pivot->order);

      $this->assertEquals($secondaryA->id, $primaryB->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryB->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryB->id, $primaryB->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryB->secondaries[1]->pivot->order);
   }

   /** @test */
   public function when_a_pivot_with_orderable_trait_is_created_with_a_specific_order_other_pivots_with_the_order_or_higher_get_theirs_increased_with_plus_1(){
      $primaryA = factory(PrimaryTestPivotModel::class)->create();
      $primaryB = factory(PrimaryTestPivotModel::class)->create();

      $secondaryA = factory(SecondaryTestPivotModel::class)->create();
      $secondaryB = factory(SecondaryTestPivotModel::class)->create();
      $secondaryC = factory(SecondaryTestPivotModel::class)->create();

      $primaryA->secondaries()->attach($secondaryC);
      $primaryA->secondaries()->attach($secondaryB);
      $primaryA->secondaries()->attach($secondaryA->id,['order' => 2]);

      $primaryB->secondaries()->attach($secondaryA);
      $primaryB->secondaries()->attach($secondaryB);

      $this->assertEquals($secondaryC->id, $primaryA->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryA->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryA->id, $primaryA->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryA->secondaries[1]->pivot->order);
      $this->assertEquals($secondaryB->id, $primaryA->secondaries[2]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(3, $primaryA->secondaries[2]->pivot->order);

      $this->assertEquals($secondaryA->id, $primaryB->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryB->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryB->id, $primaryB->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryB->secondaries[1]->pivot->order);

   }

   /** @test */
   public function when_a_pivot_model_with_the_orderable_trait_is_deleted_other_pivots_with_the_same_order_or_higher_get_their_order_decreased_with_1() {
      $primaryA = factory(PrimaryTestPivotModel::class)->create();
      $primaryB = factory(PrimaryTestPivotModel::class)->create();

      $secondaryA = factory(SecondaryTestPivotModel::class)->create();
      $secondaryB = factory(SecondaryTestPivotModel::class)->create();
      $secondaryC = factory(SecondaryTestPivotModel::class)->create();

      $primaryA->secondaries()->attach($secondaryA);
      $primaryA->secondaries()->attach($secondaryB);
      $primaryA->secondaries()->attach($secondaryC);

      $primaryB->secondaries()->attach($secondaryA);
      $primaryB->secondaries()->attach($secondaryB);

      $primaryA->secondaries()->detach($secondaryB);

      $this->assertEquals($secondaryA->id, $primaryA->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryA->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryC->id, $primaryA->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryA->secondaries[1]->pivot->order);

      $this->assertEquals($secondaryA->id, $primaryB->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryB->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryB->id, $primaryB->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryB->secondaries[1]->pivot->order);
   }

   /** @test */
   public function when_a_pivot_model_with_the_orderable_trait_has_its_order_column_updated_to_a_lower_order_the_other_pivots_update_their_orders_accordingly()
   {
      $primaryA = factory(PrimaryTestPivotModel::class)->create();
      $primaryB = factory(PrimaryTestPivotModel::class)->create();

      $secondaryA = factory(SecondaryTestPivotModel::class)->create();
      $secondaryB = factory(SecondaryTestPivotModel::class)->create();
      $secondaryC = factory(SecondaryTestPivotModel::class)->create();
      $secondaryD = factory(SecondaryTestPivotModel::class)->create();

      $primaryA->secondaries()->attach($secondaryA);
      $primaryA->secondaries()->attach($secondaryB);
      $primaryA->secondaries()->attach($secondaryC);
      $primaryA->secondaries()->attach($secondaryD);

      $primaryB->secondaries()->attach($secondaryA);
      $primaryB->secondaries()->attach($secondaryB);

      $primaryA->secondaries()->updateExistingPivot($secondaryC->id,['order' => 2]);

      $this->assertEquals($secondaryA->id, $primaryA->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryA->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryC->id, $primaryA->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryA->secondaries[1]->pivot->order);
      $this->assertEquals($secondaryB->id, $primaryA->secondaries[2]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(3, $primaryA->secondaries[2]->pivot->order);
      $this->assertEquals($secondaryD->id, $primaryA->secondaries[3]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(4, $primaryA->secondaries[3]->pivot->order);

      $this->assertEquals($secondaryA->id, $primaryB->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryB->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryB->id, $primaryB->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryB->secondaries[1]->pivot->order);
   }

   /** @test */
   public function when_a_pivot_model_with_the_orderable_trait_has_its_order_column_updated_to_a_hgiher_order_the_other_models_update_their_orders_accordingly()
   {
      $primaryA = factory(PrimaryTestPivotModel::class)->create();
      $primaryB = factory(PrimaryTestPivotModel::class)->create();

      $secondaryA = factory(SecondaryTestPivotModel::class)->create();
      $secondaryB = factory(SecondaryTestPivotModel::class)->create();
      $secondaryC = factory(SecondaryTestPivotModel::class)->create();
      $secondaryD = factory(SecondaryTestPivotModel::class)->create();

      $primaryA->secondaries()->attach($secondaryA);
      $primaryA->secondaries()->attach($secondaryB);
      $primaryA->secondaries()->attach($secondaryC);
      $primaryA->secondaries()->attach($secondaryD);

      $primaryB->secondaries()->attach($secondaryA);
      $primaryB->secondaries()->attach($secondaryB);

      $primaryA->secondaries()->updateExistingPivot($secondaryB,['order' => 3]);

      $this->assertEquals($secondaryA->id, $primaryA->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryA->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryC->id, $primaryA->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryA->secondaries[1]->pivot->order);
      $this->assertEquals($secondaryB->id, $primaryA->secondaries[2]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(3, $primaryA->secondaries[2]->pivot->order);
      $this->assertEquals($secondaryD->id, $primaryA->secondaries[3]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(4, $primaryA->secondaries[3]->pivot->order);

      $this->assertEquals($secondaryA->id, $primaryB->secondaries[0]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(1, $primaryB->secondaries[0]->pivot->order);
      $this->assertEquals($secondaryB->id, $primaryB->secondaries[1]->pivot->secondary_test_pivot_model_id);
      $this->assertEquals(2, $primaryB->secondaries[1]->pivot->order);
   }
}

class PrimaryTestPivotModel extends Model{

   public function secondaries() {
      return $this
         ->belongsToMany(SecondaryTestPivotModel::class,'primary_secondary')
         ->withPivot('order')
         ->orderBy('pivot_order')
         ->using(OrderablePivotTestRelationship::class);
   }

}

class SecondaryTestPivotModel extends Model{

   public function primaries() {
      return $this
         ->belongsToMany(PrimaryTestPivotModel::class,'primary_secondary')
         ->using(OrderablePivotTestRelationship::class);
   }

}

class OrderablePivotTestRelationship extends Pivot implements OrderableInterface{

   use PivotOrderable;

   protected $table = 'primary_secondary';
   public $timestamps = false;
   public $incrementing = true;

   public function scopeWithinOrderGroup($query, OrderableInterface $orderableModel)
   {
      return $query->where('primary_test_pivot_model_id', $orderableModel->primary_test_pivot_model_id);
   }

   public function scopeOrdered(Builder $query): void
   {
      $query->orderBy('primary_test_pivot_model_id')->orderBy($this->getOrderableColumn());
   }

   public function scopeNotSelf(Builder $query, OrderableInterface $orderableModel): void {
      $query->where('secondary_test_pivot_model_id', '!=', $orderableModel->secondary_test_pivot_model_id);

   }

   public function columnsAffectingOrderGroup(): Collection
   {
      return collect(['primary_test_pivot_model_id']);
   }

}
