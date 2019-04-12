<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Tests\database;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Flyhjaelp\LaravelEloquentOrderable\Tests\DefaultTestCase;
use Flyhjaelp\LaravelEloquentOrderable\Traits\OrderableWithinGroup;
use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;

class OrderableGroupChangingTest extends DefaultTestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_grouped_model_with_the_orderable_trait_has_its_order_and_group_updated_to_a_new_group_both_the_old_and_new_groups_update_accordingly()
    {
        $orderableModelA = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelB = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelC = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelD = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelE = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelF = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'C']);
        $orderableModelG = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'C']);
        $orderableModelH = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'C']);
        $orderableModelI = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'C']);
        $orderableModelL = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'D']);
        $orderableModelM = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'D']);

        $orderableModelB->order = 3;
        $orderableModelB->group_a = 'B';
        $orderableModelB->save();

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(3, $orderableModelB->fresh()->order);
        $this->assertEquals(2, $orderableModelC->fresh()->order);
        $this->assertEquals(3, $orderableModelD->fresh()->order);
        $this->assertEquals(4, $orderableModelE->fresh()->order);
        $this->assertEquals(1, $orderableModelF->fresh()->order);
        $this->assertEquals(2, $orderableModelG->fresh()->order);
        $this->assertEquals(4, $orderableModelH->fresh()->order);
        $this->assertEquals(5, $orderableModelI->fresh()->order);
        $this->assertEquals(1, $orderableModelL->fresh()->order);
        $this->assertEquals(2, $orderableModelM->fresh()->order);
    }

    /** @test */
    public function when_a_grouped_model_with_the_orderable_trait_has_its_order_and_multiple_group_columns_updated_to_a_new_group_both_the_old_and_new_groups_update_accordingly()
    {
        $orderableModelA = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelB = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelC = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelD = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelE = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'A', 'group_b' => 'C']);
        $orderableModelF = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'C']);
        $orderableModelG = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'C']);
        $orderableModelH = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'C']);
        $orderableModelI = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'C']);
        $orderableModelL = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'D']);
        $orderableModelM = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'D']);
        $orderableModelN = factory(OrderableGroupChangingTestModel::class)->create(['group_a' => 'B', 'group_b' => 'D']);

        $orderableModelB->order = 2;
        $orderableModelB->group_a = 'B';
        $orderableModelB->group_b = 'D';
        $orderableModelB->save();

        $this->assertEquals(1, $orderableModelA->fresh()->order);
        $this->assertEquals(2, $orderableModelB->fresh()->order);
        $this->assertEquals(2, $orderableModelC->fresh()->order);
        $this->assertEquals(3, $orderableModelD->fresh()->order);
        $this->assertEquals(4, $orderableModelE->fresh()->order);
        $this->assertEquals(1, $orderableModelF->fresh()->order);
        $this->assertEquals(2, $orderableModelG->fresh()->order);
        $this->assertEquals(3, $orderableModelH->fresh()->order);
        $this->assertEquals(4, $orderableModelI->fresh()->order);
        $this->assertEquals(1, $orderableModelL->fresh()->order);
        $this->assertEquals(3, $orderableModelM->fresh()->order);
        $this->assertEquals(4, $orderableModelN->fresh()->order);
    }
}

class OrderableGroupChangingTestModel extends Model implements OrderableInterface
{
    use OrderableWithinGroup;

    public function scopeWithinOrderGroup($query, OrderableInterface $orderableModel)
    {
        return $query
         ->where('group_a', $orderableModel->group_a)
         ->where('group_b', $orderableModel->group_b);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query
         ->orderBy('group_a')
         ->orderBy('group_b')
         ->orderBy($this->getOrderableColumn());
    }

    public function columnsAffectingOrderGroup(): Collection
    {
        return collect(['group_a', 'group_b']);
    }
}
