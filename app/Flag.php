<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Recipe
 */
class Flag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'flag', 'nationality',
    ];

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
                ->to("api/v1/flags/{$this->attributes['id']}"),
        ];
    }

    /**
     * A cookbook has many cookbooks
     */
    public function cookbook()
    {
        return $this->belongsToMany('App\Cookbook');
    }
}
