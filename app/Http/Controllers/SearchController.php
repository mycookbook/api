<?php

namespace App\Http\Controllers;

use App\Adapters\Search\FulltextSearchAdapterInterface;
use App\Http\Requests\SearchRequest;

class SearchController extends Controller
{
    /**
     * @var FulltextSearchAdapterInterface
     */
    protected FulltextSearchAdapterInterface $adapter;

    /**
     * @param  FulltextSearchAdapterInterface  $adapter
     */
    public function __construct(FulltextSearchAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param  SearchRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSearchResults(SearchRequest $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'response' => $this->adapter->fetch($request->get('query')),
        ]);
    }
}
