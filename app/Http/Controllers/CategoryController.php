<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;

class CategoryController extends Controller
{
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
