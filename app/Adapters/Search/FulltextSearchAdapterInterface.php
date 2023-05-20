<?php

declare(strict_types=1);

namespace App\Adapters\Search;

interface FulltextSearchAdapterInterface
{
    public function fetch(string $q): \Illuminate\Support\Collection;
}
