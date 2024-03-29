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
use App\Models\User;
use App\Utils\DbHelper;
use App\Utils\IngredientMaker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Class RecipeService
 */
class RecipeService extends BaseService implements serviceInterface
{
    public function __construct()
    {
        $this->serviceModel = new Recipe();
    }

    public function index($user_id = null)
    {
        $recipes = Recipe::paginate(100);

        $recipes = $recipes->filter(function ($recipe) {
            return !$recipe->is_draft;
        });

        return $user_id ? $recipes->where('user_id', '=', $user_id) : $recipes;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws CookbookModelNotFoundException
     */
    public function show($id)
    {
        return $this->get($id) ?: null;
    }

    /**
     * @param $request
     * @return bool
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

                return $draft->save();
            }

            return $created;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return bool|int|void
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

            return $recipe->update($payload);
        }
    }

    /**
     * @param User $user
     * @param $id
     * @return bool|mixed|void|null
     * @throws CookbookModelNotFoundException
     */
    public function delete(User $user, $id)
    {
        if ($user->isSuper()) {
            $recipe = $this->get($id);

            return $recipe->delete();
        }
    }

    /**
     * @param $recipeId
     * @return false|\Illuminate\Database\Eloquent\Model
     * @throws CookbookModelNotFoundException
     */
    public function addClap($recipeId)
    {
        $recipe = $this->get($recipeId);

        $recipe->claps = $recipe->claps + 1;

        if ($recipe->save()) {
            return $recipe->refresh();
        }

        return false;
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
        if ($cookbook_id = Arr::get($payload, 'cookbook_id')) {
            if (!Cookbook::find($cookbook_id)) {
                $sources[] = [
                    'cookbook_id' => 'This cookbook does not exist.'
                ];
            }
        }

        //nationality must exist
        if ($nationality = Arr::get($payload, 'nationality')) {
            If (!Flag::where(["id" => $nationality])
                ->orWhere(["flag" => $nationality])
                ->orWhere(["nationality" => $nationality])
                ->first()) {
                $sources[] = [
                    'nationality' => 'This nationality is unrecognized.'
                ];
            }
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

        //description length
        If ($description = Arr::get($payload, 'description')) {
            //todo: ai enabled gibberish detection
            if (Str::wordCount($description) < 100) {
                $sources[] = [
                    'description' =>'Description must not be less than 100 words.'
                ];
            }
        }

        //summary length
        If ($summary = Arr::get($payload, 'summary')) {
            //todo: ai enabled gibberish detection
            if (Str::wordCount($summary) < 50) {
                $sources[] = [
                    'summary' => 'Summary must not be less than 50 words.'
                ];
            }
        }

        //ingredients must be a list
        If ($ingredients = Arr::get($payload, 'ingredients')) {
            $invalid_keys = [];
            If (!is_array($ingredients)) {
                $sources[] = [
                    'ingredients' => 'Ingredients must be a list.'
                ];
            }

            foreach($ingredients as $key => $value) {
                $keys = array_keys($value);
                if ($keys !== ['name', 'unit', 'thumbnail']) {
                    $invalid_keys[] =  [
                        'item_'. $key => $value
                    ];
                }
            }

            if ($invalid_keys) {
                $invalid_keys[] = ["expected_keys" => ['name', 'unit', 'thumbnail']];
                $sources[]['ingredients'] = [
                    "invalid keys",
                    $invalid_keys
                ];
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
