<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CookbookModelNotFoundException;
use App\Interfaces\serviceInterface;
use App\Models\User;
use App\Models\UserContactDetail;
use App\Utils\DbHelper;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class UserService
 */
class UserService extends BaseService implements serviceInterface
{
    public function __construct()
    {
        $this->serviceModel = new UserContactDetail();
    }

    /**
     * Get all users from the database
     */
    public function index()
    {
        $users = User::with('cookbooks', 'recipes', 'contact')->get();

        return response([
            'data' => $users,
        ], Response::HTTP_OK);
    }

    /**
     * Create a new user
     *
     * @param \Illuminate\Http\Request $request
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
            'name_slug' => DbHelper::generateUniqueSlug($request->name, 'users', 'name_slug'),
            'avatar' => 'https://bit.ly/3m3M73g',
        ]);

        $created = $user->save();
        $serialized = $request->merge(['user_id' => $user->id]);
        $contact = new UserContactDetailsService();
        $contact->store(new Request($serialized->all()));

        //		dispatch(new SendEmailNotification($user->id));

        return response()->json(
            [
                'response' => [
                    'created' => $created,
                    'data' => $user,
                    'status' => 'success',
                ],
            ], Response::HTTP_CREATED
        );
    }

    /**
     * @param $q
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     * @throws CookbookModelNotFoundException
     */
    public function show($q)
    {
        return response(
            [
                'data' => [
                    'user' => $this->findWhere($q)->get()->append(['tiktok_videos']),
                ],
            ], Response::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @param string $option
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function update(Request $request, string $option)
    {
        try {
            $userRecord = User::findWhere($option, ['cookbooks', 'recipes'], ['email', 'name_slug'])->first();

            $data = $request->only([
                'name',
                'email',
                'name_slug',
                'pronouns',
                'avatar',
                'contact_email',
                'about',
                'expertise_level',
                'can_take_orders',
            ]);

            foreach ($this->getFillables() as $fillable) {
                if (isset($data[$fillable])) {
                    $userRecord->$fillable = $data[$fillable];
                }
            }

            if ($updated = $userRecord->save()) {
                return response(
                    [
                        'updated' => (bool)$updated,
                        'status' => 'success',
                    ], Response::HTTP_OK
                );
            }

            throw new \Exception('Not saved.');
        } catch (\Exception $e) {
            return response([
                'errors' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $q
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findWhere($q)
    {
        return User::with(['cookbooks', 'recipes'])
            ->where('id', $q)
            ->orWhere('email', $q)
            ->orWhere('name_slug', $q);
    }
}
