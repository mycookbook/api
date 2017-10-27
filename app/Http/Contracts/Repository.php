<?php

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
     * @param int $id identofoer
     *
     * @return mixed
     */
    public function update($id);

    /**
     * Delete method
     *
     * @param int $id identofier
     *
     * @return mixed
     */
    public function delete($id);
}