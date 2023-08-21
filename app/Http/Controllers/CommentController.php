<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class CommentController extends Controller
{
    public function addComment(Request $request, JWT $jwtAuth)
    {
        /** @phpstan-ignore-next-line */
        if ($user = JWTAuth::parseToken()->user()) {

            $payload = $request->only([
                'resource-type', 'resource-id', 'comment'
            ]);

            if ($payload['resource-type'] === 'recipe') {
                try {
                    $comment = new Comment([
                        'user_id' => $user->getKey(),
                        'recipe_id' => Recipe::findOrFail(Arr::get($payload, 'resource-id')),
                        'comment' => Arr::get($payload, 'comment')
                    ]);

                    return response()->json(['created' => $comment->save()]);
                } catch (\Exception $exception) {
                    Log::debug(
                        'comment creation failed.',
                        ['error' => $exception->getMessage(), 'payload' => $payload]
                    );

                    return response()->json([
                        'error' => 'There was an error processing this request. Please try again later.'
                    ], 400);
                }
            }
        }

        throw new ApiException('You are not suthorized to perfrom this action.');
    }

    public function destroyComment(Request $request)
    {
        /** @phpstan-ignore-next-line */
        if ($user = JWTAuth::parseToken()->user()) {
            $payload = $request->only(['comment-id']);
            $comment = Comment::findOrFail($request->only(['comment-id']))->first();

            if ($user->isSuper() || $user->ownsComment($payload['comment-id'])) {
                try {
                    return response()->json(['deleted' => $comment->delete()]);
                } catch (\Exception $exception) {
                    Log::debug(
                        'comment deletion failed.',
                        ['error' => $exception, 'payload' => $payload]
                    );

                    return response()->json([
                        'error' => 'There was an error processing this request. Please try again later.'
                    ], 400);
                }
            } else {
                throw new ApiException('You are not suthorized to perfrom this action.');
            }
        }

        throw new ApiException('You are not suthorized to perfrom this action.');
    }
}
