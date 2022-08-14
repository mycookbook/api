<?php

namespace Unit\Models;

use App\Models\User;

class UserModelTest extends \TestCase
{
    /**
     * @test
     */
    public function it_is_an_instance_of_eloquent_model()
    {
        $user = new User();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $user);
    }

    /**
     * @test
     */
    public function it_implements_jwt_subject_contract()
    {
        $user = new User();
        $this->assertInstanceOf('Tymon\JWTAuth\Contracts\JWTSubject', $user);
    }

    /**
     * @test
     */
    public function it_implements_the_authenticatable_contract()
    {
        $user = new User();
        $this->assertInstanceOf('Illuminate\Contracts\Auth\Authenticatable', $user);
    }

    /**
     * @test
     */
    public function it_implements_the_authorizable_contract()
    {
        $user = new User();
        $this->assertInstanceOf('Illuminate\Contracts\Auth\Access\Authorizable', $user);
    }

    /**
     * @test
     */
    public function it_has_a_recipes_relationship()
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'recipes'));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $user->recipes());
    }

    /**
     * @test
     */
    public function it_has_a_cookbooks_relationship()
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'cookbooks'));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $user->cookbooks());
    }
}
