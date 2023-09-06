<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\serviceInterface;
use App\Models\User;
use App\Models\UserContactDetail;
use App\Utils\DbHelper;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Request;

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
        return User::with('cookbooks', 'recipes', 'contact')->get();
    }

    /**
     * Create a new user resource
     */
    public function store(Request $request)
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

        if ($created) {
            $serialized = $request->merge(['user_id' => $user->id]);

            //TODO: hand this over to a job to handle asynchronously
            $contact = new UserContactDetailsService();
            $contact->store(new Request($serialized->all()));

            // dispatch(new SendEmailNotification($user->id));
            return true;
        }

        //TODO: log some debugging info here
        return false;
    }

    public function show($q)
    {
        return $this->findWhere($q)->get()->append(['tiktok_videos']);
    }

    /**
     * @param Request $request
     * @param string $option
     * @return bool
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

            return $userRecord->save();
        } catch (\Exception $e) {
            //TODO: log debugging message here
        }

        return false;
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
