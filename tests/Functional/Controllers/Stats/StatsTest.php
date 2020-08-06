<?php

namespace Tests\Functional\Controllers\Stats;

/**
 * Class StatsTest
 */
class StatsTest extends \TestCase
{
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
}
