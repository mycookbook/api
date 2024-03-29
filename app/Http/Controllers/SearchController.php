<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Services\SearchService;
use App\Utils\LocationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Flysystem\Visibility;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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

        if (str_starts_with($searchQuery, ":me|following")) {
            $response = $this->service->getFollowing();

            if ($response->isNotEmpty()) {
                return $this->jsonResponse($response);
            }

            return response()->json([
                'error', 'Your login session has expired. Please login.'
            ], ResponseAlias::HTTP_UNAUTHORIZED);
        }

        if (str_starts_with($searchQuery, ":me|for-you")) {
            $response = $this->service->getForYou();

            if ($response->isNotEmpty()) {
                return $this->jsonResponse($response);
            }

            return response()->json([
                'error', 'Your login session has expired. Please login.'
            ], ResponseAlias::HTTP_UNAUTHORIZED);
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

    public function writeToCsv(Request $request)
    {
        $payload = $request->only(['city', 'country', 'ip', 'keyword', 'loc', 'timezone']);

        if (array_key_exists('keyword', $payload)) {

            if (!array_key_exists('ip', $payload)) {
                $payload['ip'] = $request->getClientIp();
            }

            if (!array_key_exists('timezone', $payload)) {
                $payload['timezone'] = "Unknown";
            }

            if (!array_key_exists('loc', $payload)) {
                $payload['loc'] = LocationHelper::getLocFromIpAddress($payload['ip']);
            }

            if (!array_key_exists('country', $payload)) {
                $payload['country'] = LocationHelper::getCountryCodeFromIpAddress($payload['ip']);
            }

            $dataToWrite = [];

            if (Storage::disk('public')->get('keywords.txt')) {
                $contents = json_decode(Storage::disk('public')->get('keywords.txt'), true);

                if (count($contents) > 0) {
                    $contents[] = $payload;
                    $dataToWrite = $contents;
                }

            } else {
                $dataToWrite = [$payload];
            }

            Storage::disk('public')
                ->put('keywords.txt', json_encode($dataToWrite),['visibility' => Visibility::PUBLIC]);
        }
    }
}
