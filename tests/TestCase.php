<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Traits\CreatesObjects;

abstract class TestCase extends BaseTestCase
{
    use CreatesObjects;
    use CreatesApplication;
    use DatabaseMigrations;

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
