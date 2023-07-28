<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ApiException;
use App\Exceptions\CookbookModelNotFoundException;
use App\Interfaces\serviceInterface;
use App\Models\Cookbook;
use App\Models\Draft;
use App\Models\Flag;
use App\Models\Recipe;
use App\Utils\DbHelper;
use App\Utils\IngredientMaker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class RecipeService
 */
class RecipeService extends BaseService implements serviceInterface
{
    public function __construct()
    {
        $this->serviceModel = new Recipe();
    }

    /**
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($user_id = null): \Illuminate\Http\JsonResponse
    {
        $recipes = Recipe::paginate(100);

        $recipes = $recipes->filter(function ($recipe) {
            return !$recipe->is_draft;
        });

        if ($user_id) {
            return response()->json(
                [
                    'data' => $recipes->where('user_id', '=', $user_id),
                ], Response::HTTP_OK
            );
        }

        return response()->json(['data' => $recipes]);
    }

    /**
     * Retrieve one Recipe
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     *
     * @throws CookbookModelNotFoundException
     */
    public function show($id)
    {
        $recipe = $this->get($id);

        if (!$recipe) {
            throw new CookbookModelNotFoundException();
        }

        return $recipe;
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     * @throws ApiException
     */
    public function store($request)
    {
        try {
            $user = $request->user();

            $payload = $request->only([
                'is_draft',
                'name',
                'imgUrl',
                'description',
                'cookbook_id',
                'summary',
                'ingredients',
                'nationality',
                'tags',
                'cuisine'
            ]);

            $payload['slug'] = DbHelper::generateUniqueSlug($request->name, 'recipes', 'slug');
            $payload['user_id'] = $user->id;
            $payload['nutritional_detail'] = json_encode([]);
            $payload['prep_time'] = Carbon::now()->toDateTimeString();
            $payload['cook_time'] = Carbon::now()->toDateTimeString();
            $payload['course'] = 'main';
            $payload['ingredients'] = IngredientMaker::format($payload['ingredients']);

            if ($payload["tags"]) {
                $tagsArray = collect(explode(",", trim($payload["tags"])));
                $tagsArray = $tagsArray->map(function($tag) {
                    return trim($tag);
                });

                $payload["tags"] = $tagsArray->toArray();
            }

            $payload["nationality"] = Flag::where(["flag" => $payload["nationality"]])->first()->getKey();

            $recipe = new Recipe($payload);

            $cookbook = Cookbook::findOrfail($request->cookbook_id);
            $recipe->cookbook_id = $cookbook->id;

            $created = $recipe->save();

            if ($payload['is_draft'] == "true") {
                $draft = new Draft([
                    'resource_id' => $recipe->refresh()->getKey(),
                    'resource_type' => 'recipe'
                ]);

                $draft->save();
            }

            return response([
                'created' => $created,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     * @throws CookbookModelNotFoundException
     */
    public function update(Request $request, $id)
    {
        //TODO: if user dont own recipe, can update it

        $recipe = $this->get($id);
        //		$recipe->prep_time

        $payload = $request->all();
        $payload['slug'] = DbHelper::generateUniqueSlug($payload['name'], 'recipes', 'slug');

        return response(
            [
                'updated' => $recipe->update($payload),
            ], Response::HTTP_OK
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     * @throws CookbookModelNotFoundException
     */
    public function delete($id)
    {
        //TODO: if user dont own recipe, cannot delete it

        $recipe = $this->get($id);

        return response(
            [
                'deleted' => $recipe->delete(),
            ], Response::HTTP_ACCEPTED
        );
    }

    /**
     * @param $recipeId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     * @throws CookbookModelNotFoundException
     */
    public function addClap($recipeId)
    {
        $recipe = $this->get($recipeId);

        $recipe->claps = $recipe->claps + 1;
        $recipe->save();

        return response(
            [
                'updated' => true,
                'claps' => $recipe->claps,
            ], Response::HTTP_OK
        );
    }

    /**
     * @param $q
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     * @throws CookbookModelNotFoundException
     */
    public function get($q)
    {
        $record = Recipe::with('User', 'Cookbook')
            ->where('id', $q)
            ->orWhere('slug', $q)
            ->first();

        if (!$record) {
            throw new CookbookModelNotFoundException();
        }

        return $record;
    }

    public function findWhere($q)
    {
        // TODO: Implement findWhere() method.
    }
}
