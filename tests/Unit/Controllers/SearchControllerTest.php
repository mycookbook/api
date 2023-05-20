<?php

declare(strict_types=1);

namespace Unit\Controllers;

use App\Http\Controllers\SearchController;
use App\Http\Requests\SearchRequest;
use App\Models\Cookbook;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Routing\Controller;

class SearchControllerTest extends \TestCase
{
    public function test_it_is_instantiable()
    {
        $searchController = new SearchController();

        $this->assertInstanceOf(Controller::class, $searchController);
    }

    public function test_it_can_return_search_results_for_the_tag_cookbooks_query_syntax()
    {
        $user = User::factory()->make();

        $user->save();

        $user_id = $user->refresh()->getKey();

        $cookbook = Cookbook::factory()->make([
            'name' => 'test',
            'user_id' => $user_id,
            'tags' => 'seasonal,fresh,nasty'
        ]);

        $cookbook->save();

        $search = new SearchController();

        $response = $search->getSearchResults(new SearchRequest([
            'query' => ':tags|cookbooks seasonal'
        ]));

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('response', $decoded);

        $this->assertSame("test", $decoded['response'][0]['name']);

        foreach ($decoded['response'] as $key => $response) {
            $this->assertArrayHasKey('id', $response);
            $this->assertArrayHasKey('name', $response);
            $this->assertArrayHasKey('description', $response);
            $this->assertArrayHasKey('bookCoverImg', $response);
            $this->assertArrayHasKey('created_at', $response);
            $this->assertArrayHasKey('updated_at', $response);
            $this->assertArrayHasKey('slug', $response);
            $this->assertArrayHasKey('flag_id', $response);
            $this->assertArrayHasKey('resource_type', $response);
            $this->assertArrayHasKey('is_locked', $response);
            $this->assertArrayHasKey('alt_text', $response);
            $this->assertArrayHasKey('tags', $response);
            $this->assertArrayHasKey('recipes_count', $response);
            $this->assertArrayHasKey('categories', $response);
            $this->assertArrayHasKey('author', $response);
            $this->assertArrayHasKey('_links', $response);
        }
    }

    public function test_it_can_return_search_results_for_the_tag_recipes_query_syntax()
    {
        $user = User::factory()->make();

        $user->save();

        $user_id = $user->refresh()->getKey();

        $cookbook = Cookbook::factory()->make([
            'name' => 'test',
            'user_id' => $user_id
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $user->getKey(),
            'tags' => ['seasonal', 'fresh', 'breakfast'],
            'ingredients' => json_encode([])
        ]);

        $recipe->save();

        $search = new SearchController();

        $response = $search->getSearchResults(new SearchRequest([
            'query' => ':tags|recipes seasonal'
        ]));

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('response', $decoded);

        foreach ($decoded['response'] as $key => $response) {
            $this->assertArrayHasKey('id', $response);
            $this->assertArrayHasKey('name', $response);
            $this->assertArrayHasKey('description', $response);
            $this->assertArrayHasKey('imgUrl', $response);
            $this->assertArrayHasKey('cookbook_id', $response);
            $this->assertArrayHasKey('created_at', $response);
            $this->assertArrayHasKey('updated_at', $response);
            $this->assertArrayHasKey('nutritional_detail', $response);
            $this->assertArrayHasKey('slug', $response);
            $this->assertArrayHasKey('calorie_count', $response);
            $this->assertArrayHasKey('cook_time', $response);
            $this->assertArrayHasKey('servings', $response);
            $this->assertArrayHasKey('claps', $response);
            $this->assertArrayHasKey('resource_type', $response);
            $this->assertArrayHasKey('prep_time', $response);
            $this->assertArrayHasKey('cuisine', $response);
            $this->assertArrayHasKey('course', $response);
            $this->assertArrayHasKey('is_orderable', $response);
            $this->assertArrayHasKey('ingredients', $response);
            $this->assertArrayHasKey('summary', $response);
            $this->assertArrayHasKey('tags', $response);
            $this->assertArrayHasKey('total_time', $response);
            $this->assertArrayHasKey('varieties_count', $response);
            $this->assertArrayHasKey('_links', $response);
            $this->assertArrayHasKey('author', $response);
        }
    }

    public function test_it_can_return_search_results_for_no_query_syntax()
    {
        $user = User::factory()->make();

        $user->save();

        $user_id = $user->refresh()->getKey();

        $cookbook = Cookbook::factory()->make([
            'name' => 'test',
            'user_id' => $user_id
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'name' => 'breakfast delights',
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $user->getKey(),
            'ingredients' => json_encode([])
        ]);

        $recipe->save();

        $search = new SearchController();

        $response = $search->getSearchResults(new SearchRequest([
            'query' => 'breakfast'
        ]));

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('response', $decoded);

        //in this case, recipe because we know for sure results will be returned for recipes based on test data

        foreach ($decoded['response'] as $key => $response) {
            $this->assertArrayHasKey('id', $response);
            $this->assertArrayHasKey('name', $response);
            $this->assertArrayHasKey('description', $response);
            $this->assertArrayHasKey('imgUrl', $response);
            $this->assertArrayHasKey('cookbook_id', $response);
            $this->assertArrayHasKey('created_at', $response);
            $this->assertArrayHasKey('updated_at', $response);
            $this->assertArrayHasKey('nutritional_detail', $response);
            $this->assertArrayHasKey('slug', $response);
            $this->assertArrayHasKey('calorie_count', $response);
            $this->assertArrayHasKey('cook_time', $response);
            $this->assertArrayHasKey('servings', $response);
            $this->assertArrayHasKey('claps', $response);
            $this->assertArrayHasKey('resource_type', $response);
            $this->assertSame('recipe', $response["resource_type"]);
            $this->assertArrayHasKey('prep_time', $response);
            $this->assertArrayHasKey('cuisine', $response);
            $this->assertArrayHasKey('course', $response);
            $this->assertArrayHasKey('is_orderable', $response);
            $this->assertArrayHasKey('ingredients', $response);
            $this->assertArrayHasKey('summary', $response);
            $this->assertArrayHasKey('tags', $response);
            $this->assertArrayHasKey('_links', $response);
            $this->assertArrayHasKey('author', $response);
        }
    }
}
