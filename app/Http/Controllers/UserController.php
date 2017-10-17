<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;

/**
 * Class UserController
 */
class UserController extends Controller
{
    // TO DO: Implement soft delete
    // Applicable to users who want to delete theor account
    // Or freeze their account

    /**
     * Get all users from the database
     *
     * @return int
     */
    public function index()
    {
        return response(
            [
                'data' =>  User::with('Recipes', 'Cookbooks')->get()->toArray()
            ]
        );
    }

    /**
     * Create new user resource
     *
     * @param Request $request form inputs
     *
     * @return array|string
     */
    public function store(Request $request)
    {
        $this->validate(
            $request, [
                'name' => 'required',
                'email' => 'required|unique:users|email',
                'password' => 'required|min:5'
            ]
        );

        $user = new User(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => (new BcryptHasher)->make($request->password),
                'following' => 0,
                'followers' => 0
            ]
        );

        $data = $user->save();

        $statusCode = $user ? 201 : 422;

        return response()->json(
            [
                'response' => [
                    'created' => true,
                    'data' => self::userExist($user->id),
                    'status' => $data ? "success" : "error",
                ]
            ], $statusCode
        );
    }

    /**
     * Get one user
     *
     * @param int $id id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = self::userExist($id);
        } catch (\Exception $e) {
            $user = null;
            $statusCode = 404;
        }

        return response(
            [
                'data' => User::with('Recipes', 'Cookbooks')->find($id),
                'status' => $user ? "success" : "Not found.",
            ], $statusCode ?? 200
        );
    }

    /**
     * Implement a full/partial update
     *
     * @param Request $request request
     * @param int     $userId  userId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $userId)
    {
        try {
            $user = self::userExist($userId);
            $user->update($request->all());
        } catch(\Exception $e) {
            $user = null;
            $statusCode = 404;
        }
        return response(
            [
                "data" => $user,
                "status" => $user ? "success" : "ILLEGAL OPERATION."
            ], $statusCode ?? 200
        );
    }

    /**
     * Check if user exist by id
     *
     * @param int $id id
     *
     * @return bool|mixed|static
     */
    protected static function userExist($id)
    {
        return User::findOrFail($id);
    }
}
