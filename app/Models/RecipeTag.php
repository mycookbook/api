<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeTag extends Model
{
    /**
     * @var array<string>
     */
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
