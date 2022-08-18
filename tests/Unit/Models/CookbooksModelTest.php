<?php

namespace Unit\Models;

use App\Models\Cookbook;

class CookbooksModelTest extends \TestCase
{
    /**
     * @test
     */
    public function it_is_an_instance_of_eloquent_model()
    {
        $cookbook = new Cookbook();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $cookbook);
    }

    /**
     * @test
     */
    public function it_has_a_recipes_relationship()
    {
        $cookbook = new Cookbook();
        $this->assertTrue(method_exists($cookbook, 'recipes'));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $cookbook->recipes());
    }

    /**
     * @test
     */
    public function it_has_a_users_relationship()
    {
        $cookbook = new Cookbook();
        $this->assertTrue(method_exists($cookbook, 'users'));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $cookbook->users());
    }

    /**
     * @test
     */
    public function it_has_a_categories_relationship()
    {
        $cookbook = new Cookbook();
        $this->assertTrue(method_exists($cookbook, 'categories'));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $cookbook->categories());
    }

    /**
     * @test
     */
    public function it_has_a_flags_relationship()
    {
        $cookbook = new Cookbook();
        $this->assertTrue(method_exists($cookbook, 'flag'));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $cookbook->flag());
    }
}
