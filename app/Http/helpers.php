<?php

declare(strict_types=1);

/**
 * Create Slug
 *
 * @param $slug
 * @return string
 */
function slugify($slug)
{
    return str_replace(' ', '-', strtolower($slug));
}
