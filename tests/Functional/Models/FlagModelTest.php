<?php

namespace Functional\Models;

use App\Flag;

class FlagModelTest extends \TestCase
{
    /**
     * @test
     */
    public function it_is_an_instance_of_eloquent_model()
    {
        $flag = new Flag();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $flag);
    }

    /**
     * @test
     */
    public function it_has_a_cookbook_method()
    {
        $flag = new Flag();
        $this->assertTrue(method_exists($flag, 'cookbook'));
    }
}
