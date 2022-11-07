<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryCookbook;
use App\Models\Cookbook;
use App\Models\CookbookUser;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchService
{
    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function searchEveryWhere($query)
    {
        $cookbooksQueryBuilder = Cookbook::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('slug', 'LIKE', '%' . $query . '%')
            ->get()->toArray();

        $usersQueryBuilder = User::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('name_slug', 'LIKE', '%' . $query . '%')
            ->get()->toArray();

        $recipesQueryBuilder = Recipe::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('slug', 'LIKE', '%' . $query . '%')
            ->get()->toArray();

        return collect(array_merge($cookbooksQueryBuilder, $usersQueryBuilder, $recipesQueryBuilder));
    }

    /**
     * @param string $tag
     * @return mixed
     */
    public function getAllCookbooksByTag(string $tag): Collection
    {
        return Cookbook::where('tags', 'LIKE', '%' . $tag . '%')->get();
    }

    /**
     * @param $tag
     * @param string $column
     * @return mixed
     */
    public function getAllRecipesByTag($tag, $column = "tags")
    {
        return Recipe::where($column, 'LIKE', '%' . $tag . '%')->get();
    }

    /**
     * @return mixed
     */
    public function getMostRecentCookbooks()
    {
        return array_values(Cookbook::all()->sortByDesc('updated_at')->take(1000)->toArray());
    }

    /**
     * @return mixed
     */
    public function getMostRecentRecipes()
    {
        return array_values(Recipe::all()->sortByDesc('updated_at')->take(1000)->toArray());
    }

    /**
     * @param $author_name
     * @return Collection
     */
    public function getAllCookbooksByThisAuthor($author_name)
    {
        $findMatchingUsers = User::where('name', 'LIKE', '%' . $author_name . '%')
            ->orWhere('name_slug', 'LIKE', '%' . $author_name . '%')->pluck("id");

        if ($findMatchingUsers->isNotEmpty()) {
            return Cookbook::whereIn('user_id', $findMatchingUsers->toArray())->get();
        }

        return collect([]);
    }

    /**
     * @param $author_name
     * @return Collection
     */
    public function getAllRecipesByThisAuthor($author_name)
    {
        $findMatchingUsers = User::where('name', 'LIKE', '%' . $author_name . '%')
            ->orWhere('name_slug', 'LIKE', '%' . $author_name . '%')->pluck("id");

        if ($findMatchingUsers->isNotEmpty()) {
            return Recipe::whereIn('user_id', $findMatchingUsers->toArray())->get();
        }

        return collect([]);
    }

    /**
     * @param $category_names
     * @return Collection
     */
    public function getAllCookbooksByCategoryName($category_names)
    {
        $cat_names = explode(",", $category_names);

        $categories = Category::whereIn("name", $cat_names)->pluck("id");

        if ($categories->isNotEmpty()) {
            $cookbooks = CategoryCookbook::whereIn("category_id", $categories)->pluck("cookbook_id");

            if ($cookbooks->isEmpty()) {
                return collect([]);
            }

            return Cookbook::whereIn("id", $cookbooks->toArray())->get();
        }

        return collect([]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function getAllCookbooksHavingThisRecipe($query)
    {
        $cookbook_ids = $this->getAllRecipesByTag($query, "name")->pluck("cookbook_id");

        return Cookbook::whereIn("id", $cookbook_ids->toArray())->get();
    }

    /**
     * @param $expression
     * @return mixed
     */
    public function getAllRecipesWithThisNumberofLikes($expression)
    {
        if (
            str_starts_with($expression, "gt|") ||
            str_starts_with($expression, "lt|")
        ) {
            $than = explode("|", $expression);
            $than = $than[1];

            if (str_starts_with($expression, "gt|")) {
                return Recipe::where("claps", ">", $than)->get();
            }

            if (str_starts_with($expression, "lt|")) {
                return Recipe::where("claps", "<", $than)->get();
            }
        }

        if (str_starts_with($expression, "between:")) {
            $expression = explode(":", $expression);
            $between = explode("|", $expression[1]);

            return Recipe::wherebetween("claps", $between)->get();
        }

        return Recipe::where(["claps" => $expression])->get();
    }

    /**
     * @param $ingredients
     * @return Collection
     */
    public function getAllRecipesByIngredientName($ingredients)
    {
        $ingredientsArray = explode(",", $ingredients);

        $recipes = [];

        foreach($ingredientsArray as $ingr) {
            $recipes[] = array_values(Recipe::where("ingredients", 'LIKE', '%'.$ingr.'%')->get()->toArray());
        }

        return collect($recipes[0]);
    }

    /**
     * @return Collection
     */
    public function getAllCookbooksByMe($cookbookName = "")
    {
        if ($user = JWTAuth::parseToken()->user()) {
            $me = $user->getKey();

            $myOtherContributions = CookbookUser::whereIn("user_id", [$me])->get()->pluck("cookbook_id")->toArray();

            if ($cookbookName == "") {
                $originallyAuthoredByMe = Cookbook::where(["user" => $me])->get()->toArray();
                $myOtherContributions = Cookbook::whereIn("id", $myOtherContributions);
            } else {
                $originallyAuthoredByMe = Cookbook::where("name", "like", "%".$cookbookName."%")->get()->toArray();
                $myOtherContributions = [];
            }

            return collect(array_merge($originallyAuthoredByMe, $myOtherContributions));
        }

        return collect([]);
    }

    /**
     * @return Collection
     */
    public function getAllRecipesByMe($recipeName = "")
    {
        if ($user = JWTAuth::parseToken()->user()) {
            $me = $user->getKey();

            if ($recipeName == "") {
                return Recipe::where(["user_id" => $me])->get();
            }

            return Recipe::where("name", "like", "%".$recipeName."%")->get();
        }

        return collect([]);
    }
}
