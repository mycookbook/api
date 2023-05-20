<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Exceptions\CookbookModelNotFoundException;
use App\Http\Requests\CookbookStoreRequest;
use App\Models\Flag;
use App\Services\CookbookService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 */
#[AllowDynamicProperties] class CookbookController extends Controller
{
    protected CookbookService $service;

    /**
     * @param CookbookService $service
     */
    public function __construct(CookbookService $service)
    {
        $this->middleware('auth.guard')->except(['index', 'show']);

        $this->service = $service;
    }

    /**
     * All cookbooks
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
     * @return JsonResponse
     */
    public function myCookbooks(Request $request): JsonResponse
    {
        return $this->service->index($request->get('user_id'));
    }

    /**
     * @param CookbookStoreRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(CookbookStoreRequest $request)
    {
        try {
            //todo: creation of cookbooks will not be publicly accessible until later releases
//            if (!Auth::user()->isEarlyBird()) {
//                throw new UnauthorizedException("You are not authorized to perform this action.");
//            }

            $request->merge([
                'user_id' => Auth::user()->id,
                'alt_text' => $request->get("alt_text") ?? 'cookbook cover image',
                'flag_id' => Flag::where(["flag" => $request->get("flag_id")])->first()->getKey(),
                'tags' => $request->get("tags") ?? ""
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|Response
     * @throws CookbookModelNotFoundException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function update(int $id, Request $request)
    {
        if (Auth::user()->ownCookbook($id)) {
            return $this->service->update($request, (string) $id);
        }

        return response()->json([
            'error' => 'You are not authorized to access this resource.'
        ], 401);
    }

    /**
     * @param $cookbookId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|Response
     * @throws CookbookModelNotFoundException
     */
    public function destroy($cookbookId)
    {
        if (Auth::user()->ownCookbook($cookbookId)) {
            return $this->service->delete($cookbookId);
        }

        return response()->json([
            'error' => 'You are not authorized to perform this action.'
        ], 401);
    }
}
