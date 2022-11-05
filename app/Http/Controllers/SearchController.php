<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    protected const SUPPORTED_QUERY_SYNTAX = [
        ":tags|cookbooks " => "getAllCookbooksByTag",
        ":tags|recipes " => "getAllRecipesByTag"
    ];

    /**
     * @var SearchService $service
     */
    protected SearchService $service;

    /**
     * Init
     */
    public function __construct()
    {
        $this->service = new SearchService();
    }

    /**
     * @param SearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSearchResults(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $searchQuery = $request->get("query");
        $tags = explode(" ", $searchQuery);

        if (str_starts_with($searchQuery, ":tags|cookbooks ")) {
            return $this->jsonResponse(
                $this->service->getAllCookbooksByTag(end($tags))
            );
        }

        if (str_starts_with($searchQuery, ":tags|recipes ")) {
            return $this->jsonResponse(
                $this->service->getAllRecipesByTag(end($tags))
            );
        }

        return $this->jsonResponse($this->service->searchEveryWhere($searchQuery));
    }

    /**
     * @param Collection $collection
     * @return \Illuminate\Http\JsonResponse
     */
    private function jsonResponse(Collection $collection)
    {
        return response()->json([
            'response' => $collection,
        ]);
    }
}
