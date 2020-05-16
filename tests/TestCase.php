<?php

use Traits\CreatesObjects;
use Laravel\Lumen\Testing\DatabaseMigrations;

/**
 * Class TestCase
 */
abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
	use CreatesObjects;
	use DatabaseMigrations;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
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
