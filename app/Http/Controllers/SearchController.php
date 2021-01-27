<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Requests\SearchRequest;
use App\Adapters\Search\FulltextSearchAdapterInterface;

class SearchController extends Controller
{
	/**
	 * @var FulltextSearchAdapterInterface
	 */
	protected $adapter;

	/**
	 * SearchController constructor.
	 *
	 * @param Request $request
	 * @param FulltextSearchAdapterInterface $adapter
	 */
	public function __construct(Request $request, FulltextSearchAdapterInterface $adapter)
	{
		parent::__construct($request);
		$this->adapter = $adapter;
	}

	/**
	 * @param SearchRequest $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function fetch(SearchRequest $request): \Illuminate\Http\JsonResponse
	{
		$q = $request->getParams()->input('query');

		return response()->json([
			'response' => $this->adapter->fetch($q)
		]);
	}
}
