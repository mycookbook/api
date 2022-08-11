<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipeVariation extends Model
{
    protected $table = 'recipe_variations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'imgUrl', 'ingredients', 'description', 'summary', 'nutritional_detail', 'slug', 'calorie_count',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipe()
    {
        return $this->belongsTo('App\Recipe');
    }
}
