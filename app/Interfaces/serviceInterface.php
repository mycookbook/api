<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface serviceInterface
{
	public function index();
	public function show($option);
	public function store(Request $request);
	public function update(Request $request, string $option);
	function findWhere($q);
}