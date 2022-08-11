<?php

namespace App\Http\Controllers\Requests\User;

use App\Http\Controllers\Controller;
use App\Rules\DisallowedCharactersRule;
use Illuminate\Http\Request;

class StoreRequest extends Controller
{
    /**
     * StoreRequest constructor.
     *
     * @param  Request  $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __construct(Request $request)
    {
        $valid_request_payload = $this->validate(
            $request, [
                'name' => ['required', 'min:2', new DisallowedCharactersRule()],
                'email' => 'required|email|unique:users',
                'password' => 'required|min:5',
            ]
        );

        parent::__construct($request->merge($valid_request_payload));
    }
}
