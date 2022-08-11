<?php

namespace App\Http\Controllers\Requests\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignInRequest extends Controller
{
    public function __construct(Request $request)
    {
        $valid_request_payload = $this->validate(
            $request, [
                'email' => 'required',
                'password' => 'required',
            ]
        );

        parent::__construct($request->merge($valid_request_payload));
    }
}
