<?php

namespace App\Services;

use App\Jobs\SendEmailNotification;
use App\User;
use App\Jobs\SendEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Hashing\BcryptHasher;
use App\Interfaces\serviceInterface;
use App\Jobs\UpdateUserContactDetail;
use App\Jobs\UpdateUserContactDetailJob;
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
    public function store(Request $request): \Illuminate\Http\JsonResponse
	{
        $user = new User([
        	'name' => $request->name,
			'email' => $request->email,
			'password' => (new BcryptHasher)->make($request->password),
			'following' => 0,
			'followers' => 0,
			'name_slug' => slugify($request->name),
			'avatar' => 'https://bit.ly/3m3M73g',
		]);

        $created = $user->save();
        $serialized = $request->merge(['user_id' => $user->id]);
		$contact = new UserContactDetailsService();
		$contact->store(new Request($serialized->all()));

		dispatch(new SendEmailNotification($user->id));

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
	 * @param mixed $q
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws CookbookModelNotFoundException
	 */
    public function show($q)
    {
		$user = $this->get($q)->with('cookbooks', 'recipes', 'contact')->get();

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
	 * @param string $option
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws CookbookModelNotFoundException
	 */
    public function update(Request $request, string $option)
    {
		$user_record = $this->get($option);
		$user_id = $user_record->get()->first()->id;
		$user_contact_detail = $user_record->get()->first()->contact;

		try {
			$updated = $user_record->update([
				'name' => Str::ucfirst($request->name),
				'name_slug' => slugify($request->name),
				'pronouns' => $request->pronouns ? $request->pronouns : NULL,
				'avatar' => $request->avatar ? $request->avatar : '',
				'expertise_level' => $request->expertise_level ? $request->expertise_level : 'novice',
				'about' => $request->about ? $request->about : NULL,
				'can_take_orders' => ($request->can_take_orders == "0") ? 0 : 1,
			]);

			$request->merge(['user_id' => $user_id]);
			$user_contact_detail->update($request->all());

			return response(
				[
					"updated" => (bool) $updated,
					"status" => "success",
					"username" => $request->username
				], Response::HTTP_OK
			);
		} catch (\Exception $e) {
			return response([
				'errors' => $e->getMessage()
			], Response::HTTP_NO_CONTENT);
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
