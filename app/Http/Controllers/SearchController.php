<?php

namespace App\Http\Controllers;

use App\Adapters\Search\FulltextSearchAdapterInterface;
use App\Http\Requests\SearchRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    public function getSearchResults(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 411);
        }

        return response()->json([
            'response' => $this->adapter->fetch($request->get('query')),
        ]);
    }
}
