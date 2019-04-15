# Laravel Eloquent Orderable

Laravel Eloquent Orderable is a package that helps you make your eloquent models orderable, either within a group our within all other models of the same class.

## Installation

Install via composer

```bash
composer require flyhjaelp/laravel-eloquent-orderable
```

## Database setup
If you want to use the orderable functionality on a model it has to have a database column it can be ordered by. By default the package will look for a column named "order", but this can be overwritten. The order column should be an unsigned integer that's nullable. Example:

```
Schema::create('orderable_test_models', function (Blueprint $table) {
  $table->unsignedInteger('order')->nullable();
});
```

## Default Usage

```php
<?php

use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;
use Flyhjaelp\LaravelEloquentOrderable\Traits\Orderable;
use Illuminate\Database\Eloquent\Model;

class Foobar extends Model implements OrderableInterface { //implement the orderable interface

   use Orderable; //use the orderable trait
   
}
```

#### Creating a new model without a specified order
New instances will now have an order added to them, by default they are added as last in order.

```php
$foobarA = (new Foobar())->save();
$foobarB = (new Foobar())->save();
$foobarC = (new Foobar())->save();
Foobar::all()->pluck('order','id');
// will output [1 => 1, 2 => 2, 3 => 3]
```
#### Creating a new model with a specified order
If an order is specified when being created that order will update already existing orders accordingly.
```php
$foobarD = new Foobar();
$foobarD->order = 2;
$foobarD->save();
Foobar::all()->pluck('order','id');
// will output [1 => 1, 4 => 2, 2 => 3, 3 => 4]
```

#### Updating the order of a model
When updating a models order the other models automatically update their orders accordingly.
```php
$foobarC->order = 2;`
$foobarC->save();
Foobar::all()->pluck('order','id'); // will output [1 => 1, 3 => 2, 4 => 3, 2 => 3]
```

#### Deleting a model
When deleting a model, the order of all other models with a higher order will have their order decreased by one.
```php
$foobarA->delete();
Foobar::all()->pluck('order','id'); // will output [3 => 1, 4 => 2, 2 => 3]
```

## Grouping usage
You can make a group within your model, and the order only applies within the group. Example you might have a model called MenuItem which should be grouped by menu_id, and the order should only apply within it's group. To add a group to model you have to include the orderableWithinGroup trait and implement the following functions:
- scopeOrdered(Builder $query)
- scopeWithinOrderGroup(Builder $query, OrderableInterface $orderableModel)
- columnsAffectingOrderGroup()

```php
<?php

use Flyhjaelp\LaravelEloquentOrderable\Interfaces\OrderableInterface;
use Flyhjaelp\LaravelEloquentOrderable\Traits\OrderableWithinGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MenuItem extends Model implements OrderableInterface { //implement the orderable interface

   use OrderableWithinGroup; //use the orderableWithinGroup trait
   
   public function scopeOrdered(Builder $query): void{
      $query->orderBy('menu_id')->orderBy('order');
   }
   
   public function scopeWithinOrderGroup(Builder $query, OrderableInterface $orderableModel): void{
      $query->where('menu_id',$orderableModel->menu_id);
   }
   
   public function columnsAffectingOrderGroup(): Collection{
      return collect(['menu_id']);
   }
   
}
```

#### Models are now ordered within their group
New instances will have an order added to them, by default they are added as last in order within their group

```php
$foobarA = new Foobar();
$foobarA->menu_id = 1;
$foobarA->save();
$foobarB= new Foobar();
$foobarB->menu_id = 1;
$foobarB->save();
$foobarC = new Foobar();
$foobarC->menu_id = 2;
$foobarC->save();
Foobar::all()->pluck('order','id');
// will output [1 => 1, 2 => 2, 3 => 1]
```

## Overwriting default values
#### Overwriting default ordering column
```
public function getOrderableColumn(): string {
  return 'non_default_order_column';
}
```

#### Overwriting global ordering scope
```
public function scopeOrdered(Builder $query): void{
  $query->orderBy('menu_id')->orderBy('order');
}
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENSE.md)