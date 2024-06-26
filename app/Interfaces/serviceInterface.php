<?php

declare(strict_types=1);

namespace App\Interfaces;

use Illuminate\Http\Request;

interface serviceInterface
{
    /**
     * @return mixed
     */
    public function index();

    /**
     * @param string|int $option
     * @return mixed
     */
    public function show(string|int $option);

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request);

    /**
     * @param Request $request
     * @param string $option
     * @return mixed
     */
    public function update(Request $request, string $option);

    /**
     * @param string|int $q
     * @return mixed
     */
    public function findWhere(string|int $q);
}
