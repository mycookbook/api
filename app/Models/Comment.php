<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * @var array<string>
     */
    protected $fillable = [
        'user_id', 'recipe_id', 'comment',
    ];

    protected $appends = ['author'];

    /**
     * @return mixed
     */
    public function getAuthorAttribute()
    {
        $author_id = $this->user_id;

        return User::where(['id' => $author_id])->first();
    }

    /**
     * @return string
     */
    public function getCreatedAtAttribute(): string
    {
        return Carbon::create($this->attributes['created_at'])->diffForHumans();
    }
}
