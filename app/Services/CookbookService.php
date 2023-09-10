<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CookbookModelNotFoundException;
use App\Interfaces\serviceInterface;
use App\Models\Category;
use App\Models\Cookbook;
use App\Utils\DbHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class CookbookService
 */
class CookbookService extends BaseService implements serviceInterface
{
    public function __construct()
    {
        $this->serviceModel = new Cookbook();
    }

    public function index($user_id = null)
    {
        $cookbooks = DB::table('cookbooks')->get();

        if ($user_id !== null) {
            return $cookbooks->where('user_id', '=', $user_id);
        }

        return $cookbooks->map(function($cookbook) {
            $category_ids = DB::table('category_cookbook')
                ->where('cookbook_id', '=', $cookbook->id)
                ->pluck('category_id');
            $categories = DB::table('categories')->whereIn('id', $category_ids->toArray())->get();

            $flag = DB::table('flags')->where('id', '=', $cookbook->flag_id)->pluck('flag', 'nationality');
            $recipes = DB::table('recipes')->where('cookbook_id', '=', $cookbook->id)->get();
            $user_ids = DB::table('cookbook_user')->where('cookbook_id', '=', $cookbook->id)->pluck('user_id');
            $users = DB::table('users')->whereIn('id', $user_ids->toArray())->get();

            $cookbook->categories = $categories;
            $cookbook->flag = $flag;
            $cookbook->recipes = $recipes;
            $cookbook->recipes_count = $recipes->count();
            $cookbook->users = $users;
            $cookbook->author = DB::table('users')->where('id', $cookbook->user_id)->get();

            return $cookbook;
        });
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function store(Request $request): bool
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
        $cookbook->slug = DbHelper::generateUniqueSlug($request->name, 'cookbooks', 'slug');

        if ($cookbook->save()) {
            /** @phpstan-ignore-next-line  */
            $cookbook->users()->attach($cookbook->user_id);

            foreach ($categories as $category) {
                /** @phpstan-ignore-next-line  */
                $cookbook->categories()->attach($category);
            }

            return true;
        }

        return false;
    }

    /**
     * @param $request
     * @param string $id
     * @return bool|int
     * @throws CookbookModelNotFoundException
     */
    public function update($request, string $id): bool|int
    {
        $cookbook = $this->findWhere($id);

        $data = $request->only([
            'name', 'description', 'bookCoverImg', 'categories', 'alt_text', 'tags', 'slug'
        ]);

        if (isset($data['tags'])) {
            $existingTags = $cookbook->tags;

            if ($existingTags) {
                $existingTags = array_merge($existingTags, explode(",", $data["tags"]));
                $data["tags"] = array_unique($existingTags);
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

        return $cookbook->update($data);
    }

    /**
     * @param $id
     * @return bool|null
     * @throws CookbookModelNotFoundException
     */
    public function delete($id): bool|null
    {
        $cookbook = $this->findWhere($id);

        return $cookbook->delete();
    }

    /**
     * @param mixed $option
     * @throws CookbookModelNotFoundException
     */
    public function show($option)
    {
        $cookbook = $this->findWhere($option);

        if (!$cookbook) {
            throw new CookbookModelNotFoundException();
        }

        return $cookbook;
    }

    /**
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
