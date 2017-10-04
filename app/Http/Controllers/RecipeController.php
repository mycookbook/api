<?php

namespace App\Http\Controllers;

use App\Recipe;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class RecipeController extends Controller
{
    /**
     * Constructor
     *
     * @param JWTAuth $jwt auth-jwt
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->middleware('jwt.auth', ['only' => ['update', 'store', 'destroy']]);
        $this->jwt = $jwt;
        $this->user = $this->jwt->parseToken()->authenticate();

        if (! $this->user ) {
            return response()->json(
                [
                    'msg' => 'user not authenticated'
                ]
            );
        }
    }
    /**
     * Get all recipes belonging to a user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $recipes = Recipe::with('Cookbook')
            ->where('user_id', $this->jwt->toUser()->id)
            ->get();

        return response()->json(
            [
                'response' => [
                    'recipes' => $recipes->toArray()
                ]
            ], 200
        );
    }

    /**
     * Create recipe for user
     *
     * @param Request $request    Form input
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, CookbookController $cookbook)
    {
        $response = [];

        $this->validate(
            $request, [
                'name' => 'required',
                'ingredients' => 'required',
                'url' => 'required',
                'description' => 'required',
                'cookbookId' => 'required'
            ]
        );

        $recipe = new Recipe();

        $recipe->name = $request->input('name');
        $recipe->ingredients = $request->input('ingredients');
        $recipe->imgUrl = $request->input('url');
        $recipe->description = $request->input('description');
        $recipe->user_id = $this->user->id;

        $cookbookExist = $cookbook::cookbookExist($request->input('cookbookId'));

        if (! $cookbookExist) {
            $response['error'] = 'Cookbook not found';
            $response['status'] = 404;
        } else {
            $recipe->cookbook_id = $request->input('cookbookId');

            if ($recipe->save()) {
                $response['created'] = true;
                $response['recipeId'] = $recipe->id;
                $response['status'] = 201;
            }
        }

        return response()->json(
            [
                'response' => $response
            ], $response["status"]
        );
    }

    /**
     * Update Recipe
     *
     * @param Request $request  request
     * @param int     $recipeId recipeId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $recipeId)
    {
        $response = [];

        $recipe = self::recipeExist($recipeId);

        if (! $recipe || $recipe === null) {
            $response["error"] = 'Recipe does not exist.';
            $response["status"] = 404;
        } else {
            $fields = $request->only(
                'name',
                'ingredients',
                'url',
                'description'
            );

            foreach ($fields as $key => $val) {
                if ($val !== null || !is_null($val)) {
                    $recipe->$key = $val;
                }
            }

            try {
                if ($recipe->save()) {
                    $response["updated"] = true;
                    $response["status"] = 200;
                }
            } catch (Exception $e) {
                $response["error"] = $e->getMessage();
                $response["status"] = 422;
            }
        }

        return response()->json(
            [
                'response' => $response
            ], $response["status"]
        );
    }

    /**
     * Find the cookbook
     *
     * @param int $recipeId $recipeId
     *
     * @return mixed
     */
    protected static function recipeExist($recipeId)
    {
        return Recipe::find($recipeId) ?? false;
    }
}
