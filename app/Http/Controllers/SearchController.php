<?php

namespace App\Http\Controllers;


/**
 * Class SearchController
 */
class SearchController extends Controller
{
    /**
     * Return items based on query
     *
     * @param string $query search_query
     *
     * @return void
     */
    public function find($query)
    {
        // find cookbook that matches query
        // find recipe that matches query
        // merge into one array
    }

    /**
     * Return cookbooks for specific key
     *
     * @param string $query query
     *
     * @return array
     */
    public function getRecipes($query)
    {
        return [];
    }


    /**
     * Return re
     * @param $key
     */
    public function getCookbooks($key)
    {

    }

    protected function queryResults()
    {

    }
}