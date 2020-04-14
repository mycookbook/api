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
        'name', 'description', 'bookCoverImg', 'user_id', 'flag', 'slug',
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
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flag()
    {
        return $this->belongsTo('App\Flag');
    }
}
