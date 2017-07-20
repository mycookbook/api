<?php
/**
 * UserController
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        //
    }

    /**
     * Create new user
     *
     * @param Request $request form inputs
     *
     * @return array|string
     */
    public function create(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = new User();

            $user->name = $name;
            $user->email = $email;
            $hashedPassword = (new BcryptHasher)->make($password);
            $user->following = 0;
            $user->followers = 0;

            $user->password = $hashedPassword;

            $user->save();

            return response()->json(
                [
                    'response' => ['created' => true]
                ], 200
            );
        } else {
            return response()->json(
                [
                    'response' => [
                        'success' => false,
                        'data' => 'Email exists already'
                    ]
                ], 401
            );
        }
    }

    /**
     * Signin
     *
     * @param Request $request form inputs
     *
     * @return array|string
     */
    public function signin(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        return response()->json(
            [
                'response' => [
                    'success' => true,
                    'data' => [$email, $password]
                ]
            ], 200
        );
    }

    /**
     * Update user
     *
     * @param int $id unique identification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $user = $this->getUser($id);

        return $user;

    }

    /**
     * Get all users fromt he database
     *
     * @return int
     */
    public function getAllUsers()
    {
        $users = User::with('Recipes', 'Cookbooks')->get();

        if (count($users) < 1) {
            return response()->json(
                [
                    'response' => [
                        'success' => false,
                        'data' => null
                    ]
                ], 404
            );
        }

        return response()->json(
            [
                'response' => [
                    'success' => true,
                    'data' => $users->toArray()
                ]
            ], 200
        );
    }

    /**
     * Get one user
     *
     * @param int $id id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        $user = User::with('Recipes', 'Cookbooks')->find($id);

        if (! $user) {
            return response()->json(
                [
                    'response' => [
                        'success' => false,
                        'data' => 'Not found!'
                    ]
                ], 404
            );
        }

        return response()->json(
            [
                'response' => [
                    'success' => false,
                    'data' => $user->toArray()
                ]
            ], 200
        );
    }
}
