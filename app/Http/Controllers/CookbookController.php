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

        $this->user = $this->jwt->parseToken()->authenticate();

        if (! $this->user ) {
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
     * Create cookbook for user
     *
     * @param Request $request Form input
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $response = [];

        $this->validate(
            $request, [
                'name' => 'required',
                'description' => 'required'
            ]
        );

        $cookbook = new Cookbook();

        $cookbook->name = $request->input('name');
        $cookbook->description = $request->input('description');
        $cookbook->user_id = $this->user->id;

        try {
            if ($cookbook->save()) {
                $response['created'] = true;
                $response['status'] = 200;
                $response['links'] = [
                    'get' => 'api/v1/cookbook/' . $cookbook->id,
                    'put' => 'api/v1/cookbook/' . $cookbook->id,
                    'patch' => 'api/v1/cookbook/' . $cookbook->id,
                    'delete' => 'api/v1/cookbook/' . $cookbook->id
                ];
            }
        } catch (Exception $e) {
            $response["error"] = $e->getMessage();
            $response["status"] = 422;
        }


        return response()->json(
            [
                'response' => $response
            ], $response["status"]
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

        $cookbook = self::cookbookExist($cookbookId);

        if (! $cookbook || $cookbook === null) {
            $response["error"] = 'Cookbook does not exist.';
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
     * Delete a cookbook
     *
     * @param int $cookbookId cookbookId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($cookbookId)
    {
        $response = [];

        $cookbook = self::cookbookExist($cookbookId);

        if (! $cookbook || $cookbook === null) {
            $response["error"] = 'Record does not exist.';
            $response["status"] = 404;
        } else {
            try {
                if ($cookbook->delete()) {
                    $response["deleted"] = true;
                    $response["status"] = 202;
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
     * Find the cookbook
     *
     * @param int $cookbookId cookbokId
     *
     * @return mixed
     */
    public static function cookbookExist($cookbookId)
    {
        return Cookbook::find($cookbookId) ?? false;
    }
}
