<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Flyhjaelp\LaravelEloquentOrderable\Traits\Orderable;
use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;

class OrderableTest extends DefaultTestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_model_with_the_orderable_trait_is_by_default_ordered_by_order()
    {
        $orderableModelA = factory(OrderableTestModel::class)->create();
        $orderableModelB = factory(OrderableTestModel::class)->create();
        $orderableModelC = factory(OrderableTestModel::class)->create(['order' => 2]);

        $modelsFromDB = OrderableTestModel::all();

        $this->assertEquals($orderableModelA->id, $modelsFromDB->shift()->id);
        $this->assertEquals($orderableModelC->id, $modelsFromDB->shift()->id);
        $this->assertEquals($orderableModelB->id, $modelsFromDB->shift()->id);
    }

    /** @test */
    public function when_a_model_with_the_orderable_trait_is_created_if_no_specific_order_is_given_its_added_as_the_last_model()
    {
        $orderableModelA = factory(OrderableTestModel::class)->create();
        $orderableModelB = factory(OrderableTestModel::class)->create();

        $this->assertEquals(1, $orderableModelA->order);
        $this->assertEquals(2, $orderableModelB->order);
    }

    /** @test */
    public function when_a_model_with_the_orderable_trait_is_created_with_a_specified_order_other_models_with_the_same_order_or_higher_increases_their_order_by_1()
    {
        $orderableModelA = factory(OrderableTestModel::class)->create();
        $orderableModelB = factory(OrderableTestModel::class)->create();
        $orderableModelC = factory(OrderableTestModel::class)->create();
        $orderableModelD = factory(OrderableTestModel::class)->create(['order' => 2]);

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(3, $orderableModelB->fresh()->order);
        $this->assertEquals(4, $orderableModelC->fresh()->order);
        $this->assertEquals(2, $orderableModelD->fresh()->order);
    }

    /** @test */
    public function when_a_model_with_the_orderable_trait_is_deleted_other_models_the_same_order_or_higher_get_their_order_decreased_with_one()
    {
        $orderableModelA = factory(OrderableTestModel::class)->create();
        $orderableModelB = factory(OrderableTestModel::class)->create();
        $orderableModelC = factory(OrderableTestModel::class)->create();

        $orderableModelB->delete();

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(2, $orderableModelC->fresh()->order);
    }

    /** @test */
    public function when_a_model_with_the_orderable_trait_has_its_order_column_updated_to_a_lower_order_the_other_models_update_their_orders_accordingly()
    {
        $orderableModelA = factory(OrderableTestModel::class)->create();
        $orderableModelB = factory(OrderableTestModel::class)->create();
        $orderableModelC = factory(OrderableTestModel::class)->create();
        $orderableModelD = factory(OrderableTestModel::class)->create();
        $orderableModelE = factory(OrderableTestModel::class)->create();

        $orderableModelD->order = 2;
        $orderableModelD->save();

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(3, $orderableModelB->fresh()->order);
        $this->assertEquals(4, $orderableModelC->fresh()->order);
        $this->assertEquals(2, $orderableModelD->fresh()->order);
        $this->assertEquals(5, $orderableModelE->fresh()->order);
    }

    /** @test */
    public function when_a_model_with_the_orderable_trait_has_its_order_column_updated_to_a_hgiher_order_the_other_models_update_their_orders_accordingly()
    {
        $orderableModelA = factory(OrderableTestModel::class)->create();
        $orderableModelB = factory(OrderableTestModel::class)->create();
        $orderableModelC = factory(OrderableTestModel::class)->create();
        $orderableModelD = factory(OrderableTestModel::class)->create();
        $orderableModelE = factory(OrderableTestModel::class)->create();

        $orderableModelB->order = 4;
        $orderableModelB->save();

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(4, $orderableModelB->fresh()->order);
        $this->assertEquals(2, $orderableModelC->fresh()->order);
        $this->assertEquals(3, $orderableModelD->fresh()->order);
        $this->assertEquals(5, $orderableModelE->fresh()->order);
    }
}

class OrderableTestModel extends Model implements OrderableInterface
{
    use Orderable;
}
