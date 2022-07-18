<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->middleware('jwt.auth', ['except' => [
            'index',
            'show'
        ]]);

        parent::__construct($request);
    }
}
