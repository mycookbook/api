<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ApiException;
use App\Exceptions\CookbookModelNotFoundException;
use App\Exceptions\InvalidPayloadException;
use App\Interfaces\serviceInterface;
use App\Models\Cookbook;
use App\Models\Draft;
use App\Models\Flag;
use App\Models\Recipe;
use App\Utils\DbHelper;
use App\Utils\IngredientMaker;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;

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
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws CookbookModelNotFoundException
     * @throws InvalidPayloadException
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->ownRecipe($id)) {
            $payload = $request->only($this->getFillables());

            $this->validatePayload($payload);

            $recipe = $this->get($id);

            if ($name = Str::replace('-', " ", Arr::get($payload, 'name'))) {
                if ($name != $recipe->name) {
                    $payload['slug'] = DbHelper::generateUniqueSlug($name, 'recipes', 'slug');
                }
                $payload['name'] = $name;
            }

            if ($nationality = Arr::get($payload, "nationality")) {
                $payload['nationality'] = Flag::where(["id" => $nationality])
                ->orWhere(["flag" => $nationality])
                ->orWhere(["nationality" => $nationality])
                ->first()
                ->getKey();
            }

            return response(
                [
                    'updated' => $recipe->update($payload),
                ], Response::HTTP_OK
            );
        }

        throw new UnauthorizedException("You are not authorized to perform this action.");
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

    private function validatePayload(array $payload)
    {
        $sources = [];

        //cookbook must exist
        if (!Cookbook::find($payload['cookbook_id'])) {
            $sources[] = [
                'cookbook_id' => $payload['cookbook_id'] . ' does not exist.'
            ];
        }

        //nationality must exist
        $nationality = Arr::get($payload, 'nationality');
        If (!Flag::where(["id" => $nationality])
            ->orWhere(["flag" => $nationality])
            ->orWhere(["nationality" => $nationality])
            ->first()) {
            $sources[] = [
                'nationality' => 'This nationality is unrecognized.'
            ];
        }

        //imgUrl
        If ($imgUrl = Arr::get($payload, 'imgUrl')) {
            try {
                exif_imagetype($imgUrl);
            } catch (\Exception $e) {
                $sources[] = [
                    'imgUrl' => $imgUrl . ' is not a valid image url.'
                ];
            }
        }

        //descriptin length
        If ($description = Arr::get($payload, 'description')) {
            //todo: ai enabled geberrish detection
            if (Str::wordCount($description) < 100) {
                $sources[] = [
                    'description' =>'Description must not be less than 100 words.'
                ];
            }
        }

        //summary length
        If ($summary = Arr::get($payload, 'summary')) {
            //todo: ai enabled geberrish detection
            if (Str::wordCount($summary) < 50) {
                $sources[] = [
                    'summary' => 'Summary must not be less than 50 words.'
                ];
            }
        }

        //ingredients must be a list
        If ($ingredients = Arr::get($payload, 'ingredients')) {
            If (!is_array($ingredients)) {
                $sources[] = [
                    'ingredients' => 'Ingredients must be a list.'
                ];
            }

            foreach($ingredients as $key => $value) {
                $keys = array_keys($value);
                if ($keys !== ['name', 'unit']) {
                    $sources[] = [
                        'ingredients' => [
                            'invalid_keys' => [
                                'position' => $key,
                                'keys' => $keys
                            ]
                        ]
                    ];
                }
            }
        }

        //tags must be a list
        If ($tags = Arr::get($payload, 'tags')) {
            If (!is_array($tags)) {
                $sources[] = [
                    'tags' => 'Tags must be a list.'
                ];
            }
        }

        if ($sources) {
            throw new InvalidPayloadException("The payload is invalid.", ['sources' => $sources]);
        }
    }
}
