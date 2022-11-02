<?php

namespace Unit\Controllers;

use App\Adapters\Search\MySqlAdapter;
use App\Http\Controllers\SearchController;
use App\Http\Requests\SearchRequest;
use App\Models\Cookbook;
use App\Models\User;
use Illuminate\Routing\Controller;

class SearchControllerTest extends \TestCase
{
    public function test_it_is_instantiable()
    {
        $search = new SearchController(new MySqlAdapter());

        $this->assertInstanceOf(Controller::class, $search);
    }

    public function test_it_can_return_search_results_for_the_given_query()
    {
        $this->markTestSkipped();

        $user = User::factory()->make();
        $user->save();

        $user_id = $user->refresh()->getKey();

        $cookbook = Cookbook::factory()->make([
            'name' => 'test',
            'user_id' => $user_id
        ]);

        $cookbook->save();

        $search = new SearchController(new MySqlAdapter());

        $response = $search->getSearchResults(new SearchRequest([
            'query' => 'test'
        ]));

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('response', $decoded);

        $this->assertSame("test", $decoded['response'][0]['cookbook_name']);
    }
}
