<?php

namespace App\Http\Controllers;

use App\Exceptions\CookbookModelNotFoundException;
use App\Http\Requests\CookbookStoreRequest;
use App\Models\User;
use App\Services\CookbookService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWT;

/**
 * Class UserController
 */
class CookbookController extends Controller
{
    /**
     * @param CookbookService $service
     */
    public function __construct(CookbookService $service)
    {
        $this->middleware('auth.guard')->except(['index', 'show']);

        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->service->index();
    }

    /**
     * @param mixed $id
     * @return Response|ResponseFactory
     *
     * @throws CookbookModelNotFoundException
     */
    public function show($id)
    {
        return $this->service->show($id);
    }

    /**
     * @param Request $request
     * @param JWT $jwtAuth
     * @return JsonResponse
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function myCookbooks(Request $request, JWT $jwtAuth): JsonResponse
    {
        if ($jwtAuth->parseToken()->check()) {
            return $this->service->index($request->get('user_id'));
        }

        return response()->json([
            'error', 'You are not authorized to access this resource.'
        ], 401);
    }

    /**
     * @param CookbookStoreRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(CookbookStoreRequest $request)
    {
        try {
            $request->merge([
                'user_id' => Auth::user()->id,
                'alt_text' => 'cookbook cover image'
            ]);

            return $this->service->store($request);

        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @param JWT $jwtAuth
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|Response
     * @throws CookbookModelNotFoundException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function update(int $id, Request $request, JWT $jwtAuth)
    {
        if (
            $request->user()->ownCookbook($id) &&
            $jwtAuth->parseToken()->check()
        ) {
            return $this->service->update($request, $id);
        }

        return response()->json([
            'error' => 'You are not authorized to access this resource.'
        ], 401);
    }

    /**
     * @param $cookbookId
     * @param Request $request
     * @param JWT $jwtAuth
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|Response
     * @throws CookbookModelNotFoundException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function destroy($cookbookId, Request $request, JWT $jwtAuth)
    {
        if (
            $request->user()->isSuper() &&
            $jwtAuth->parseToken()->check()
        ) {
            return $this->service->delete($cookbookId);
        }

        return response()->json([
            'error' => 'You are not authorized to perform this action.'
        ], 401);
    }
}
