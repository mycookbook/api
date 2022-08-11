<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipeTag extends Model
{
    protected $fillable = ['name', 'recipe_id'];

    /**
     * Tag belongs to a Recipe
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipe(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
