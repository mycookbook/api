<?php
/**
 * UserController
 */

namespace App\Http\Controllers;

use App\User;
use App\Recipe;
use App\Cookbook;
use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        //
    }

    /**
     * Update user
     *
     * @param int $id unique identification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $user = $this->getUser($id);

        return $user;

    }

    /**
     * Get all users fromt he database
     *
     * @return int
     */
    public function getAllUsers()
    {
        $users = User::with('Recipes', 'Cookbooks')->get();

        if (count($users) < 1) {
            return response()->json(
                [
                    'response' => [
                        'success' => false,
                        'data' => null
                    ]
                ], 404
            );
        }

        return response()->json(
            [
                'response' => [
                    'data' => $users->toArray()
                ]
            ], 200
        );
    }

    /**
     * Get one user
     *
     * @param int $id id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        $user = User::with('Recipe', 'Cookbook')->find($id);

        if (! $user) {
            return response()->json(
                [
                    'response' => [
                        'success' => false,
                        'data' => 'Not found!'
                    ]
                ], 404
            );
        }

        return response()->json(
            [
                'response' => [
                    'success' => false,
                    'data' => $user->toArray()
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
    public function createRecipe(Request $request, $userId, $cookbookId)
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

    /**
     * Create cookbook for user
     *
     * @param Request $request Form input
     * @param int     $userId  unique identofocation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCookbook(Request $request, $userId)
    {
        $cookbook = new Cookbook();

        $cookbook->name = $request->input('name');
        $cookbook->description = $request->input('description');
        $cookbook->user_id = $userId;

        if ($cookbook->save()) {
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
