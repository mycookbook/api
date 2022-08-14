<?php

namespace Functional;

use Illuminate\Foundation\Testing\WithoutMiddleware;

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
            ->assertStatus(200);
    }
}
