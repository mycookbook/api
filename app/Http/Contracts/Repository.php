<?php

namespace App\Http\Contracts;

use Illuminate\Http\Request;

/**
 * Interface iResource
 */
interface Repository
{
    /**
     * Index method
     *
     * @return mixed
     */
    public function index();

    /**
     * Update method
     *
     * @param Request $request request
     * @param int     $id      identofoer
     *
     * @return mixed
     */
    public function update($request, $id);

    /**
     * Delete method
     *
     * @param int $id identofier
     *
     * @return mixed
     */
    public function delete($id);
}