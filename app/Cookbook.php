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
        'name', 'description', 'user_id'
    ];

    /**
     * A user can subscribe to many cookbooks
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsToMany('Cookbook\User');
    }

    /**
     * A cook has many recipes
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipes()
    {
        return $this->hasMany('Cookbook\Recipe');
    }
}
