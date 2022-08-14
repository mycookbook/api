<?php

namespace Traits;

use App\Models\Category;
use App\Models\Cookbook;
use App\Models\Flag;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Str;

trait CreatesObjects
{
    protected $user;

    protected $cookbook;

    protected $flag;

    protected $category;

    protected $recipe;

    /**
     * Returns a User instance
     *
     * @param  array  $args
     * @return User
     */
    protected function createUser($args = []): User
    {
        $user = new User([
            'name' => 'test mate 2',
            'email' => isset($args['email']) ? $args['email'] : 'test2@mail.com',
            'password' => '@X_I123^76',
            'followers' => 13,
            'following' => 1,
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
            'color' => '000000',
            'emoji' => '',
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
            'nationality' => 'Ugandan',
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
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->flag->id,
            'user_id' => $this->user->id,
            'alt_text' => 'example',
        ]);

        if ($cookbook->save()) {
            return $this->cookbook = $cookbook;
        }
    }

    /**
     * Returns a recipe Instance
     *
     * @return Recipe
     *
     * @throws \Exception
     */
    protected function createRecipe(): Recipe
    {
        $this->createCookbook();

        $recipe = new Recipe([
            'name' => 'sample title',
            'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
            'imgUrl' => 'http://lorempixel.com/400/200/',
            'description' => 'sample description',
            'cookbook_id' => $this->cookbook->id,
            'summary' => Str::random(100),
            'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
            'calorie_count' => 1200,
            'user_id' => $this->user->id,
            'cook_time' => '2020-04-07 00:55:00',
            'servings' => 2,
            'prep_time' => '2020-01-01 00:00:00',
        ]);

        try {
            $recipe->save();

            return $this->recipe = $recipe;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
