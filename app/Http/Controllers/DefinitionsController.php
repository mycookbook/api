<?php

namespace App\Http\Controllers;

use App\Models\Definition;

class DefinitionsController extends Controller
{
    public function index()
    {
        return Definition::all();
    }
}
