<?php

namespace Unit\Models;

use App\Models\Subscriber;

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
