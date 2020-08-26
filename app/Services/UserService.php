<?php

namespace App\Services;

use App\Jobs\SendEmail;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Interfaces\serviceInterface;
use Illuminate\Hashing\BcryptHasher;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserService
 */
class UserService implements serviceInterface
{
    /**
     * Get all users from the database
     */
    public function index()
    {
        $users = User::with('cookbooks', 'recipes')->get();

        return response([
        	'data' => $users
		], Response::HTTP_OK);
    }

	/**
	 * Create a new user
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function store(Request $request)
    {
        $user = new User([
        	'name' => $request->name,
			'email' => $request->email,
			'password' => (new BcryptHasher)->make($request->password),
			'following' => 0,
			'followers' => 0,
			'name_slug' => slugify($request->name)
		]);

        $created = $user->save();

        dispatch(new SendEmail());

        return response()->json(
            [
                'response' => [
                    'created' => $created,
                    'data' => $user,
                    'status' => "success",
                ]
            ], Response::HTTP_CREATED
        );
    }

	/**
	 * Get one user
	 *
	 * @param string $q
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 */
    public function show($q)
    {
		$user = User::where('id', $q)
			->orWhere('email', $q)
			->orWhere('name_slug', $q)
			->first();

		if (!$user) {
			throw new ModelNotFoundException();
		}

        return response(
            [
                "data" => [
                	'user' => $user,
					'recipes' => $user->recipes()->get()
				],
            ], Response::HTTP_OK
        );
    }

	/**
	 * Implement a full/partial update
	 *
	 * @param \Illuminate\Http\Request $request request
	 * @param string $username
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 */
    public function update(Request $request, $username)
    {
		$record = User::where('name_slug', $username)->firstOrFail();

		if ($request->all()) {
			$updated = $record->update([
				'name' => Str::ucfirst($request->name),
				'name_slug' => slugify($request->name),
				'password' => (new BcryptHasher)->make($request->password),
				'followers' => $request->followers ? $request->followers : 0,
				'following' => $request->following ? $request->following : 0
			]);

			return response(
				[
					"updated" => $updated,
					"status" => "success",
				], Response::HTTP_OK
			);
		} else {
			return response([], Response::HTTP_NO_CONTENT);
		}
    }
}
