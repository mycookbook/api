<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class BaseService
{
    /**
     * @var Model $serviceModel
     */
    protected Model $serviceModel;

    /**
     * @return string[]
     */
    protected function getFillables()
    {
        return $this->serviceModel->getFillable();
    }
}