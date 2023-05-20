<?php

declare(strict_types=1);

namespace App\Adapters\Search;

use Illuminate\Support\Collection;

class AlgoliaAdapter implements FulltextSearchAdapterInterface
{
    /**
     * @param  string  $q
     * @return \Illuminate\Support\Collection
     */
    public function fetch(string $q): Collection
    {
        return new Collection();
    }
}
