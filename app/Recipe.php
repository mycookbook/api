<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Recipe
 *
 * @package Cookbook
 */
class Recipe extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
		'imgUrl',
		'ingredients',
		'description',
		'user_id',
		'cookbook_id',
		'summary',
		'nutritional_detail',
        'slug',
		'calorie_count',
		'cook_time',
		'prep_time',
		'tags'
    ];

    protected $hidden = ['user_id'];

    protected $casts = [
    	'cook_time' => 'datetime:H:i:s',
		'prep_time' => 'datetime:H:i:s',
		'ingredients' => 'json',
	];

    protected $attributes = [
    	'servings' => 1,
		'prep_time' => '2020-01-01 00:00:00',
	];

    /**
     * A recipe belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A recipe belongs to a cookbook
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cookbook()
    {
        return $this->belongsTo('App\Cookbook');
    }

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function variations()
	{
		return $this->hasMany('App\RecipeVariation');
	}

    /**
     * Append custom attributes
     *
     * @var array
     */
    protected $appends = [
    	'total_time',
		'varieties_count',
		'_links',
		'author',
		'submission_date',
	];

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
                ->to("api/v1/recipes/{$this->attributes['slug']}")
        ];
    }

	/**
	 * Compute total time to prepare recipe
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getTotalTimeAttribute()
	{
		$cook_time = strtotime(Carbon::parse($this->attributes['cook_time']));
		$prep_time = strtotime(Carbon::parse($this->attributes['prep_time']));
		$total_time = $cook_time + $prep_time;
		$total_time = Carbon::createFromTimestamp($total_time);
		return $total_time;
//		return CarbonInterval::createFromFormat('H:i:s', $total_time)->forHumans();
	}

	/**
	 * Compute Recipe variations count
	 *
	 * @return int
	 */
	public function getVarietiesCountAttribute()
	{
		return $this->variations()->count();
	}

	/**
	 * Set Attribute Cook Time
	 *
	 * @return string
	 * @throws \Exception
	 */
    public function getCookTimeAttribute()
	{
		$dt = Carbon::parse($this->attributes['cook_time']);
		return $dt->diffForHumans();
	}

	/**
	 * Set Attribute prep Time
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getPrepTimeAttribute()
	{
		$dt = Carbon::parse($this->attributes['prep_time']);
		return $dt->diffForHumans();
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getSubmissionDateAttribute()
	{
		$dt = Carbon::parse($this->attributes['created_at']);
		return $dt->diffForHumans();
	}

	/**
	 * Recipe cookbook
	 *
	 * @return array
	 */
	public function getAuthorAttribute()
	{
		return $this->user()->get()->first();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function tags(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(RecipeTag::class);
	}
}
