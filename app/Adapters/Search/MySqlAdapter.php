<?php

declare(strict_types=1);

namespace App\Adapters\Search;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MySqlAdapter implements FulltextSearchAdapterInterface
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * @param string $q
     * @return \Illuminate\Support\Collection
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function fetch(string $q): \Illuminate\Support\Collection
    {
        $by_cookbook = 'cookbooks by';
        $by_recipe = 'recipes by';

        if (str_contains($q, $by_cookbook)) {
            $q = trim(str_replace($by_cookbook, '', $q));

            return $this->fetchCookbooks($q);
        }

        if (str_contains($q, $by_recipe)) {
            $q = trim(str_replace($by_recipe, '', $q));

            return $this->fetchRecipes($q);
        }

        return $this->fetchCookbooks($q)->merge($this->fetchRecipes($q));
    }

    /**
     * @param $q
     * @return \Illuminate\Support\Collection
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    private function fetchCookbooks($q): \Illuminate\Support\Collection
    {
        $author = $this->userService->findWhere($q)->first();

        $query = DB::table('cookbooks')
            ->select([
                'cookbooks.id AS cookbook_id',
                'cookbooks.name AS cookbook_name',
                'cookbooks.slug AS cookbook_slug',
                'cookbooks.bookCoverImg',
                'cookbooks.resource_type',
                'cookbooks.is_locked',
                'cookbooks.description',
                'cookbooks.created_at',
                'users.name AS author_name',
                'users.name_slug AS username',
                'users.id AS author_id',
            ])
            ->leftJoin('users', 'users.id', '=', 'cookbooks.user_id')
            ->whereFullText('cookbooks.name', $q)
            ->orWhereFullText('cookbooks.description', $q)
            ->orWhereFullText('cookbooks.slug', $q);

        if (!is_null($author)) {
            return $query->orWhere('cookbooks.user_id', '=', $author->getKey())->get();
        }

        return $query->get();
    }

    /**
     * @param $q
     * @return \Illuminate\Support\Collection
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    private function fetchRecipes($q): \Illuminate\Support\Collection
    {
        $author = $this->userService->findWhere($q)->first();

        $query = DB::table('recipes')
            ->select([
                'recipes.id as recipe_id',
                'recipes.name AS recipe_name',
                'recipes.slug AS recipe_slug',
                'recipes.description',
                'recipes.summary',
                'recipes.ingredients',
                'recipes.resource_type',
                'recipes.nutritional_detail',
                'recipes.imgUrl',
                'recipes.cookbook_id AS cookbook_id',
                'recipes.created_at',
                'users.name AS author_name',
                'users.name_slug AS username',
                'users.id AS author_id',
            ])
            ->leftJoin('users', 'users.id', '=', 'recipes.user_id')
            ->whereFullText('recipes.name', $q)
            ->orWhereFullText('recipes.description', $q)
            ->orWhereFullText('recipes.ingredients', $q)
            ->orWhereFullText('recipes.nutritional_detail', $q);

        if (!is_null($author)) {
            return $query->orWhere('recipes.user_id', '=', $author->getKey())->get();
        }

        return $query->get();
    }

    /**
     * Get the user meta data and write to a csv file for ML purposes
     *
     * @param Request $request
     */
    public function writeToCsv(Request $request)
    {
        $csv = $request->only([
            'city',
            'country',
            'ip',
            'keyword',
            'loc',
            'timezone',
        ]);

        $csv['server_time'] = \Carbon\Carbon::now()->toDateTimeString();

        $file_open = fopen('keywords.csv', 'a+');

        fputcsv($file_open, $csv);
    }
}
