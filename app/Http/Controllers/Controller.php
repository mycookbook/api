<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Requests\FormRequest;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController implements FormRequest
{
	/**
	 * @var
	 */
	protected $service;

	/**
	 * @var array
	 */
	protected $params;

	/**
	 * @var Request
	 */
	public $request;

	/**
	 * Controller constructor.
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->params = $request->all();
		$this->request = $request;
	}

	/**
	 * Return the Request Object
	 *
	 * @return \Illuminate\Http\Request
	 */
	public function getParams(): Request
	{
		return $this->request->replace($this->params);
	}
}
