<?php
/**
 * RecipeController
 */

namespace App\Http\Controllers;

use App\Recipe;
use Illuminate\Http\Request;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class RecipeController extends Controller
{
    /**
     * Get all recipes belonging to a user
     *
     * @param int $user_id userid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($user_id)
    {
        $recipes = Recipe::with('Users')->get();

        return response()->json(
            [
                'response' => [
                    'user_id' => $user_id,
                    'data' => $recipes->toArray()
                ]
            ], 200
        );
    }

    /**
     * Create recipe for user
     *
     * @param Request $request    Form input
     * @param int     $userId     user
     * @param int     $cookbookId cookbook
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $userId, $cookbookId)
    {
        $recipe = new Recipe();

        $recipe->name = $request->input('name');
        $recipe->ingredients = $request->input('ingredients');
        $recipe->imgUrl = $request->input('url');
        $recipe->description = $request->input('description');
        $recipe->user_id = $userId;
        $recipe->cookbook_id = $cookbookId;

        if ($recipe->save()) {
            return response()->json(
                [
                    'response' => [
                        'created' => true
                    ]
                ], 200
            );
        } else {
            return response()->json(
                [
                    'response' => [
                        'created' => false
                    ]
                ], 401
            );
        }
    }
}
