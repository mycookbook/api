<?php

namespace Functional\Models;

use App\User;
use Carbon\Carbon;

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

	/**
	 * @test
	 */
	public function the_created_at_attribute_is_translated_to_human_readable_format()
	{
		$user = new User([
			'name' => 'test name',
			'email' => 'test@mail.com',
			'password' => 'testpassword',
			'following' => 0,
			'followers' => 0
		]);

		$user->save();
		$user->created_at = Carbon::now()->subDays(3);
		$user->save();
		$user_ = User::find($user->id);
		$this->assertSame('3 days ago', $user_->created_at);
	}
}
