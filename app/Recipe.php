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
        'name', 'imgUrl', 'ingredients', 'description', 'user_id', 'cookbook_id'
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
     * Get all recipes
     *
     * @return mixed
     */
    public static function getRecipes()
    {
        $recipes = Recipe::get();
        return $recipes;
    }
}
