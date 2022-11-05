<?php

namespace App\Services;

use App\Models\Cookbook;
use App\Models\Recipe;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function searchEveryWhere($query)
    {
        return Cookbook::query()
            ->leftJoin('users', 'users.id', '=', 'cookbooks.user_id')
            ->leftJoin('recipes', 'recipes.user_id', '=', 'users.id')
            ->where('cookbooks.name', 'LIKE', '%'.$query.'%')
            ->orWhere('cookbooks.description', 'LIKE', '%'.$query.'%')
            ->orWhere('cookbooks.slug', 'LIKE', '%'.$query.'%')
            ->orWhere('cookbooks.alt_text', 'LIKE', '%'.$query.'%')
            ->orWhere('users.name', 'LIKE', '%'.$query.'%')
            ->orWhere('users.name_slug', 'LIKE', '%'.$query.'%')
            ->orWhere('users.pronouns', 'LIKE', '%'.$query.'%')
            ->orWhere('users.about', 'LIKE', '%'.$query.'%')
            ->orWhere('recipes.name', 'LIKE', '%'.$query.'%')
            ->orWhere('recipes.description', 'LIKE', '%'.$query.'%')
            ->orWhere('recipes.summary', 'LIKE', '%'.$query.'%')
            ->orWhere('recipes.slug', 'LIKE', '%'.$query.'%')
            ->get();
    }

    /**
     * @param string $tag
     * @return mixed
     */
    public function getAllCookbooksByTag(string $tag): Collection
    {
        return Cookbook::where('tags', 'LIKE', '%'.$tag.'%')->get();
    }

    /**
     * @param $tag
     * @return mixed
     */
    public function getAllRecipesByTag($tag)
    {
        return Recipe::where('tags', 'LIKE', '%'.$tag.'%')->get();
    }
}