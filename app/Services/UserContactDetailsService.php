<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserContactDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserContactDetailsService
{
    protected $contact_detail;

    /**
     * Creates new user contact detail
     *
     * @param  Request  $request
     */
    public function store(Request $request)
    {
        $detail = new UserContactDetail($request->only([
            'user_id',
            'visibility',
            'facebook',
            'twitter',
            'instagram',
            'office_address',
            'phone',
            'calendly',
            'skype',
            'website',
        ]));

        $detail->save();
    }

    /**
     * @param  Request  $request
     * @return Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function updateUserSettings(Request $request)
    {
        $user = User::where('email', $request->get('email'))->get()->first();
        $contact_detail = UserContactDetail::where('user_id', '=', $user->id)->get()->first();

        if ($contact_detail) {
            Log::info('user contact detail found', [$contact_detail->get()->first()]);
            $updated = $contact_detail->update($request->all());

            return response(
                [
                    'updated' => $updated,
                    'status' => 'success',
                ], Response::HTTP_OK
            );
        } else {
            Log::info('User contact detail not found.:', ['user_id' => $user->id]);
        }
    }
}
