<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected CategoryService $service;

    /**
     * @param  \App\Services\CategoryService  $service
     */
    public function __construct(CategoryService $service)
    {
        $this->middleware('jwt.auth', ['except' => ['index']]);

        $this->service = $service;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->service->index();
    }
}
