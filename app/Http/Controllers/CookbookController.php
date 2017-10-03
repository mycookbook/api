<?php

namespace App\Http\Controllers;

use App\Cookbook;
use Tymon\JWTAuth\JWTAuth;
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
     *
     * @param JWTAuth $jwt jwt
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->middleware('jwt.auth', ['only' => ['update', 'store', 'destroy']]);
        $this->jwt = $jwt;

        if (! $user = $this->jwt->parseToken()->authenticate() ) {
            return response()->json(
                [
                    'msg' => 'user not authenticated'
                ]
            );
        }
    }

    /**
     * Return all the cookbooks and associated resipes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $cookbooks = Cookbook::with('Recipes')
            ->where('user_id', $this->jwt->toUser()->id)
            ->get();

        $data = $cookbooks->toArray();

        $meta = [];

        foreach ($data as $key => $val) {
            $meta[$key] = $val;
            $meta[$key]['links'] = [
                'methods' => [
                    'get' => 'api/v1/cookbook/' . $val["id"],
                    'put' => 'api/v1/cookbook/' . $val["id"],
                    'delete' => 'api/v1/cookbook/' . $val["id"]
                ]
            ];
        }

        return response()->json(
            [
                'response' => [
                    'cookbooks' => $meta
                ]
            ], 200
        );
    }

    /**
     * Update cookbook
     *
     * @param Request $request    request input
     * @param int     $cookbookId paramname
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $cookbookId)
    {
        $response = [];

        $cookbook = Cookbook::find($cookbookId);

        if (! $cookbook || $cookbook === null) {
            $response["error"] = 'Record does not exist.';
            $response["status"] = 404;
        } else {
            $fields = $request->only('name', 'description');

            foreach ($fields as $key => $val) {
                if ($val !== null || !is_null($val)) {
                    $cookbook->$key = $val;
                }
            }

            try {
                if ($cookbook->save()) {
                    $response["updated"] = true;
                    $response["status"] = 200;
                }
            } catch (Exception $e) {
                $response["error"] = $e->getMessage();
                $response["status"] = 422;
            }
        }

        return response()->json(
            [
                'response' => $response
            ], $response["status"]
        );
    }

    /**
     * Find one cookbook belonging to the author
     *
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
        $this->validate(
            $request, [
                'name' => 'required',
                'description' => 'required'
            ]
        );

        $cookbook = new Cookbook();

        $cookbook->name = $request->input('name');
        $cookbook->description = $request->input('description');
        $cookbook->user_id = $userId;

        if ($cookbook->save()) {
            return response()->json(
                [
                    'response' => [
                        'created' => true,
                        'link' => 'api/v1/cookbook/' . $cookbook->id
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

    /**
     * Delete a cookbook
     *
     * @param int $cookbookId cookbookId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($cookbookId)
    {
        $cookbook = Cookbook::find($cookbookId);
        $response = [];

        if (! $cookbook || $cookbook === null) {
            return response()->json(
                [
                    'response' => 'Record does not exist'
                ], 404
            );
        } else {
            try {
                if ($cookbook->delete()) {
                    $response["deleted"] = true;
                    $response["status"] = 200;
                }
            } catch (Exception $e) {
                $response["error"] = $e->getMessage();
                $response["status"] = 422;
            }
        }

        return response()->json(
            [
                'response' => $response["response"]
            ], $response["status"]
        );
    }
}
