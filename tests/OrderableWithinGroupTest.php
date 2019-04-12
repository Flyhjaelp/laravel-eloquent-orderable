<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Tests;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Flyhjaelp\LaravelEloquentOrderable\Traits\OrderableWithinGroup;
use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;

class OrderableWithinGroupTest extends DefaultTestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_grouped_model_with_the_orderable_trait_is_created_if_no_specific_order_is_given_its_added_as_the_last_model_within_its_group()
    {
        $orderableModelA = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelB = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelC = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);

        $this->assertEquals(1, $orderableModelA->order);
        $this->assertEquals(2, $orderableModelB->order);
        $this->assertEquals(1, $orderableModelC->order);
    }

    /** @test */
    public function when_a_grouped_model_with_the_orderable_trait_is_created_with_a_specified_order_other_models_with_the_same_order_or_higher_increases_their_order_by_1_within_its_group()
    {
        $orderableModelA = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelB = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelC = factory(OrderableGroupTestModel::class)->create(['group' => 'A', 'order' => 2]);
        $orderableModelD = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelE = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);
        $orderableModelF = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(3, $orderableModelB->fresh()->order);
        $this->assertEquals(2, $orderableModelC->fresh()->order);
        $this->assertEquals(4, $orderableModelD->fresh()->order);
        $this->assertEquals(1, $orderableModelE->fresh()->order);
        $this->assertEquals(2, $orderableModelF->fresh()->order);
    }

    /** @test */
    public function when_a_grouped_model_with_the_orderable_trait_is_deleted_other_models_the_same_order_or_higher_get_their_order_decreased_with_one_within_its_group()
    {
        $orderableModelA = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelB = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelC = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelD = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);
        $orderableModelE = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);
        $orderableModelF = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);

        $orderableModelB->delete();

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(2, $orderableModelC->fresh()->order);
        $this->assertEquals(3, $orderableModelF->fresh()->order);
    }

    /** @test */
    public function when_a_grouped_model_with_the_orderable_trait_has_its_order_column_updated_to_a_lower_order_the_other_models_update_their_orders_accordingly_within_its_group()
    {
        $orderableModelA = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelB = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelC = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelD = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelE = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelF = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);
        $orderableModelG = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);
        $orderableModelH = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);

        $orderableModelD->order = 2;
        $orderableModelD->save();

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(3, $orderableModelB->fresh()->order);
        $this->assertEquals(4, $orderableModelC->fresh()->order);
        $this->assertEquals(2, $orderableModelD->fresh()->order);
        $this->assertEquals(5, $orderableModelE->fresh()->order);
        $this->assertEquals(1, $orderableModelF->fresh()->order);
        $this->assertEquals(2, $orderableModelG->fresh()->order);
        $this->assertEquals(3, $orderableModelH->fresh()->order);
    }

    /** @test */
    public function when_a_grouped_model_with_the_orderable_trait_has_its_order_column_updated_to_a_hgiher_order_the_other_models_update_their_orders_accordingly_within_its_group()
    {
        $orderableModelA = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelB = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelC = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelD = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelE = factory(OrderableGroupTestModel::class)->create(['group' => 'A']);
        $orderableModelF = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);
        $orderableModelG = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);
        $orderableModelH = factory(OrderableGroupTestModel::class)->create(['group' => 'B']);

        $orderableModelB->order = 4;
        $orderableModelB->save();

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(4, $orderableModelB->fresh()->order);
        $this->assertEquals(2, $orderableModelC->fresh()->order);
        $this->assertEquals(3, $orderableModelD->fresh()->order);
        $this->assertEquals(5, $orderableModelE->fresh()->order);
        $this->assertEquals(1, $orderableModelF->fresh()->order);
        $this->assertEquals(2, $orderableModelG->fresh()->order);
        $this->assertEquals(3, $orderableModelH->fresh()->order);
    }
}

class OrderableGroupTestModel extends Model implements OrderableInterface
{
    use OrderableWithinGroup;

    public function scopeWithinOrderGroup($query, OrderableInterface $orderableModel)
    {
        return $query->where('group', $orderableModel->group);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('group')->orderBy($this->getOrderableColumn());
    }

    public function columnsAffectingOrderGroup(): Collection
    {
        return collect(['group']);
    }
}
