<?php


/**
 * Create Slug
 *
 * @param $slug
 *
 * @return string
 */
function slugify($slug) {
    return str_replace(" ", "-", strtolower($slug));
}
