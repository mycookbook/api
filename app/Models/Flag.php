<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Recipe
 */
class Flag extends Model
{
    /**
     * @var array<string>
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
        return $this->belongsToMany('App\Models\Cookbook');
    }

    public function getAll()
    {
        return collect($this->all())->transform(function ($i) {
            return [
                'id' => $i->id,
                'code' => $i->flag,
                'country' => $i->nationality,
                'nationality' => $i->nationality
            ];
        });
    }
}
