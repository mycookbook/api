<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchRequest extends Controller
{
    public function __construct(Request $request)
    {
        $valid_request_payload = $this->validate(
            $request, [
                'query' => 'required',
            ]
        );

        parent::__construct($request->merge($valid_request_payload));
    }
}
