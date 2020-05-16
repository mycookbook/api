<?php

namespace Traits;

use App\Flag;
use App\User;
use App\Recipe;
use App\Category;
use App\Cookbook;
use Illuminate\Support\Str;

trait TestObjects
{
	protected $user;
	protected $cookbook;
	protected $flag;
	protected $category;
	protected $recipe;

	/**
	 * Returns a User instance
	 *
	 * @return User
	 */
	protected function createUser(): User
	{
		$user = new User([
			'name' => 'test mate 2',
			'email' => 'test2@mail.com',
			'password' => '@X_I123^76',
			'followers' => 13,
			'following' => 1
		]);

		if ($user->save()) {
			return $this->user = $user;
		}
	}

	/**
	 * Returns Category instance
	 *
	 * @return Category
	 */
	protected function createCategory(): Category
	{
		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);

		if ($category->save()) {
			return $this->category = $category;
		}
	}

	/**
	 * Returns a Flag instance
	 *
	 * @return Flag
	 */
	protected function createFlag(): Flag
	{
		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);

		if ($flag->save()) {
			return $this->flag = $flag;
		}
	}

	/**
	 * Returns a Cookbook Instance
	 *
	 * @return Cookbook
	 */
	protected function createCookbook(): Cookbook
	{
		$this->createUser();
		$this->createCategory();
		$this->createFlag();

		$cookbook = new Cookbook([
			'name' => 'sample cookbook',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'category_id' => $this->category->id,
			'flag_id' => $this->flag->id,
			'user_id' => $this->user->id
		]);

		if ($cookbook->save()) {
			return $this->cookbook = $cookbook;
		}
	}

	/**
	 * Returns a recipe Instance
	 *
	 * @return Recipe
	 */
	protected function createRecipe(): Recipe
	{
		$this->createCookbook();

		$recipe = new Recipe([
			'name' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => 'sample description',
			'cookbook_id' => $this->cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200,
			'user_id' => $this->user->id
		]);

		if ($recipe->save()) {
			return $this->recipe = $recipe;
		}
	}
}