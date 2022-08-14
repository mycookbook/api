<?php

namespace App\Http\Controllers;

use App\Models\Cookbook;
use App\Models\Recipe;
use App\Models\User;

/**
 * Class StatsController
 */
class StatsController extends Controller
{
    /**
     * Return stats meta data
     * Users, Recipes and Cookbooks count
     *
     * @param  User  $user user
     * @param  Recipe  $recipe recipe
     * @param  Cookbook  $cookbook cookbook
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function index(
        User $user,
        Recipe $recipe,
        Cookbook $cookbook
    ) {
        $data = [
            'users' => self::getUsersStats($user),
            'recipes' => self::getRecipesStats($recipe),
            'cookbooks' => self::getCookbooksStats($cookbook),
        ];

        return response(['data' => $data]);
    }

    /**
     * Return users count
     *
     * @param  \App\Models\User  $user
     * @return int
     */
    protected static function getUsersStats($user): int
    {
        return $user->count();
    }

    /**
     * Return recipes count
     *
     * @param  \App\Models\Recipe  $recipe
     * @return int
     */
    protected static function getRecipesStats($recipe): int
    {
        return $recipe->count();
    }

    /**
     * Return Cookbook count
     *
     * @param  \App\Models\Cookbook  $cookbook
     * @return int
     */
    protected static function getCookbooksStats($cookbook): int
    {
        return $cookbook->count();
    }
}
