<?php

namespace App\Adapters\Search;

interface FulltextSearchAdapterInterface
{
    public function fetch(string $q): \Illuminate\Support\Collection;
}
