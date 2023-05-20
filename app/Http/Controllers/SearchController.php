<?php

declare(strict_types=1);

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
            $searchQuery = str_replace(",", "|", $searchQuery);
            $searchQuery = str_replace(":tags|cookbooks ", "", $searchQuery);
            $tags = explode("|", $searchQuery);
            $searchTags = [];

            foreach ($tags as $tag) {
                $searchTags[] = trim($tag);
            }

            return $this->jsonResponse(
                $this->service->getAllCookbooksByTag($searchTags)
            );
        }

        if (str_starts_with($searchQuery, ":tags|recipes ")) {
            return $this->jsonResponse(
                $this->service->getAllRecipesByTag(end($tags))
            );
        }

        if (str_starts_with($searchQuery, ":cookbooks|categories ")) {
            $searchQuery = str_replace(",", "|", $searchQuery);
            $searchQuery = str_replace(":cookbooks|categories ", "", $searchQuery);
            $tags = explode("|", $searchQuery);
            $searchCats = [];

            foreach ($tags as $tag) {
                $searchCats[] = trim($tag);
            }

            return $this->jsonResponse(
                $this->service->getAllCookbooksByCategoryName($searchCats)
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

        if (str_starts_with($searchQuery, ":recipes|likes ")) {
            return $this->jsonResponse(
                $this->service->getAllRecipesWithThisNumberofLikes(end($tags))
            );
        }

        if (str_starts_with($searchQuery, ":recipes|ingredients ")) {
            return $this->jsonResponse(
                $this->service->getAllRecipesByIngredientName(end($tags))
            );
        }

        if (str_starts_with($searchQuery, ":me|cookbooks ")) {
            return $this->jsonResponse(
                $this->service->getAllCookbooksByMe(end($tags))
            );
        }

        if (str_starts_with($searchQuery, ":me|recipes ")) {
            return $this->jsonResponse(
                $this->service->getAllRecipesByMe(end($tags))
            );
        }

        if ($searchQuery === "cookbooks") {
            return response()->json([
                'response' => $this->service->getMostRecentCookbooks(),
            ]);
        }

        if ($searchQuery === "recipes") {
            return response()->json([
                'response' => $this->service->getMostRecentRecipes(),
            ]);
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
