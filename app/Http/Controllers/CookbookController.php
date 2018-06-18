<?php

namespace App\Http\Controllers;

use App\Cookbook;
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

     * @param CookbookRepository $cookbook cookbookRepository
     *
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function __construct(CookbookRepository $cookbook)
    {
        $this->middleware('jwt.auth', ['except' => ['index']]);
        $this->cookbook = $cookbook;
    }

    /**
     * Return all the cookbooks and associated resipes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->cookbook->index();
    }

    /**
     * Create cookbook for user
     *
     * @param Request $request Form input
     * @param JWTAuth $jwt     jwt-auth
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, JWTAuth $jwt)
    {
        $this->validate(
            $request, [
                'name' => 'required',
                'description' => 'required|min:126',
                'bookCoverImg' => 'required|url',
                'flag' => 'required'
            ]
        );

        $user = $jwt->parseToken()->authenticate();

        return $this->cookbook->store($request, $user);
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

    /**
     * Find resource
     *
     * @param int $id identifier
     *
     * @return mixed
     */
    public function find($id)
    {
        try {
            $response = Cookbook::with('Users')->findOrFail($id);
        } catch(\Exception $e) {
            $response = response(
                [
                    'error' => $e->getMessage(),
                ], 404
            );
        }

        return $response;
    }
}
