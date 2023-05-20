<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => [
            'index',
            'show',
        ]]);
    }
}
