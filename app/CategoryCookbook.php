<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryCookbook extends Model
{
    protected $table = 'category_cookbook';

    public function cookbooks()
    {
        return $this->belongsToMany('App\Cookbook');
    }
}
