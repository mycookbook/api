<?php

namespace App\Http\Controllers;

use App\User;
use App\Recipe;
use App\Cookbook;

/**
 * Class StatsController
 */
class StatsController extends Controller
{
    /**
     * Return stats meta data
     * Users, Recipes and Cookbooks count
     *
     * @param User     $user     user
     * @param Recipe   $recipe   recipe
     * @param Cookbook $cookbook cookbook
     *
     * @return array
     */
    public function index(
        User $user,
        Recipe $recipe,
        Cookbook $cookbook
    ) {
        $users = self::getUsersStats($user);
        $recipes = self::getRecipesStats($recipe);
        $cookbooks = self::getCookbooksStats($cookbook);

        $data = [
            'users' => $users,
            'recipes' => $recipes,
            'cookbooks' => $cookbooks
        ];

        return response(["data" => $data]);
    }

    /**
     * Return users count
     *
     * @param User $user user instance
     *
     * @return UserController
     */
    protected static function getUsersStats($user)
    {
        $users = $user->count();

        return $users;
    }

    /**
     * Return recipes stats
     *
     * @param Recipe $recipe recipe instance
     *
     * @return RecipeController
     */
    protected static function getRecipesStats($recipe)
    {
        $recipes = $recipe->count();

        return $recipes;
    }

    /**
     * Return Cookbook stats
     *
     * @param Cookbook $cookbook cookbook instance
     *
     * @return CookbookController
     */
    protected static function getCookbooksStats($cookbook)
    {
        $cookbooks = $cookbook->count();

        return $cookbooks;
    }
}
