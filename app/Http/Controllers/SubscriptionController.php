<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Response;

class SubscriptionController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $this->validate($request, ['email' => 'required|email|unique:subscribers,email']);

        $subscriber = new Subscriber($request->all());
        $subscriber->subscriptions = json_encode([3 => true]);
        $subscriber->save();

        //TODO: dispatch job to send thank you email notification

        return response()->json(
            [
                'response' => [
                    'created' => true,
                    'data' => $subscriber,
                ],
            ], Response::HTTP_CREATED
        );
    }
}
