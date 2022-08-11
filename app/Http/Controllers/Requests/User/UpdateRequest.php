<?php

namespace App\Http\Controllers\Requests\User;

use App\Exceptions\UnprocessibleEntityException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateRequest extends Controller
{
    public function __construct(Request $request)
    {
        if (array_key_exists('name', $request->all())) {
            $name = $request->input('name');
            if (empty($name)) {
                throw new UnprocessibleEntityException('Slug cannot be empty.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if (array_key_exists('password', $request->all())) {
            $password = $request->input('password');
            if (empty($password)) {
                throw new UnprocessibleEntityException('Password cannot be empty.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        parent::__construct($request);
    }
}
