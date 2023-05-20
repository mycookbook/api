<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    const RECIPE_RESOURCE = 'recipe';
    const COOKBOOK_RESOURCE = 'cookbook';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
