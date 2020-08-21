<?php

namespace App;

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
        'slug', 'calorie_count', 'cook_time'
    ];

    protected $casts = [
    	'cook_time' => 'datetime:H:i:s',
		'ingredients' => 'json',
//		'nutritional_detail' => 'json'
	];

//	protected $attributes = [
//		'nutritional_detail' => ["cal" => "0g", "fat" => "0g", "carbs" => "0g", "protein" => "0g"]
//	];

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
                ->to("api/v1/recipes/{$this->attributes['id']}")
        ];
    }

    /**
     * Unserialize Ingredients
     *
     * @param [] $recipe recipe
     *
     * @return mixed
     */
    public function getIngredientsAttribute($recipe)
    {
        return explode(", ", $recipe);
    }
}
