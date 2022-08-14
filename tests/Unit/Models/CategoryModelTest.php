<?php

namespace Unit\Models;

use App\Category;

class CategoryModelTest extends \TestCase
{
    /**
     * @test
     */
    public function it_is_an_instance_of_eloquent_model()
    {
        $category = new Category();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $category);
    }

    /**
     * @test
     */
    public function it_has_a_cookbooks_method()
    {
        $category = new Category();
        $this->assertTrue(method_exists($category, 'cookbooks'));
    }
}
