<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
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

        if (str_starts_with($searchQuery, ":cookbooks|categories ")) {
            return $this->jsonResponse(
                $this->service->getAllCookbooksByCategoryName(end($tags))
            );
        }

        if (str_starts_with($searchQuery, ":cookbooks|recipes ")) {
            return $this->jsonResponse(
                $this->service->getAllCookbooksHavingThisRecipe(end($tags))
            );
        }

        if (str_starts_with($searchQuery, ":cookbooks|author ")) {
            return $this->jsonResponse(
                $this->service->getAllCookbooksByThisAuthor(end($tags))
            );
        }

        if (str_starts_with($searchQuery, ":recipes|author ")) {
            return $this->jsonResponse(
                $this->service->getAllRecipesByThisAuthor(end($tags))
            );
        }

        //todo likes e.g 0, >10 <100 =1000 exactly 5 etc
        if (str_starts_with($searchQuery, ":recipes|likes ")) {
            return $this->jsonResponse(
                $this->service->getAllRecipesWithThisNumberofLikes(end($tags))
            );
        }

        //todo: containinig one or more of the listed ingredients
        if (str_starts_with($searchQuery, ":recipes|ingredients ")) {
            return $this->jsonResponse(
                $this->service->getAllRecipesByIngredientName(end($tags))
            );
        }

        if ($searchQuery === "cookbooks") {
            return response()->json([
                'response' => $this->service->getMostrecentCookbooks(),
            ]);
        }

        if ($searchQuery === "recipes") {
            return response()->json([
                'response' => $this->service->getMostRecentRecipes(),
            ]);
        }

        //todo: :me syntax

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
