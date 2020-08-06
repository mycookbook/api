<?php

namespace App\Services;

use App\Cookbook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Interfaces\serviceInterface;

/**
 * Class CookbookService
 */
class CookbookService implements serviceInterface
{
    /**
     * Return all cookbooks
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
		return response()->json(
			[
				'data' =>  Cookbook::with('Recipes', 'Users', 'Categories', 'Flag')
					->take(50)->orderByDesc('created_at')->get()
			], Response::HTTP_OK
		);
    }

    /**
     * Create cookbook resource
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $cookbook = new Cookbook($request->all());

		$cookbook->user_id = $request->user()->id;
        $cookbook->slug = slugify($request->name);

        if ($cookbook->save()) {
			$cookbook->users()->attach($request->user()->id);
			$cookbook->categories()->attach($request->get('categories'));

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
     */
    public function update($request, $id)
    {
    	$cookbook = Cookbook::findOrfail($id);

        return response(
            [
                'updated' => $cookbook->update($request->all()),
            ],Response::HTTP_OK
        );
    }

    /**
     * Delete Cookbook resource
     *
     * @param int $id identofier
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function delete($id)
    {
		$cookbook = Cookbook::findOrfail($id);

        return response(
            [
                'deleted' => $cookbook->delete()
            ], Response::HTTP_ACCEPTED
        );
    }

	/**
	 * @param $id
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
	 */
	public function show($id)
	{
		return Cookbook::with('Users')
			->where('id', $id)
			->orWhere('slug', $id)
			->firstOrFail();
	}
}
