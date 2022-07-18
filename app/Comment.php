<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id', 'recipe_id', 'comment'
    ];

    protected $appends = ['author'];

    /**
     * @return mixed
     */
    public function getAuthorAttribute()
    {
        $author_id = $this->user_id;

        return User::where(["id" => $author_id])->first();
    }
}