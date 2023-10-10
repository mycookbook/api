<?php

declare(strict_types=1);

namespace App\Utils;

class UriHelper
{
    public static function buildHttpQuery(string $redirectToPage, array $parameters): string
    {
        $redirectToPage = $redirectToPage . '.beta-version-1-staging';

        return config('services.redirects.' . $redirectToPage) . http_build_query($parameters);
    }
}
