<?php

namespace Functional\Models;

use App\Recipe;
use Monolog\Test\TestCase;

class RecipeModelTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_an_instance_of_eloquent_model()
    {
        $recipe = new Recipe();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $recipe);
    }

    /**
     * @test
     */
    public function it_has_a_user_method()
    {
        $recipe = new Recipe();
        $this->assertTrue(method_exists($recipe, 'user'));
    }

    /**
     * @test
     */
    public function it_has_a_cookbook_method()
    {
        $recipe = new Recipe();
        $this->assertTrue(method_exists($recipe, 'cookbook'));
    }

    /**
     * @test
     */
    public function it_has_a_variations_method()
    {
        $recipe = new Recipe();
        $this->assertTrue(method_exists($recipe, 'variations'));
    }
}
