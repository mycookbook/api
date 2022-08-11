<?php

namespace Functional\Integration\Requests\Cookbook;

use App\Http\Controllers\Requests\Cookbook\StoreRequest;
use App\Http\Controllers\Requests\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Traits\CreatesObjects;

class StoreRequestTest extends \TestCase
{
    use CreatesObjects;

    /**
     * @test
     */
    public function it_throws_an_exception_if_the_request_is_empty()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $storeRequest = new StoreRequest(new Request([]));

        $this->assertResponseOk();
    }

    /**
     * @test
     */
    public function it_is_an_instance_of_cookbook_form_request()
    {
        $request = new StoreRequest(new Request([
            'name' => 'sample cookbook',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));

        $this->assertInstanceOf(FormRequest::class, $request);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_title_is_empty()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => '',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_title_is_null()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_description_is_empty()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => '',
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_description_is_null()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_description_is_less_than_126_characters_long()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => Str::random(125),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_bookCoverImg_is_empty()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => Str::random(126),
            'bookCoverImg' => '',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_bookCoverImg_is_null()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => Str::random(126),
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_bookCoverImg_is_not_a_img_valid_url()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://dummuy-image.jpg',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_category_id_is_null()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => null,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_given_category_id_does_not_exist()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => 0,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_flag_id_is_empty()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => '',
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_flag_id_is_null()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
        ]));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_flag_id_does_not_exist()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new StoreRequest(new Request([
            'name' => 'sample title',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => 0,
        ]));
    }

    /**
     * @test
     */
    public function it_returns_the_request_object()
    {
        $requestData = [
            'name' => 'sample cookbook',
            'description' => Str::random(126),
            'bookCoverImg' => 'http://lorempixel.com/400/200/',
            'category_id' => $this->createCategory()->id,
            'categories' => implode(',', [$this->createCategory()->id]),
            'flag_id' => $this->createFlag()->id,
        ];

        $storeRequest = new StoreRequest(new Request($requestData));

        $this->assertInstanceOf(Request::class, $storeRequest->getParams());
        $this->assertSame($requestData['name'], $storeRequest->getParams()->input('name'));
        $this->assertSame($requestData['description'], $storeRequest->getParams()->input('description'));
        $this->assertSame($requestData['bookCoverImg'], $storeRequest->getParams()->input('bookCoverImg'));
        $this->assertSame($requestData['flag_id'], $storeRequest->getParams()->input('flag_id'));
    }
}
