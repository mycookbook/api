<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeVariation extends Model
{
    protected $table = 'recipe_variations';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'name', 'imgUrl', 'ingredients', 'description', 'summary', 'nutritional_detail', 'slug', 'calorie_count',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipe()
    {
        return $this->belongsTo('App\Models\Recipe');
    }
}
