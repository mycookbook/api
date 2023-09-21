<?php

declare(strict_types=1);

namespace Unit;

use App\Http\Controllers\DefinitionsController;
use App\Http\Controllers\StaticContentController;
use App\Models\Definition;
use Illuminate\Http\Request;

class StaticContentsTest extends \TestCase
{
    /**
     * @test
     */
    public function it_returns_definitions_data()
    {
        $this->seed('Database\\Seeders\\DefinitionsSeeder');
        $controller = new DefinitionsController();

        $definitions = $controller->index();
        $this->assertNotEmpty($definitions->all());

        $definitions->map(function ($defintion) {
            $this->assertInstanceOf(Definition::class, $defintion);
        });
    }

    /**
     * @test
     */
    public function it_returns_static_contents_data()
    {
        $this->seed('Database\Seeders\StaticContentsSeeder');
        $controller = new StaticContentController();

        $staticContents = $controller->get(new Request());
        $decoded = json_decode($staticContents->getContent(), true);

        $this->assertArrayHasKey('cookiePolicy', $decoded['response']);
        $this->assertArrayHasKey('usagePolicy', $decoded['response']);
        $this->assertArrayHasKey('dataRetentionPolicy', $decoded['response']);
        $this->assertArrayHasKey('termsAndConditions', $decoded['response']);
    }
}
