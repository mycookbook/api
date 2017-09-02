<?php
/**
 * RecipeController
 */

namespace App\Http\Controllers;

use App\Recipe;
use Illuminate\Support\Facades\Auth;
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
    }
    /**
     * Get all recipes belonging to a user
     *
     * @param int $user_id userid
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $recipes = Recipe::where('user_id', $this->jwt->toUser()->id)->get();

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
     * @param int     $cookbookId cookbook
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $cookbookId)
    {
        $response = [];

        $this->validate(
            $request, [
                'name' => 'required',
                'ingredients' => 'required',
                'url' => 'required',
                'description' => 'required'
            ]
        );

        $recipe = new Recipe();

        if (! $user = $this->jwt->parseToken()->authenticate() ) {
            return response()->json(
                [
                    'msg' => 'user not authenticated'
                ]
            );
        }

        $recipe->name = $request->input('name');
        $recipe->ingredients = $request->input('ingredients');
        $recipe->imgUrl = $request->input('url');
        $recipe->description = $request->input('description');
        $recipe->user_id = $user->id;
        $recipe->cookbook_id = $cookbookId;

        if ($recipe->save()) {
            $response =  response()->json(
                [
                    'response' => [
                        'created' => true,
                        'recipeId' => $recipe->id
                    ]
                ], 201
            );
        }

        return $response;
    }
}
