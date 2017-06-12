<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Recipe
 *
 * @package Cookbook
 */

class Cookbook extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description'
    ];

    /**
     * A user can subscribe to many cookbooks
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->hasMany('Cookbook\User');
    }
}
