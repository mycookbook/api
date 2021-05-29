<?php

namespace App\Services;

use App\Cookbook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Interfaces\serviceInterface;
use App\Exceptions\CookbookModelNotFoundException;

/**
 * Class CookbookService
 */
class CookbookService implements serviceInterface
{
	/**
	 * Return all cookbooks
	 *
	 * @param null $user_id
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function index($user_id = null): \Illuminate\Http\JsonResponse
	{
		$cookbooks = Cookbook::with([
			'categories',
			'flag',
			'recipes',
			'users'
		]);

		if ($user_id) {
			return response()->json(
				[
					'data' =>  $cookbooks
						->where("user_id", "=", $user_id)
						->take(15)
						->orderByDesc('created_at')
						->get()
				], Response::HTTP_OK
			);
		}

		return response()->json(
			[
				'data' =>  $cookbooks->take(15)
					->orderByDesc('created_at')
					->get()
			], Response::HTTP_OK
		);
    }

	/**
	 * Create cookbook resource
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
    public function store(Request $request): \Illuminate\Http\JsonResponse
	{
		//TODO: CookbookPolicy to ascertain that user is able to create a cookbook

		if (!is_array($request->get("categories"))) {
			throw new \Exception("There was a problem processing this request. Please try again.");
		}

		$categories = $request->get("categories");

		$cookbook = new Cookbook($request->all());
		$cookbook->user_id = $request->user()->id;
        $cookbook->slug = slugify($request->name);

        if ($cookbook->save()) {
			$cookbook->users()->attach($request->user()->id);

			foreach ($categories as $category) {
				$cookbook->categories()->attach($category);
			}

			return response()->json(
				[
					'response' => [
						'created' => true,
						'data' => $cookbook
					]
				], Response::HTTP_CREATED
			);
		}
    }

	/**
	 * Update cookbook resource
	 *
	 * @param $request
	 * @param int $id identifier
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws CookbookModelNotFoundException
	 */
    public function update($request, $id)
    {
		//TODO: Cookbook Policy to ascertain that user is able to update this cookbook
		$cookbook = $this->get($id);

		$data = $request->only([
			'name', 'description', 'bookCoverImg', 'category_id', 'flag_id', 'categories', 'alt_text'
		]);

        return response(
            [
                'updated' => $cookbook->update($data),
            ],Response::HTTP_OK
        );
    }

	/**
	 * Delete Cookbook resource
	 *
	 * @param int $id identofier
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws CookbookModelNotFoundException
	 */
    public function delete($id)
    {
		$cookbook = $this->get($id);

        return response(
            [
                'deleted' => $cookbook->delete()
            ], Response::HTTP_ACCEPTED
        );
    }

	/**
	 * @param mixed $id
	 *
	 * @return Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws CookbookModelNotFoundException
	 */
	public function show($id)
	{
		$cookbook = $this->get($id);

		if (!$cookbook) {
			throw new CookbookModelNotFoundException();
		}

		return response(
			[
				'data' => $cookbook
			], Response::HTTP_OK
		);
	}

	/**
	 * Find cookbook record
	 *
	 * @param $q
	 * @return mixed
	 * @throws CookbookModelNotFoundException
	 */
	public function get($q)
	{
		$record =  Cookbook::with('Users')
			->where('id', $q)
			->orWhere('slug', $q)
			->first();

		if (!$record) {
			throw new CookbookModelNotFoundException();
		}

		return $record;
	}
}
