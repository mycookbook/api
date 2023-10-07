<?php

declare(strict_types=1);

namespace App\Utils;

class UriHelper
{
    public static function buildHttpQuery(string $redirectTo, array $parameters): string
    {
        return config('services.redirects.' . $redirectTo) . http_build_query($parameters);
    }

    public static function redirectToUrl($to)
    {
        return redirect($to);
    }
}
