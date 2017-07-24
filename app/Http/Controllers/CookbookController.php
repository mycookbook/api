<?php
/**
 * CookbookController
 */

namespace App\Http\Controllers;

use App\Cookbook;
use Illuminate\Http\Request;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class CookbookController extends Controller
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
        return $id;
    }


    /**
     * @param int $id identifier
     *
     * @return int
     */
    public function find($id)
    {
        return $id;
    }

    /**
     * Create cookbook for user
     *
     * @param Request $request Form input
     * @param int     $userId  unique identofocation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $userId)
    {
        $cookbook = new Cookbook();

        $cookbook->name = $request->input('name');
        $cookbook->description = $request->input('description');
        $cookbook->user_id = $userId;

        if ($cookbook->save()) {
            return response()->json(
                [
                    'response' => [
                        'created' => true
                    ]
                ], 200
            );
        } else {
            return response()->json(
                [
                    'response' => [
                        'created' => false
                    ]
                ], 401
            );
        }
    }
}
