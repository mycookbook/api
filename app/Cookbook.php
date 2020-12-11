<?php

namespace App;

use Carbon\Carbon;
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
        'name', 'description', 'bookCoverImg', 'user_id', 'flag_id', 'slug',
    ];

    protected $hidden = ['user_id', 'pivot'];

    /**
     * Append links attribute.
     *
     * @var array
     */
    protected $appends = ['_links', 'recipes_count', 'categories', 'author'];

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
	 * Get the recipes count
	 * @return int
	 */
    public function getRecipesCountAttribute()
	{
		return count($this->recipes);
	}

	/**
	 * @return mixed
	 */
	public function getCategoriesAttribute()
	{
		return $this->categories()->get();
	}

	/**
	 * Set attribute created at
	 *
	 * @return string
	 */
	public function getCreatedAtAttribute(): string
	{
		$year = Carbon::parse($this->attributes['created_at'])->year;
		$month = Carbon::parse($this->attributes['created_at'])->month;

		return Carbon::createFromDate($year, $month)->format('F Y');
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
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function categories()
	{
		return $this->belongsToMany('App\Category', 'category_cookbook');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flag()
    {
        return $this->belongsTo('App\Flag');
    }

	/**
	 * original author
	 * @return mixed
	 */
    public function author()
	{
		return User::find($this->user_id);
	}

	/**
	 * @return mixed
	 */
	public function getAuthorAttribute()
	{
		return $this->author();
	}
}
