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
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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
     * Get all cookbooks
     */
    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->index()]);
    }

    /**
     * @param mixed $id
     * @return JsonResponse
     * @throws CookbookModelNotFoundException
     */
    public function show(mixed $id): JsonResponse
    {
        return response()->json(['data' => $this->service->show($id)]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function myCookbooks(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->service->index($request->get('user_id'))]);
    }

    /**
     * @param CookbookStoreRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(CookbookStoreRequest $request): JsonResponse
    {
        $request->merge([
            'user_id' => Auth::user()->id,
            'alt_text' => $request->get("alt_text") ?? 'cookbook cover image',
            'flag_id' => Flag::where(["flag" => $request->get("flag_id")])->first()->getKey(),
            'tags' => $request->get("tags") ?? ""
        ]);

        if ($this->service->store($request)) {
            return $this->successResponse([
                'response' => [
                    'created' => true,
                    'data' => []
                ]
            ]);
        }

        return $this->errorResponse([
            'error'=> 'There was an error processing this request, please try again.'
        ]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws CookbookModelNotFoundException
     */
    public function update(int $id, Request $request): JsonResponse
    {
        if (Auth::user()->ownCookbook($id) && $this->service->update($request, (string) $id)) {
            return $this->successResponse(['updated' => true]);
        }

        return response()->json([
            'error' => 'You are not authorized to access this resource.'
        ], ResponseAlias::HTTP_UNAUTHORIZED);
    }

    /**
     * @param $cookbookId
     * @return JsonResponse|Response
     * @throws CookbookModelNotFoundException
     */
    public function destroy($cookbookId): JsonResponse|Response
    {
        if (Auth::user()->ownCookbook($cookbookId) && $this->service->delete($cookbookId)) {
            return $this->noContentResponse();
        }

        return response()->json([
            'error' => 'You are not authorized to perform this action.'
        ], ResponseAlias::HTTP_UNAUTHORIZED);
    }
}
