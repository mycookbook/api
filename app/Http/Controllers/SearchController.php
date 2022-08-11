<?php

namespace App\Http\Controllers;

use App\Adapters\Search\FulltextSearchAdapterInterface;
use App\Http\Controllers\Requests\SearchRequest;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * @var FulltextSearchAdapterInterface
     */
    protected $adapter;

    /**
     * SearchController constructor.
     *
     * @param  Request  $request
     * @param  FulltextSearchAdapterInterface  $adapter
     */
    public function __construct(Request $request, FulltextSearchAdapterInterface $adapter)
    {
        parent::__construct($request);
        $this->adapter = $adapter;
    }

    /**
     * @param  SearchRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch(SearchRequest $request): \Illuminate\Http\JsonResponse
    {
        $q = $request->getParams()->input('query');

        return response()->json([
            'response' => $this->adapter->fetch($q),
        ]);
    }
}
