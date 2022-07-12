<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'name', 'description', 'bookCoverImg', 'user_id', 'flag_id', 'slug', 'alt_text'
    ];

    protected $hidden = ['user_id', 'pivot'];

    /**
     * Append links attribute.
     *
     * @var array
     */
    protected $appends = ['_links', 'recipes_count', 'categories', 'author', 'contributors'];

    /**
     * Set attributes links
     *
     * @return array
     */
    public function getLinksAttribute(): array
	{
        return [
            'self' => app()
                ->make('url')
                ->to("api/v1/cookbooks/{$this->attributes['slug']}")
        ];
    }

	/**
	 * Get the recipes count
	 */
    public function getRecipesCountAttribute(): string
	{
		$count = count($this->recipes);

		if ($count >= 100 && $count < 1000) {
			return '100+ Recipes';
		}

		if ($count >= 1000 && $count < 1000000) {
			return '1K+ Recipes';
		}

		if ($count > 1000000) {
			return '1M+ Recipes';
		}

		return $count . " " . Str::pluralStudly('Recipe', $count);
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
    public function recipes(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
        return $this->hasMany('App\Recipe');
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
	{
		return $this->belongsToMany(Category::class);
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flag(): \Illuminate\Database\Eloquent\Relations\BelongsTo
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

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
		return $this->belongsToMany(User::class);
	}

    /**
     * @return array
     */
    public function getContributorsAttribute(): array
    {
        $contributor_ids = $this->recipes()->get()->pluck("user_id")->toArray();

        return array_values(array_unique($contributor_ids));
    }
}
