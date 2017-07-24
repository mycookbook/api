<?php
/**
 * UserController
 */

namespace App\Http\Controllers;

use App\User;
use App\Recipe;
use App\Cookbook;
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
     * Update user
     *
     * @param int $id unique identification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $user = $this->find($id);

        return $user;

    }

    /**
     * Get all users fromt he database
     *
     * @return int
     */
    public function index()
    {
        $users = User::with('Recipes', 'Cookbooks')->get();

        return response()->json(
            [
                'response' => [
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
    public function find($id)
    {
        $user = User::with('Recipes', 'Cookbooks')->find($id);

        if (! $user) {
            return response()->json(
                [
                    'error' => 'Record not found.'
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
