<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Requests\FormRequest;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController implements FormRequest
{
	protected $params;

	/**
	 * Return the Request Object
	 *
	 * @return \Illuminate\Http\Request
	 */
	public function getParams(): Request
	{
		return new Request($this->params);
	}
}
