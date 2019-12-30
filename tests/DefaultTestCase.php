<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Tests;

use Flyhjaelp\LaravelEloquentOrderable\EloquentOrderableServiceProvider;
use Orchestra\Testbench\TestCase;

class DefaultTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [EloquentOrderableServiceProvider::class];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->withFactories(__DIR__.'/database/factories');
    }
}
