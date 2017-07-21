<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Initialise class
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
        $this->validate(
            $request, [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:5'
            ]
        );
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
                ], 201
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
}
