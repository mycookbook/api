<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryCookbook extends Model
{
    protected $table = 'category_cookbook';

    public function cookbooks()
    {
        return $this->belongsToMany('App\Models\Cookbook');
    }
}
