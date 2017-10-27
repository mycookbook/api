<?php


/**
 * Class StatsTest
 */
class StatsTest extends TestCase
{
    /**
     * Run migrations
     * Seed DB
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    /**
     * Test Application
     *
     * @return void
     */
    public function testApplication()
    {
        $this->get('/api/v1/stats')
            ->seeJsonStructure(
                [
                    'data' => [
                        'users',
                        'recipes',
                        'cookbooks'
                    ]
                ]
            )->assertResponseStatus(200);
    }

    /**
     * Reset Migrations
     *
     * @return void
     */
    public function tearDown()
    {
        $this->artisan('migrate:reset');
    }
}
