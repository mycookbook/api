<?php

namespace App\Services;

use App\Jobs\TriggerEmailVerificationProcess;
use App\Jobs\UpdateUserContactDetail;
use App\Jobs\UpdateUserContactDetailJob;
use App\User;
use App\Jobs\SendEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Hashing\BcryptHasher;
use App\Jobs\CreateUserContactDetail;
use App\Interfaces\serviceInterface;
use Illuminate\Support\Facades\Log;
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
			'name_slug' => slugify($request->name),
			'avatar' => 'https://bit.ly/3m3M73g',
		]);

        $created = $user->save();
        $serialized = $request->merge(['user_id' => $user->id]);

        dispatch(new CreateUserContactDetail($serialized->all()));
        dispatch(new TriggerEmailVerificationProcess($user->id));

//        TODO: send post req using a webhook to the notifications service: to handle sending
// the email containing the verification link - the link is the token generated
		// a new token will be generated and used as the payload
		//this will be sent in the message body
		//this token will contain the user email
		// if the email in the token exists in the db then set the email verification field of that entry to current date time stamp

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
	 * @param string $username
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws CookbookModelNotFoundException
	 */
    public function update(Request $request, string $username)
    {
		$user_record = $this->get($username);
		$user_contact_detail = $user_record->get()->first()->contact;

		if ($request->all()) {
			$updated = $user_record->update([
				'name' => Str::ucfirst($request->name),
				'name_slug' => slugify($request->name),
				'pronouns' => $request->pronouns ? $request->pronouns : NULL,
				'avatar' => $request->avatar ? $request->avatar : 'https://bit.ly/3m3M73g',
				'expertise_level' => $request->expertise_level ? $request->expertise_level : 'novice',
				'about' => $request->about ? $request->about : NULL,
				'can_take_orders' => ($request->can_take_orders == "0") ? 0 : 1,
			]);

			$request->merge(['user_id' => $user_record->get()->first()->id]);
			Log::info('contact info', [$request->all()]);
			$user_contact_detail->update($request->all());

			return response(
				[
					"updated" => $updated,
					"status" => "success",
					"username" => $request->name
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
