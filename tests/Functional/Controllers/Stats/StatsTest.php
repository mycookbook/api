<?php

namespace Functional\Controllers\Stats;

use Laravel\Lumen\Testing\WithoutMiddleware;

/**
 * Class StatsTest
 */
class StatsTest extends \TestCase
{
    use WithoutMiddleware;

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
                        'cookbooks',
                    ],
                ]
            )->assertResponseStatus(200);
    }
}
