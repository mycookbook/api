<?php

namespace App\Services;

use App\Interfaces\serviceInterface;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryService extends BaseService implements serviceInterface
{
    public function __construct()
    {
        $this->serviceModel = new Category();
    }

    public function index()
    {
        return response()->json(
            [
                'data' => Category::with('Cookbooks')->get(),
            ], Response::HTTP_OK
        );
    }

    public function show($option)
    {
        // TODO: Implement show() method.
    }

    public function store(Request $request)
    {
        // TODO: Implement store() method.
    }

    public function update(Request $request, $option)
    {
        // TODO: Implement update() method.
    }

    public function findWhere($q)
    {
        // TODO: Implement get() method.
    }
}
