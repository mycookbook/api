<?php

namespace App\Http\Controllers;

use App\Definition;

class DefinitionsController extends Controller
{
	public function index()
	{
		return Definition::all();
	}
}