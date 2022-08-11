<?php

namespace Functional\Integration\Services;

use App\Exceptions\CookbookModelNotFoundException;
use App\Http\Controllers\Requests\Cookbook\StoreRequest;
use App\Services\CookbookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CookbookServiceTest extends \TestCase
{
    /**
     * @test
     */
    public function it_responds_with_a_200_when_retrieving_all_cookbooks()
    {
        $service = new CookbookService();
        $response = $service->index();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_an_unauthenticated_user_attempts_to_create_a_cookbook()
    {
        $this->expectException(ValidationException::class);

        $category = $this->createCategory();
        $flag = $this->createFlag();

        $request = new StoreRequest(new Request([
            'name' => 'sample cookbook',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'categories' => json_encode([$category->id]),
            'flag_id' => $flag->id,
        ]));

        $service = new CookbookService();
        $service->store($request->getParams());
    }

    /**
     * @test
     */
    public function it_responds_with_a_201_when_an_authenticated_user_attempts_to_create_a_cookbook()
    {
        $this->markTestSkipped('must be revisited.');

        $request = new Request([
            'name' => 'sample title',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'categories' => json_encode([$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]);

        $request->setUserResolver(function () {
            return $this->createUser();
        });

        $cookbookStoreRequest = new StoreRequest($request);
        $cookbookService = new CookbookService();

        $response = $cookbookService->store($cookbookStoreRequest->getParams());
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_responds_with_a_cookbook_instance_when_retrieving_a_cookbook_that_exists()
    {
        $cookbook = $this->createCookbook();

        $service = new CookbookService();
        $response = $service->show($cookbook->id);

        $decoded = json_decode($response->getContent(), true);

        $this->assertSame($decoded['data']['name'], $cookbook->name);
        $this->assertSame($decoded['data']['description'], $cookbook->description);
        $this->assertSame($decoded['data']['bookCoverImg'], $cookbook->bookCoverImg);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_retrieving_a_cookbook_that_does_not_exist()
    {
        $this->expectException(CookbookModelNotFoundException::class);

        $service = new CookbookService();
        $service->show(0);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_trying_to_update_a_cookbook_that_does_not_exist()
    {
        $this->expectException(CookbookModelNotFoundException::class);

        $service = new CookbookService();

        $request = new Request([
            'name' => 'new title',
        ]);

        $service->update($request, 0);
    }

    /**
     * @test
     */
    public function it_responds_with_a_200_when_trying_to_update_a_cookbook_that_exists()
    {
        $cookbook = $this->createCookbook();

        $service = new CookbookService();

        $request = new Request([
            'name' => 'new title',
        ]);

        $response = $service->update($request, $cookbook->id);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->seeInDatabase('cookbooks', [
            'id' => $cookbook->id,
            'name' => 'new title',
        ]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_trying_to_delete_a_cookbook_that_does_not_exist()
    {
        $this->expectException(CookbookModelNotFoundException::class);

        $service = new CookbookService();

        $service->delete(0);
    }

    /**
     * @test
     */
    public function it_responds_with_a_202_when_trying_to_delete_a_cookbook_that_exists()
    {
        $cookbook = $this->createCookbook();

        $service = new CookbookService();

        $response = $service->delete($cookbook->id);

        $this->assertSame(Response::HTTP_ACCEPTED, $response->getStatusCode());
        $this->notSeeInDatabase('cookbooks', [
            'id' => $cookbook->id,
        ]);
    }
}
