<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cookbook;
use App\Models\User;
use App\Models\UserContactDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserContactDetailsService extends BaseService
{
    public function __construct()
    {
        $this->serviceModel = new UserContactDetail();
    }

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
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function updateUserSettings(Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        $contact_detail = UserContactDetail::where('user_id', '=', $user->id)->first();
        $updated = false;

        if ($contact_detail) {
            Log::info('user contact detail found', [$contact_detail->get()->first()]);
            $updated = $contact_detail->update($request->all());
        } else {
            Log::info('User contact detail not found.:', ['user_id' => $user->id]);
        }

        return response(
            [
                'updated' => $updated,
                'status' => 'success',
            ], Response::HTTP_OK
        );
    }
}
