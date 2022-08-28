<?php

namespace App\Adapters\Search;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MySqlAdapter implements FulltextSearchAdapterInterface
{
    /**
     * @param  string  $q
     * @return \Illuminate\Support\Collection
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
     */
    private function fetchCookbooks($q): \Illuminate\Support\Collection
    {
        $query = DB::table('cookbooks')
            ->select([
                'cookbooks.id AS cookbook_id',
                'cookbooks.name AS cookbook_name',
                'cookbooks.slug AS cookbook_slug',
                DB::raw('SUBSTR(cookbooks.description,1,250) as description'),
                DB::raw('DATE_FORMAT(cookbooks.created_at, "%d %M %Y") as created_at'),
                'cookbooks.bookCoverImg',
                'cookbooks.resource_type',
                'cookbooks.is_locked',
                'users.name AS author_name',
                'users.name_slug AS username',
                'users.id AS author_id',
            ])
            ->leftJoin('users', 'users.id', '=', 'cookbooks.user_id');

        if ($q === 'cookbooks') {
            return $query->get();
        }

        return $query
            ->whereRaw('MATCH(cookbooks.name,cookbooks.description) AGAINST(? IN BOOLEAN MODE)', [$q])
            ->orWhereRaw('MATCH(users.name) AGAINST(? IN BOOLEAN MODE)', [$q])
            ->get();
    }

    /**
     * @param $q
     * @return \Illuminate\Support\Collection
     */
    private function fetchRecipes($q): \Illuminate\Support\Collection
    {
        return DB::table('recipes')
            ->select([
                'recipes.id as recipe_id',
                'recipes.name AS recipe_name',
                'recipes.slug AS recipe_slug',
                DB::raw('SUBSTR(recipes.summary,1,250) as summary'),
                'recipes.ingredients',
                'recipes.resource_type',
                'recipes.nutritional_detail',
                'recipes.imgUrl',
                'recipes.cookbook_id AS cookbook_id',
                DB::raw('DATE_FORMAT(recipes.created_at, "%d %M %Y") as created_at'),
                'users.name AS author_name',
                'users.name_slug AS username',
                'users.id AS author_id',
            ])
            ->leftJoin('users', 'users.id', '=', 'recipes.user_id')
            ->whereRaw('MATCH(recipes.name,recipes.description,recipes.ingredients,recipes.nutritional_detail,recipes.summary) AGAINST(? IN BOOLEAN MODE)', [$q])
            ->orWhereRaw('MATCH(users.name) AGAINST(? IN BOOLEAN MODE)', [$q])
            ->get();
    }

    /**
     * Get the user meta data and write to a csv file for ML purposes
     *
     * @param  Request  $request
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
