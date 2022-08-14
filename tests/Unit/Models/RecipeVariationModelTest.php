<?php

namespace Unit\Models;

use App\RecipeVariation;
use Illuminate\Http\Response;

class RecipeVariationModelTest extends \TestCase
{
    /**
     * @test
     */
    public function it_is_an_instance_of_eloquent_model()
    {
        $variety = new RecipeVariation();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $variety);
    }

    /**
     * @test
     */
    public function it_has_a_recipe_method()
    {
        $variety = new RecipeVariation();
        $this->assertTrue(method_exists($variety, 'recipe'));
    }
}
