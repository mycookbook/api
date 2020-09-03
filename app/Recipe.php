<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonInterval;
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
        'name', 'imgUrl', 'ingredients', 'description', 'user_id', 'cookbook_id', 'summary', 'nutritional_detail',
        'slug', 'calorie_count', 'cook_time', 'prep_time'
    ];

    protected $casts = [
    	'cook_time' => 'datetime:H:i:s',
		'prep_time' => 'datetime:H:i:s',
		'ingredients' => 'json',
	];

    /**
     * A recipe belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
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
    protected $appends = ['total_time', '_links'];

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
                ->to("api/v1/recipes/{$this->attributes['id']}")
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
		return CarbonInterval::createFromFormat('H:i:s', $total_time->toTimeString())->forHumans();
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
		return CarbonInterval::createFromFormat('H:i:s', $dt->toTimeString())->forHumans();
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
		return CarbonInterval::createFromFormat('H:i:s', $dt->toTimeString())->forHumans();
	}

	/**
	 * Set the default value of servings if not given in the request
	 *
	 * @param integer $value
	 */
	public function setServingsAttribute($value)
	{
		$this->attributes['servings'] = ($value) ? $value : 1;
	}
}
