<?php

namespace Functional\Models;

use App\Subscriber;

class SubscriberModelTest extends \TestCase
{
    /**
     * @test
     */
    public function it_is_an_instance_of_eloquent_model()
    {
        $subscriber = new Subscriber();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $subscriber);
    }
}
