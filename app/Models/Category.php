<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Recipe
 */
class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name', 'slug', 'color', 'emoji',
    ];

    protected $hidden = ['pivot'];

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
                ->to("api/v1/categories/{$this->attributes['id']}"),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cookbooks()
    {
        return $this->belongsToMany('App\Models\Cookbook', 'category_cookbook');
    }
}
