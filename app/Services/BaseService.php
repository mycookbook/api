<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class BaseService
{
    protected Model $serviceModel;

    /**
     * @return string[]
     */
    protected function getFillables()
    {
        return $this->serviceModel->getFillable();
    }
}
