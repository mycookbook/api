<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Traits\CreatesObjects;

/**
 * Class TestCase
 */
abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesObjects;
    use DatabaseMigrations;

    /**
     * @return mixed
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Reset Migrations
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->artisan('migrate:reset');

        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });

        parent::tearDown();
    }

//    protected function disableExceptionHandling()
//    {
//        $this->app->instance(ExceptionHandler::class, new class extends Handler {
//            public function __construct() {}
//
//            public function report(Exception $e)
//            {
//                // no-op
//            }
//
//            public function render($request, Exception $e) {
//                throw $e;
//            }
//        });
//    }
}
