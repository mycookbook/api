<?php

namespace App\Http\Repositories;

use App\Cookbook;

/**
 * Class CookbookRepository
 */
class CookbookRepository
{

    /**
     * Return all cookbooks
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response(
            [
                'data' =>  Cookbook::with('Recipes', 'Users', 'Category', 'Flag')
                    ->take(10)->orderByDesc('created_at')->get()
            ]
        );
    }

    /**
     * Create cookbook resource
     *
     * @param Request $request request
     * @param User    $user    user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($request, $user)
    {
        $cookbook = new Cookbook(
            [
                'name'          => $request->name,
                'description'   => $request->description,
                'bookCoverImg'  => $request->bookCoverImg,
                'user_id'       => $user->id,
            ]
        );
        $cookbook->category_id = $request->category_id;
        $cookbook->flag_id = $request->flag_id;

        $cookbook->slug = slugify($request->name);

        $data = $cookbook->save();

        $cookbook->users()->attach($user->id);

        $statusCode = $cookbook ? 201 : 422;

        return response()->json(
            [
                'response' => [
                    'created' => true,
                    'data' => self::findOrFail($cookbook),
                    'status' => $data ? "success" : "error",
                ]
            ], $statusCode
        );
    }

    /**
     * Update cookbook resource
     *
     * @param $request
     * @param int $id identifier
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function update($request, $id)
    {
        try {
            $cookbook = self::findOrFail($id);
            $updated = $cookbook->update($request->all());
            $statusCode =  $updated ? 202 : 422;
            $status = "success";
        } catch(\Exception $e) {
            $updated = false;
            $statusCode = 404;
            $status = ['error' => $e->getMessage()];
        }

        return response(
            [
                'updated' => $updated,
                'status' => $status
            ], $statusCode
        );
    }

    /**
     * Find cookbook
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public static function findOrFail($id)
    {
        $cookbook = new Cookbook();

        return $cookbook->findOrfail($id);
    }

    /**
     * Delete Cookbook resource
     *
     * @param int $id identofier
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function delete($id)
    {
        try {
            $cookbook = self::findOrFail($id);
            $deleted = $cookbook->delete();
            $statusCode = $deleted ? 202 : 422;
            $status = "success";
        } catch (\Exception $e) {
            $deleted = false;
            $statusCode = 404;
            $status = ['error' => $e->getMessage()];
        }

        return response(
            [
                'deleted' => $deleted,
                'status' => $status,
            ], $statusCode
        );
    }
}
