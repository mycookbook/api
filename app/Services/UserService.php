<?php

namespace App\Services;

use App\User;
use App\Jobs\SendEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Hashing\BcryptHasher;
use App\Jobs\CreateUserContactDetail;
use App\Interfaces\serviceInterface;
use App\Exceptions\CookbookModelNotFoundException;

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
        $users = User::with('cookbooks', 'recipes', 'contact')->get();

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
        $serialized = $request->merge(['user_id' => $user->id]);

        dispatch(new CreateUserContactDetail($serialized->all()));
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
	 * @throws CookbookModelNotFoundException
	 */
    public function show($q)
    {
		$user = $this->get($q)->with('recipes', 'contact')->get();

        return response(
            [
                "data" => [
                	'user' => $user
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
	 * @throws CookbookModelNotFoundException
	 */
    public function update(Request $request, $username)
    {
		$record = $this->get($username);

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

	/**
	 * Find user record
	 *
	 * @param $q
	 * @return mixed
	 * @throws CookbookModelNotFoundException
	 */
    public function get($q)
	{
		$r = User::where('id', $q)
			->orWhere('email', $q)
			->orWhere('name_slug', $q);

		if (!$r->first()) {
			throw new CookbookModelNotFoundException();
		}

		return $r;
	}
}
