<?php

namespace App\Http\Contracts;

/**
 * Interface iResource
 */
interface Repository
{
    /**
     * Index method
     *
     * @param JWTAuth $jwt jwt
     *
     * @return mixed
     */
    public function index($jwt);

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