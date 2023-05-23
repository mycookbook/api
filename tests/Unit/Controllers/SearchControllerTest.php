<?php

declare(strict_types=1);

namespace Unit\Controllers;

use App\Http\Controllers\SearchController;
use App\Http\Requests\SearchRequest;
use App\Models\Cookbook;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class SearchControllerTest extends \TestCase
{
    public function test_it_is_instantiable()
    {
        $searchController = new SearchController();

        $this->assertInstanceOf(Controller::class, $searchController);
    }

    public function test_it_responds_with_422_if_request_validation_fails()
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('all')->andReturn([]);

        $searchController = new SearchController();

        $response = $searchController->getSearchResults($request);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('errors', $decoded);
        $this->assertArrayHasKey('query', $decoded["errors"]);
        $this->assertSame("The query field is required.", $decoded["errors"]["query"][0]);
        $this->assertSame($response->getStatusCode(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_it_can_return_search_results_for_the_tag_cookbooks_query_syntax()
    {
        $user = User::factory()->make();

        $user->save();

        $user_id = $user->refresh()->getKey();

        $cookbook = Cookbook::factory()->make([
            'name' => 'test',
            'user_id' => $user_id,
            'tags' => 'seasonal,fresh,nasty',
            'resource_type' => 'cookbook'
        ]);

        $cookbook->save();

        $search = new SearchController();

        $mockRequest = $this->mock(SearchRequest::class);

        $queryString = ':tags|cookbooks seasonal';

        $mockRequest
            ->shouldReceive('all')
            ->andReturn(['query' => $queryString]);

        $mockRequest
            ->shouldReceive('get')
            ->with('query')
            ->andReturn($queryString);

        $response = $search->getSearchResults($mockRequest);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('response', $decoded);

        $this->assertSame("test", $decoded['response'][0]['name']);

        foreach ($decoded['response'] as $key => $response) {
            $this->assertArrayHasKey('id', $response);
            $this->assertSame($response['id'], $cookbook->getKey());

            $this->assertArrayHasKey('name', $response);
            $this->assertSame($response['name'], $cookbook->name);

            $this->assertArrayHasKey('description', $response);
            $this->assertSame($response['description'], $cookbook->description);

            $this->assertArrayHasKey('bookCoverImg', $response);
            $this->assertSame($response['bookCoverImg'], $cookbook->bookCoverImg);

            $this->assertArrayHasKey('created_at', $response);
            $this->assertSame($response['created_at'], $cookbook->created_at);

            $this->assertArrayHasKey('updated_at', $response);

            $this->assertArrayHasKey('slug', $response);
            $this->assertSame($response['slug'], $cookbook->slug);

            $this->assertArrayHasKey('flag_id', $response);
            $this->assertSame($response['flag_id'], $cookbook->flag_id);

            $this->assertArrayHasKey('resource_type', $response);
            $this->assertSame($response['resource_type'], $cookbook->resource_type);

            $this->assertArrayHasKey('is_locked', $response);
            $this->assertSame($response['is_locked'], $cookbook->is_locked);

            $this->assertArrayHasKey('alt_text', $response);
            $this->assertSame($response['alt_text'], $cookbook->alt_text);

            $this->assertArrayHasKey('tags', $response);
            $this->assertSame($response['tags'], $cookbook->tags);

            $this->assertArrayHasKey('recipes_count', $response);
            $this->assertSame($response['recipes_count'], $cookbook->recipes_count);

            $this->assertArrayHasKey('categories', $response);
            $this->assertSame(count($response['categories']), $cookbook->categories()->get()->count());

            $this->assertArrayHasKey('author', $response);
            $this->assertSame($response['author']['name'], $cookbook->author()->name);

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
