<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryCookbook;
use App\Models\Cookbook;
use App\Models\CookbookUser;
use App\Models\Following;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchService
{
    /**
     * @param string $query
     * @return Collection
     */
    public function searchEveryWhere(string $query)
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
     * @param array $tags
     * @return Collection
     */
    public function getAllCookbooksByTag(array $tags): Collection
    {
        $builder = Cookbook::where('tags', 'LIKE', '%' . $tags[0] . '%');

        for ($i=1;$i<count($tags); $i++) {
            $builder = $builder->orWhere('tags', 'LIKE', '%' . $tags[$i] . '%');
        }

        $results = $builder->get();

        foreach($results as $result) {
            $contains = [];
            $missing = [];
            $result_tags = explode(",", str_replace(" ", "", $result->tags));

            foreach ($tags as $tag) {
                $tag_name = trim($tag);

                if (in_array($tag_name, $result_tags)) {
                    $contains[] = $tag_name;
                } else {
                    $missing[] = $tag_name;
                }
            }

            /** @phpstan-ignore-next-line */
            $result->metaData = [
                'contains' => $contains,
                'missing' => $missing
            ];
        }

        return $results;
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
        $categories = Category::whereIn("name", $category_names)->pluck("id");

        if ($categories->isNotEmpty()) {
            $cookbooks = CategoryCookbook::whereIn("category_id", $categories)->pluck("cookbook_id");

            if ($cookbooks->isEmpty()) {
                return collect([]);
            }

            $results = Cookbook::whereIn("id", $cookbooks->toArray())->get();

            foreach($results as $result) {
                $contains = [];
                $missing = [];
                $result_catnames = $result->categories->pluck("name")->toArray();

                foreach ($category_names as $cat_name) {
                    $cat_name = trim($cat_name);

                    if (in_array($cat_name, $result_catnames)) {
                        $contains[] = $cat_name;
                    } else {
                        $missing[] = $cat_name;
                    }
                }

                /** @phpstan-ignore-next-line */
                $result->metaData = [
                    'contains' => $contains,
                    'missing' => $missing
                ];
            }

            return $results;
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
        /** @phpstan-ignore-next-line */
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
        /** @phpstan-ignore-next-line */
        if ($user = JWTAuth::parseToken()->user()) {
            $me = $user->getKey();

            if ($recipeName == "") {
                return Recipe::where(["user_id" => $me])->get();
            }

            return Recipe::where("name", "like", "%".$recipeName."%")->get();
        }

        return new Collection();
    }

    public function getFollowing()
    {
        /** @phpstan-ignore-next-line */
        if ($me = JWTAuth::parseToken()->user()) {
            $recipes = [];
            $following = Following::where(['follower_id' => $me->getKey()])->pluck('following')->toArray();

            foreach ($following as $f) {
                $lastFiveRecipes = Recipe::where(['user_id' => $f])
                    ->latest()->take(5)->get()->toArray();

                foreach ($lastFiveRecipes as $v) {
                    $recipes[] = $v;
                }
            }

            return collect($recipes)->sortByDesc('updated_at')->filter(function($recipe) {
                return $recipe['is_draft'] === false;
            })->values();

        }

        return new Collection();
    }

    public function getForYou()
    {
        return new Collection();
    }
}
