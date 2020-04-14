<?php

namespace App\Http\Services;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Interfaces\serviceInterface;
use Illuminate\Hashing\BcryptHasher;

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
	 * @param string $username
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 */
    public function show($username)
    {
    	$user = User::where('name_slug', $username)->firstOrFail();

        return response(
            [
                "data" => $user->with('recipes'),
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
    }
}
