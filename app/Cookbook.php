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
     * Append links attribute.
     *
     * @var array
     */
    protected $appends = ['_links'];

    /**
     * Set attributes links
     *
     * @return array
     */
    public function getLinksAttribute()
    {
        return [
            'self' => app()
                ->make('url')
                ->to("api/v1/cookbooks/{$this->attributes['id']}")
        ];
    }

    /**
     * A cookbook has many recipes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipes()
    {
        return $this->hasMany('App\Recipe');
    }

    /**
     * A cookbook belongs to one user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
