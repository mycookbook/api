<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Repositories\CookbookRepository;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class CookbookController extends Controller
{
    protected $cookbook;

    /**
     * Constructor
     *
     * @param JWTAuth            $jwt      jwt
     * @param CookbookRepository $cookbook cookbookRepository
     *
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function __construct(JWTAuth $jwt, CookbookRepository $cookbook)
    {
        $this->jwt = $jwt;
        $this->user = $this->jwt->parseToken()->authenticate();
        $this->cookbook = $cookbook;
    }

    /**
     * Return all the cookbooks and associated resipes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->cookbook->index($this->jwt);
    }

    /**
     * Create cookbook for user
     *
     * @param Request $request Form input
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate(
            $request, [
                'name' => 'required',
                'description' => 'required'
            ]
        );

        return $this->cookbook->store($request, $this->user);
    }

    /**
     * Update cookbook
     *
     * @param Request $request    req
     * @param int     $cookbookId paramname
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $cookbookId)
    {
        return $this->cookbook->update($request, $cookbookId);
    }

    /**
     * Delete a cookbook
     *
     * @param int $cookbookId cookbookId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($cookbookId)
    {
        return $this->cookbook->delete($cookbookId);
    }
}
