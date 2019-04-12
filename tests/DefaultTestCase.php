<?php

namespace Flyhjaelp\LaravelEloquentOrderable\Tests;

use Orchestra\Testbench\TestCase;
use Flyhjaelp\LaravelEloquentOrderable\EloquentOrderableServiceProvider;

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
