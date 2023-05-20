<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CookbookModelNotFoundException;
use App\Interfaces\serviceInterface;
use App\Models\Category;
use App\Models\Cookbook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class CookbookService
 */
class CookbookService extends BaseService implements serviceInterface
{
    public function __construct()
    {
        $this->serviceModel = new Cookbook();
    }

    /**
     * Return all cookbooks
     */
    public function index($user_id = null)
    {
        $cookbooks = Cookbook::with([
            'categories',
            'flag',
            'recipes',
            'users',
        ]);

        if ($user_id) {
            return response()->json(
                [
                    'data' => $cookbooks
                        ->where('user_id', '=', $user_id)
                        ->take(15)
                        ->orderByDesc('created_at')
                        ->get(),
                ], Response::HTTP_OK
            );
        }

        return response()->json(
            [
                'data' => $cookbooks->take(15)
                    ->orderByDesc('created_at')
                    ->get(),
            ], Response::HTTP_OK
        );
    }

    /**
     * Create cookbook resource
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $categories = explode(",", $request->get('categories'));
        $categories = Category::whereIn('slug', $categories)->pluck('id')->toArray();

        $cookbook = $this->serviceModel;

        $data = $request->all();

        foreach ($this->getFillables() as $fillable) {
            if ($data[$fillable]) {
                $cookbook->$fillable = $data[$fillable];
            }
        }

        /** @phpstan-ignore-next-line  */
        $cookbook->slug = slugify($request->name);

        if ($cookbook->save()) {
            /** @phpstan-ignore-next-line  */
            $cookbook->users()->attach($cookbook->user_id);

            foreach ($categories as $category) {
                /** @phpstan-ignore-next-line  */
                $cookbook->categories()->attach($category);
            }

            return response()->json(
                [
                    'response' => [
                        'created' => true,
                        'data' => $cookbook,
                    ],
                ], Response::HTTP_CREATED
            );
        }

        return response()->json(
            [
                'error' => 'There was an error prcessing this request, please try again.'
            ], Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Update cookbook resource
     *
     * @param $request
     * @param string $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     * @throws CookbookModelNotFoundException
     */
    public function update($request, string $id)
    {
        $cookbook = $this->findWhere($id);

        $data = $request->only([
            'name', 'description', 'bookCoverImg', 'categories', 'alt_text', 'tags'
        ]);

        if (isset($data['tags'])) {
            $exisintgTags = $cookbook->tags;

            if ($exisintgTags) {
                $exisintgTags = array_merge($exisintgTags, explode(",", $data["tags"]));
                $data["tags"] = array_unique($exisintgTags);
            }
        }

        if (isset($data['categories'])) {
            $existingCategories = $cookbook->categories()->get()->pluck("id")->toArray();

            if ($existingCategories) {
                $categories = explode(",", $data['categories']);
                $categories = Category::whereIn('slug', $categories)->pluck('id')->toArray();
                $data['categories'] = array_unique(array_merge($existingCategories, $categories));

                foreach ($data['categories'] as $category_id) {
                    $cookbook->categories()->attach($category_id);
                }
            }
        }

        return response(
            [
                'updated' => $cookbook->update($data),
            ], Response::HTTP_OK
        );
    }

    /**
     * Delete Cookbook resource
     *
     * @param int $id identofier
     *
     * @throws CookbookModelNotFoundException
     */
    public function delete($id)
    {
        $cookbook = $this->findWhere($id);

        return response(
            [
                'deleted' => $cookbook->delete(),
            ], Response::HTTP_ACCEPTED
        );
    }

    /**
     * @param mixed $option
     *
     * @throws CookbookModelNotFoundException
     */
    public function show($option)
    {
        $cookbook = $this->findWhere($option);

        if (!$cookbook) {
            throw new CookbookModelNotFoundException();
        }

        return response(
            [
                'data' => $cookbook,
            ], Response::HTTP_OK
        );
    }

    /**
     * Find cookbook record
     *
     * @param $q
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     *
     * @throws CookbookModelNotFoundException
     */
    public function findWhere($q)
    {
        $record = Cookbook::with('Users')
            ->where('id', $q)
            ->orWhere('slug', $q)
            ->first();

        if (!$record) {
            throw new CookbookModelNotFoundException();
        }

        return $record;
    }
}
