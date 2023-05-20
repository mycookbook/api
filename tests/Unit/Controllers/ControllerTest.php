<?php

declare(strict_types=1);

namespace Unit\Controllers;

use App\Exceptions\CookbookModelNotFoundException;
use App\Http\Controllers\CookbookController;
use App\Services\CookbookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ControllerTest extends \TestCase
{
    public function test_it_is_instantiable()
    {
        $cookbookController = new CookbookController($this->mock(CookbookService::class));
        $this->assertInstanceOf(Controller::class, $cookbookController);
    }

    public function test_it_can_retrieve_all_cokbooks()
    {
        $service = $this->mock(CookbookService::class);
        $expectedResponse = new JsonResponse([]);

        $service
            ->shouldReceive('index')
            ->andReturn($expectedResponse);

        $controller = new CookbookController($service);

        $this->assertSame($expectedResponse,  $controller->index());
    }

    public function test_it_will_throw_cookbook_model_not_found_exception_if_cookbook_does_not_exist()
    {
        $this->expectException(CookbookModelNotFoundException::class);
        $expectedResponse = $this->mock(CookbookModelNotFoundException::class);
        $service = $this->mock(CookbookService::class);

        $service
            ->shouldReceive('show')
            ->andThrow($expectedResponse);

        $controller = new CookbookController($service);
        $controller->show(10);
    }

    public function test_it_can_retrieve_cookbook_by_id()
    {
        $cookbookId = random_int(1, 10);
        $service = $this->mock(CookbookService::class);
        $expectedResponse = new JsonResponse(['data' => new \stdClass()]);

        $service
            ->shouldReceive('show')
            ->with($cookbookId)
            ->andReturn($expectedResponse);

        $controller = new CookbookController($service);
        $this->assertSame($expectedResponse,  $controller->show($cookbookId));
    }
}
